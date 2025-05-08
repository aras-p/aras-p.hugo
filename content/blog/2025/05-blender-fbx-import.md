---
title: "Blender FBX importer via ufbx"
date: 2025-05-08T13:18:10+03:00
tags: ['blender', 'code', 'performance']
comments: true
---

Three years ago I found myself [speeding up Blender OBJ importer](/blog/2022/05/12/speeding-up-blender-obj-import/),
and this time I am rewriting Blender FBX importer. Or, letting
someone else take care of the *actually complex* parts of it.

*TL;DR: Blender 4.5 will have a [new FBX importer](https://projects.blender.org/blender/blender/pulls/132406).*

### FBX in Blender

[**FBX**](https://en.wikipedia.org/wiki/FBX), a 3D and animation interchange format owned by
~~Kaydara~~ ~~Alias~~ *Autodesk*, is a proprietary format that is still quite popular in some spaces.
The file format itself is quite good actually; the largest
downsides of it are: 1) it is closed and with no public spec, and 2) due to it being very flexible, various
software represent their data in funny and sometimes incompatible ways. The future of the format
seems to be "dead" at this point; after a decade of continued improvements to the FBX format and the SDK,
Autodesk seems to have stopped around year 2020. However, the two big game engines (Unity and Unreal) still
both treat FBX as the primary format in how 3D data gets into the engine. But going forward, perhaps one should
use [USD](https://en.wikipedia.org/wiki/Universal_Scene_Description),
[glTF](https://en.wikipedia.org/wiki/GlTF) or [Alembic](https://en.wikipedia.org/wiki/Alembic_(computer_graphics)). 

Blender, by design / out of principle only uses open source libraries for everything that ships inside of it.
Which means it can not use the official (closed source, binary only) [Autodesk FBX SDK](https://aps.autodesk.com/developer/overview/fbx-sdk),
and instead had to reverse engineer the format and write their own code to do import/export. And so they did!
They added FBX export in 2.44 (year 2007), and import in 2.69 (year 2013), and have a short reverse engineered
FBX format description on the [developer blog](https://code.blender.org/2013/08/fbx-binary-file-format-specification/).

The FBX import/export functionality, as was common within Blender at the time, was written in pure Python. Which
is great for the expressiveness and makes for very compact code, but is not that great for many other reasons. However,
it has been expanded, fixed and optimized over the years -- recent versions use NumPy for many heavy number
crunching parts, the exporter does some multi-threaded Deflate compression, and so on. The whole
implementation is about 12 thousand lines of Python code, which is very compact, given that it does import, export
and all the Blender parts too (that comes at a cost though... in some places it feels *too compact*, when someone
else wants to understand what the code is doing :)).

*So far so good! However, [ufbx](https://github.com/ufbx/ufbx) open source library was born sometime in 2019.*

### ufbx, and other FBX parsers

**ufbx** ([github](https://github.com/ufbx/ufbx), [website](https://ufbx.github.io/)) by Samuli Raivio is a single source file
C library for loading FBX files.

And holy potatoes, it is an excellent library. Seriously: ufbx *is one of the best written libraries I've ever seen*.

- Compiles out of the box with no configuration needed. You *can* configure it very extensively, if you want to:
  - Disable parts of library you don't need (e.g. subdivision surface evaluation, animation layer blending evaluation, etc.).
  - Pass your own memory allocation functions, your own job scheduler, even your own C runtime functions.
  - Disable data validation, disable loading of certain kinds of data, and so on.
- The API and the data structures exposed by it just *make sense*. This is highly subjective, but I found it very
  easy to use and find my way around.
- It is very extensively tested at multiple levels.

And also, it is *very fast*. I actually wanted to compare ufbx with the official FBX SDK, and several other open source
FBX parsing libraries I managed to find ([AssImp](https://github.com/assimp/assimp), [OpenFBX](https://github.com/nem0/OpenFBX)).

Here's time it takes (in seconds, on Ryzen 5950X / Windows / VS2022) to read 9 FBX files (total size 2GB), and extract very
basic information about the scene (how many vertices in total, etc.). There is sequential time (read files one by one),
and parallel time (read files in parallel, independently), as well size of the executable that does all that.

| Parser                   | Time sequential, s | Time parallel, s | Executable size, KB |
|--------------------------|------:|-------:|-----:|
| ufbx                     |   9.8 |    2.7 |  457 |
| ufbx w/ internal threads |   4.4 |    2.6 |  462 |
| FBX SDK                  | 869.9 | crash! | 4508 |
| AssImp                   |  33.9 |   26.7 | 1060 |
| OpenFBX                  |  26.7 |   15.8 |  312 |

Or, in more visual form: <br/>
[{{<img src="/img/blog/2025/fbx-parser-times.png">}}](/img/blog/2025/fbx-parser-times)

Does performance of the official FBX SDK look very bad here? Yes indeed it does. This seems to be due to two reasons:
- It can not parse several FBX files in parallel. It just can't due to shared global data of some sorts.
- On *some files* (mostly the ones that have *lots* of animation curves, or *lots* of instancing), it is *very slow*. Not to parse
  them! But to clean up after you are done with parsing. Looks like even if you want to tell it "yeet everything", it proceeds
  to do that one entity at a time, doing a lot of work making sure that after removing each individual little shit, it is properly
  de-registered from any other things that were referencing it. Probably effectively a quadratic complexity amount of work.

I have put source code and more details of these tests over at [github.com/aras-p/test-fbx-parsers](https://github.com/aras-p/test-fbx-parsers)

Anyway, if you need to load/parse FBX files, just use ufbx!

### Blender 4.5: new FBX importer

So, I made a new FBX importer for Blender 4.5 ([pull request](https://projects.blender.org/blender/blender/pulls/132406)). So far it is
marked "experimental", and for a while both the new one and the Python-based one will co-exist. Until the new one is sufficiently
tested and the old one can be removed.

The new importer comes with several advantages, like:
- It supports ASCII FBX files, as well as binary FBX files that are older than FBX 7.1.
- Better handling of "geometric transform" (common in 3dsmax), support for more types of Material shading models, imported animations
  are better organized into Actions/Slots.
- So far I have found about 20 existing open bug reports, where current FBX importer was importing something wrong, and the new one
  does the correct thing.
  - Right now there is one bug report about the new importer doing *incorrect* thing... looking into it! :)

Oh, and it is quite a bit faster too. While the Python based importer was *quite fast* (for Python, that is), the new one is often 5x-20x
faster, while using less memory too. Here are some tests, import times in seconds (Ryzen 5950X):

| | Test case | Notes | Time Python | Time new |
|---|---|---|---:|----:|
|[{{<img src="/img/blog/2025/fbx-splash3.png" width="200px">}}](/img/blog/2025/fbx-splash3.png) | Blender 3.0 Splash | 30k instances | 83.4 | 4.7 |
|[{{<img src="/img/blog/2025/fbx-rain.png" width="200px">}}](/img/blog/2025/fbx-rain.png) | Rain Restaurant | 300k animation curves | 86.8 | 4.4 |
|[{{<img src="/img/blog/2025/fbx-zeroday.png" width="200px">}}](/img/blog/2025/fbx-zeroday.png) | Zero Day | 6M triangles, 8K objects | 22.1 | 1.7 |
|[{{<img src="/img/blog/2025/fbx-caldera.png" width="200px">}}](/img/blog/2025/fbx-caldera.png) | Caldera | 21M triangles, 6K objects | 44.2 | 4.4 |

Even if it is ufbx that takes care of all the *actually complex* parts of the work, I still managed to ~~waste~~ *spend* quite a lot of time on this.

However, most of the time I spent procrastinating, in the style of "*oh no, now I will have to do materials, this is going to be complex*"
-- proceed to find excuses to not do it just yet -- eventually do it, and turns out it was not as scary. Besides stalling this way, most of the other
time sink has been just learning innards of Blender (the whole area of Armatures, Bones, EditBones, bPoseChannels is *quite something*).

The amount of code for just the Blender importer side (i.e. not counting ufbx itself) still ended up at 3000 lines, which is not compact at all.

Anyway, now what remains is to fix the open issues (so far... just one!), do a bunch of improvements I have in mind, and ship this to the world in
Blender 4.5. You can try out [daily builds](https://builder.blender.org/download/daily/) yourself!

And then... maybe a new FBX *exporter*? We will see.

