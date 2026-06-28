---
title: "Joys of cancelling a TBB task group"
date: 2026-06-28T14:42:10+03:00
tags: ['blender', 'code']
---

A Blender issue [#152467](https://projects.blender.org/blender/blender/issues/152467)
(*"File Browser thumbnail cache broken with large amount of images"*) reminded me
to write this up. This particular issue is a ([documented](https://uxlfoundation.github.io/oneTBB/main/tbb_userguide/Cancellation_and_Nested_Parallelism.html)) surprise that when you have a `parallel_for` in
[TBB](https://uxlfoundation.github.io/oneTBB/), some of the loop
iterations might not execute at all, if the task group gets cancelled.

Similar to C++ exceptions, the effect is "global" - something might throw an exception,
and now a completely unrelated part of your code needs to be aware of that possibility,
even if you don't want to. Same with task group cancellation -- you might write a `parallel_for`,
and assume that all the loop iterations will execute. That's what *all the code* within Blender
does, I think :) But! Because some caller of your code *way up above* might do a `task_group.cancel()`,
now *your* code needs to be prepared to handle that possibility.

Anyway, all that reminded me of another Blender bug that I was involved with some months ago,
which is *more curious*.

### The bug

The reported bug was [#143662](https://projects.blender.org/blender/blender/issues/143662):
- Blender crashes, while you have a file browser dialog open with "sufficient amount" of thumbnails,
- But only if some thumbnails were freshly generated (i.e. have not been cached previously),
- And only if you had rendered anything with the path-tracing Cycles renderer,
- And only if you have "Persistent Data" Cycles option on,
- The crash does not happen with Address Sanitizer being on.

So that's... *fun*.

Part of the possible cause was my change that added more multi-threading to parts of image
processing code, some of which gets executed during thumbnail generation. Which means I had
to investigate.

### What is wrong with this code?

Suppose you have a path-traced renderer (like Blender's Cycles) that as part of
scene initialization does something like this:
```c++
parallel_for_each(all_scene_geometries, build_geometry_bvh); // 1.
```
and each `Geometry` object contains something like:
```c++
struct Geometry {
    BVH bvh; // bounding volume hierarchy object backed by Embree library // 2.
    // ...
};
```

So far so good. This builds bounding volume hierarchies for all geometries,
in parallel. The `parallel_for_each` is implemented by TBB library, and the BVH data is backed
by Embree library. Both well known, production & battle-tested libraries.

Now, in a *completely unrelated* part of the application, like in a file dialog UI code,
you have an on-demand file thumbnail generation code. Thumbnails are cached to disk,
but if some of them are not cached, they are rebuilt in the background and saved. While
scrolling the dialog with potentially many thumbnails, some of the queued requests
might get no longer needed if the visible portion changes drastically. The exact logic
is somewhat complex, but essentially it has a queue of "thumbnail generation tasks",
and sometimes decides to cancel a whole group of pending tasks.

So there's code like this somewhere:
```c++
if (some_condition) {
    tbb_thumb_task_group->cancel(); // TBB cancel functionality // 3.
    // ...
}
```
and somewhere within each thumbnail generation job, there is potential image
buffer colorspace conversion or scaling code that has like a:
```c++
parallel_for(all_thumbnail_pixels, process_the_pixel); // 4.
```

Does all of that code make sense? You would think so! And yet it crashes, from innards of oneTBB,
when doing the `cancel()` call. But only sometimes. And only when Address Sanitizer is off.

And only if *all* of the 1, 2, 3, and 4 points above are present:
- Remove the parallel build of Cycles geometry BVH, i.e. build them sequentially? All good.
- Switch the Cycles BVH to something not backed by Embree? All good.
- Switch the Cycles to not persist scene data across frames/renders? All good.
- Stop doing cancel on the thumbnail task group? All good.
- Process thumbnail pixels sequentially instead of parallel? All good too.

🤯

### The crash cause

Turns out, there's nothing *particularly* wrong with the code above, it is *"just"* a surprising 
implementation detail of TBB task group cancellation, that is not intuitive at all.

It *maybe* makes sense if you would think really hard about "so, how would I *actually* implement
task cancellation with nested parallelism?", but most people do not think about this
question every day.

What happens is this:
1. Each `parallel_for` creates a "task group context" (TBB `task_group_context`)
  as an on-stack / local variable. Our `parallel_for_each(all_scene_geometries, build_geometry_bvh)`
  above has just created one.
1. Whenever something "uses" a task group context, TBB "binds" it (whatever that is).
  As part of this "binding", the context records the currently executing context
  as its parent ([task_group_context.cpp:118](https://github.com/uxlfoundation/oneTBB/blob/f48775ba4477a/src/tbb/task_group_context.cpp#L118)),
  and adds itself into a per-thread list of live contexts ([task_group_context.cpp:105](https://github.com/uxlfoundation/oneTBB/blob/f48775ba4477a/src/tbb/task_group_context.cpp#L105)).
  Turns out, Embree library also uses TBB internally, so building a BVH for a geometry
  does a bunch of `parallel_for` algorithms, and the TBB `task_group_context` objects
  get stored in our resulting BVH data. They all now point to the outer local context
  (created in previous step) as their "parent" though!
1. When using "Persistent Data" setting in Cycles, all our geometry BVH objects live
  for a long time; long after the initial "build all geometries in parallel" 
  function has finished. They all have `task_group_context` objects that point to now-random
  stack data. So far, however, this does not cause any problems...
1. Later, we get to our thumbnails UI code. This has a *completely unrelated* `task_group`,
  that we call `cancel()` on. TBB then does
  [cancellation propagation](https://uxlfoundation.github.io/oneTBB/main/tbb_userguide/Cancellation_and_Nested_Parallelism.html),
  and this walks a live-context list of every registered thread, and, for each context, walks its
  `my_parent` pointer chain up, looking for the context we are cancelling
  ([task_group_context.cpp:200](https://github.com/uxlfoundation/oneTBB/blob/f48775ba4477a/src/tbb/task_group_context.cpp#L200)).
  This is where we find the still-alive task group created in step 2, and try to walk its parent into
  long-invalid memory since it was pointing to on-stack task group created in step 1.
  Note that this only happens if the task group that is cancelled itself has nested parallelism.

😤

I have a completely standalone repro demonstrating the issue here: https://github.com/aras-p/test_tbb_cancel

### The lesson

**Stop cancelling TBB tasks**, I guess?

By now we know at least two things:
- In presence of task cancellation, parallel loops might not execute all of their iterations. And your code might need
  to be prepared to deal with that, even if you do not cancel any tasks! Someone way above in the call stack might.
- Long-lived task group contexts (as used by Embree) might record a surprise pointer to their caller
  parallel-for construct data, that can get stale. That pointer is only used when doing task cancellation. Even if
  cancellation happens on an entirely unrelated task group, in entirely unrelated code!

Luckily for Blender, it seems that task cancellation was used only in three places of the code. At least two of them
used it for no good reason; I guess the authors saw *"oh I can cancel a task group, sounds convenient"* and used that.
So another lesson would be, either do not expose task group cancellation functionality, or make the function sound
much more scary than just `cancel()`, and have a giant comment warning the users of
*thing that lumbers slobberingly*
into sight and gropingly squeezes its
geͣ͌͞l͓̻̥̓̿͐͐ă̜͆͜t̤ͥ͠i͕͚ͧͅṇ̽ͯ̀o̟u͚̯ͬ͐́͠s̷̜ͬ̓̏͆ g̴̼̹̼͊̅̓ŗ̶̝̟͌e̗͉̭ͤen͋ i̙̤̖͂͡_̷mme̷͈̺͙̓͞ͅn̥̾͐̃̒͊s̭iͩ̋ty̢͈ t̸̴̬͒͡ḥͬͧr̢̛ö̕u͒ğͯh̛͓̠̣̑ͣ͗ ṭ̂͂̑hͩë͕̻̙̐
b͋la̳̭̓c̤̓̓ͨk͖̠͉̽̃̚͠ d̘ͩoo̬̦͆ͣ̐͢r̻ͫͫ͛̚ẇ̧͕a̶̻͓͉y̡̬͍͊̏̓̂ i̠͑̈́n̘̪̋̓͛ͨt̨͓ͥ̋̀ͨ́o̡͆ͫ̿ t͓͆͛͌ͭͥhẽ͓ t͂ͧ̈ͮ́a̳̋̊ͣ̽ͭ͘ĩ͊n̶̿̓̏tͨͩe͓͋̓͡d͟ o̪̮̊̊u͑t̷̯̞̿̀͆͟šid̴͇͍̆̆͞ę̦͒͐͆
aͬ̍͆̊͋i̢͇̥̹̞̺ͥ̊͗ͫȓ̝̥̰̳͂̈́̚͢͡ ǫ̛̲̙̓́̀̇̈ͪ̕f̵̨͉ͥ͝ t͛̊ͨ̍h̪̪͋a̶̬͈͔̲̒̈́t p̡̨̗̗ͦͧ̾̎̚͠oͦ̍i̳̬͕̙̚s̥̜̗͑͜on̸̸̤̮ͨ̉͌̃ͫ c̢̡͇̳̲̻͐ͧȉ͕̔_t̯͙͉̑͟͞y ȏ̴̹̟̙ͭͧ͗f͕̠͆̎͐ m̪͈̞͙ͥ̿̽̅a̹͐ͫͭ̈́ͪ̒́d̸̶̞̣͔̤̟͚̼̔n̝̋e̫̩ͪ̎ͭ͟͡ss̴.

We will just remove usages of it: [PR 160714](https://projects.blender.org/blender/blender/pulls/160714),
[PR 160711](https://projects.blender.org/blender/blender/pulls/160711) and [PR 160709](https://projects.blender.org/blender/blender/pulls/160709),
and then un-expose TBB task group cancellation functionality from the rest of Blender codebase.
