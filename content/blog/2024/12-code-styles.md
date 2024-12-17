---
title: "Verbosity of coding styles"
date: 2024-12-17T14:30:10+03:00
tags: ['blender', 'code', 'rant']
---

Everyone knows that different code styles have different verbosity. You can have very dense
code that implements a path tracer in [99 lines of C](https://www.kevinbeason.com/smallpt/),
or on the back of a business card ([one](https://www.realtimerendering.com/blog/back-of-the-business-card-ray-tracers/),
[two](https://mzucker.github.io/2016/08/03/miniray.html)). On the other side of the spectrum,
you can have very elaborate code where it can take you *weeks* to figure out *where does the actual work happen*,
digging through all the abstraction layers and indirections.

Of course to be usable in a real world project, code style would preferably not sit on either
extreme. How compact vs how verbose it should be? That, as always, depends on a lot of factors.
How many people, and of what skill level, will work on the code? How much churn the code will have?
Will it need to keep on adapting to wildly changing requirements very fast? Should 3rd parties
be able to extend the code? Does it have public API that can never change? And a million
of other things that all influence how to structure it all, how much abstraction (and of what kind)
should there be, etc.

#### A concrete example: Compositor in Blender

The other day I was happily [deleting 40 thousand lines of code](https://projects.blender.org/blender/blender/pulls/131819)
*(just another regular Thursday, eh)*, and I thought I'd check how much code is in the "new"
[Compositor](https://docs.blender.org/manual/en/4.3/compositing/introduction.html)
in Blender, vs in the old one that I was removing.

What is the "old" and "new" compositor? Well, there have been more than just these two. You see,
some months ago I removed the "old-old" ("tiled") compositor already. There's a good talk by Habib Gahbiche
"[**Redesigning the compositor**](https://www.youtube.com/watch?v=rAY64L_U2V8)" from BCON'24 with all
the history of the compositor backends over the years.

So, how large is the compositor backend code in Blender?

> I am using [**scc**](https://github.com/boyter/scc) to count the number of lines. It is pretty good! And counts
> the 4.3 million lines inside Blender codebase in about one second, which is way faster than some other
> line counting tools ([tokei](https://github.com/XAMPPRocky/tokei) is reportedly
> also fast and good). I am using `scc --count-as glsl:GLSL` since right now `scc` does not recognize
> `.glsl` files as being GLSL, [d'oh](https://github.com/boyter/scc/pull/566).

The "Tiled" compositor I removed a while ago ([PR](https://projects.blender.org/blender/blender/pulls/118819))
was 20 thousand lines of code. Note however that this was just one "execution mode" of the compositor,
and not the full backend.

The "Full-frame" compositor I deleted just now ([PR](https://projects.blender.org/blender/blender/pulls/131819)) is
40 thousand lines of C++ code.

What remains is the "new" (used to be called "realtime") compositor. How large is it? Turns out it is... 27 thousand
lines of code. So it is way smaller! <br/>
[{{<img src="/img/blog/2024/compositor-code-sizes.png" width="70%">}}](/img/blog/2024/compositor-code-sizes.png)

And here's the kicker: while the previous
backends were CPU only, this one works on **both CPU and GPU**. With no magic, just literally "write the processing
code twice: in C++ and GLSL". "*Oh no, code duplication!*"... and yet... it is *way more compact*. Nice!

I know nothing about compositing, or about relative merits of "old" vs "new" compositor code. It is entirely
possible that the verbosity of the old compositor backend was due to a design that, in retrospect, did not stand
the test of time or production usage -- afterall compositor within Blender is a 12 year old feature by now.
Also, while I deleted the old code because I like deleting code, the actual hard work of writing the new
code was done mostly by Omar Emara, Habib Gahbiche and others.

I found it interesting that the new code that does *more things* is much smaller than the old code, and that's all!
