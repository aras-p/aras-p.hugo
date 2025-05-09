---
title: "Daily Pathtracer Part 0: Intro"
date: 2018-03-28T14:24:10+03:00
tags: ['rendering', 'code']
comments: true
---

As [mentioned before](/blog/2018/03/21/Random-Thoughts-on-Raytracing/), I realized I've never done
a path tracer. Given that I suggest everyone else who asks "how should I graphics" start with one,
this sounded wrong. So I started making a super-simple one. When I say *super simple*, I mean it!
It's not useful for anything, think of it as [smallpt] with more lines of code :)

However I do want to make one in C++, in C#, in something else perhaps, and also run into various
LOLs along the way. All code is at [github.com/aras-p/ToyPathTracer](https://github.com/aras-p/ToyPathTracer).

[{{<img src="/img/blog/2018/rt-pathtracer.png" width="350px">}}](/img/blog/2018/rt-pathtracer.png)
[{{<img src="/img/blog/2018/rt-simd-bigscene.jpg" width="330px">}}](/img/blog/2018/rt-simd-bigscene.jpg)

Now, all that said. *Sometimes* it can be useful (or at least fun) to see someone who's clueless
in the area going through parts of it, bumping into things, and going into dead ends or wrong approaches.
This is what I shall do in this blog series! Let's see where this *path* will lead us.

* [Part 1: Initial C++](/blog/2018/03/28/Daily-Pathtracer-Part-1-Initial-C-/) implementation and walkthrough.
* [Part 2: Fix stupid performance issue](/blog/2018/03/28/Daily-Pathtracer-Part-2-Fix-Stupid/).
* [Part 3: C#, Unity, Burst](/blog/2018/03/28/Daily-Pathtracer-Part-3-CSharp-Unity-Burst/).
* [Part 4: Fixing wrong rendering, and Mitsuba](/blog/2018/03/31/Daily-Pathtracer-Part-4-Fixes-Mitsuba/).
* [Part 5: GPU implementation for Metal](/blog/2018/04/03/Daily-Pathtracer-Part-5-Metal-GPU/).
* [Part 6: GPU implementation for D3D11/HLSL](/blog/2018/04/04/Daily-Pathtracer-Part-6-D3D11-GPU/).
* [Part 7: Initial C++ SIMD & SoA attempt](/blog/2018/04/10/Daily-Pathtracer-Part-7-Initial-SIMD/).
* [Part 8: SIMD SSE HitSpheres, and a larger scene](/blog/2018/04/11/Daily-Pathtracer-8-SSE-HitSpheres/).
* [Part 9: A wild ryg appears](/blog/2018/04/13/Daily-Pathtracer-9-A-wild-ryg-appears/) and optimizes my code!
* [Part 10: Let's unify all implementations](/blog/2018/04/16/Daily-Pathtracer-10-Update-CsharpGPU/) so
  that they render the same scene again etc.
* [Part 11: Attempt at a buffer-oriented approach](/blog/2018/04/19/Daily-Pathtracer-11-Buffer-Oriented/).
* [Part 12: Buffer-oriented approach on the GPU](/blog/2018/04/25/Daily-Pathtracer-12-GPU-Buffer-Oriented-D3D11/).
* [Part 13: GPU performance boost via thread group data caching](/blog/2018/05/28/Pathtracer-13-GPU-threadgroup-memory-is-useful/).
* [Part 14: Let's make it run on iOS](/blog/2018/05/30/Pathtracer-14-iOS/).
* [Part 15: Pausing it all, and a bunch of links](/blog/2018/08/01/Pathtracer-15-Pause-Links/).
* [Part 16: Update Unity Burst code with 4-wide HitSpheres](/blog/2018/10/29/Pathtracer-16-Burst-SIMD-Optimization/).
* [Part 17: WebAssembly version](/blog/2018/11/16/Pathtracer-17-WebAssembly/).

### Actually useful resources

If you want to *actually* learn someting about path tracing or raytracing, I'd suggest these:

* "[Physically Based Rendering: From Theory to Implementation](http://www.pbrt.org/)" ([free online version](http://www.pbr-book.org/)) by Pharr,
  Jakob, Humphreys. It's excellent, and explains pretty much *everything*.
* For a much lighter introduction, "[Ray Tracing in One Weekend](http://in1weekend.blogspot.lt/2016/01/ray-tracing-in-one-weekend.html)"
  and two follow-up minibooks by Shirley are really good (and dirt cheap!).
* "[The Graphics Codex](http://graphicscodex.com/)" app by McGuire is great.
* Presentation [slides on smallpt](http://www.kevinbeason.com/smallpt/#moreinfo) are a good intro too.
