---
title: "Daily Pathtracer 10: Update C#&GPU"
url: "/blog/2018/04/16/Daily-Pathtracer-10-Update-CsharpGPU/"
date: 2018-04-16T21:40:20+03:00
tags: ['rendering', 'code', 'gpu']
comments: true
---

*Introduction and index of this series [is here](/blog/2018/03/28/Daily-Pathtracer-Part-0-Intro/)*.

Short post; nothing new. Just wanted to update C#, Unity (C#+Burst) and GPU implementations
with the [larger scene](/blog/2018/04/11/Daily-Pathtracer-8-SSE-HitSpheres/) and
[optimizations](/blog/2018/04/13/Daily-Pathtracer-9-A-wild-ryg-appears/) from previous blog posts.
So that there's some, *ahem*, ***unity*** between them again. Here they are, as github commits/PRs:

* [Update C#](https://github.com/aras-p/ToyPathTracer/commit/5895cb3d3c8919c715139539f24262c549607569)
* [Update Unity/C#/Burst](https://github.com/aras-p/ToyPathTracer/commit/577d3758991561057366280d69b0989c8667a1d6)
* [Update GPU (D3D11&Metal)](https://github.com/aras-p/ToyPathTracer/commit/2baf9a339acbde0812a77a3f86b9172a0bf655d6)
* [Switch C# to larger scene](https://github.com/aras-p/ToyPathTracer/commit/60f2c64598b305daa4817fd5b491e6ae8c1ec68d)
* [Add "regular .NET 4.6" project to C#](https://github.com/aras-p/ToyPathTracer/commit/b15ece4442070182abaf06aec653aefab046fd1e)
* [@EgorBo submitted some tweaks to C# implementation](https://github.com/aras-p/ToyPathTracer/pull/8)

**A note on C# Mono performance**

As Miguel de Icaza [noted on github](https://github.com/aras-p/ToyPathTracer/issues/3#issuecomment-380238073)
and [**wrote on his blog in-depth**](http://tirania.org/blog/archive/2018/Apr-11.html), defaults in current Mono
version (5.8/5.10) are not tuned for the best floating point performance. Read his blog for details;
*much* better defaults should be shipping in later Mono versions! *If nothing else, maybe this toy project
will have been useful to gently nudge Mono into improving the defaults :)*


### Current performance numbers

 Implementation              | PC   | Mac
-----------------------------|------|-----
GPU                          | 778  | 53.0
C++, SSE+SoA HitSpheres      | 187  | 41.8
C++, SoA HitSpheres          | 100  | 19.6
C#, Unity Burst              | 82.3 | 18.7
C#, .NET Core                | 53.0 | 13.1
C#, mono `-O=float32 --llvm` w/ `MONO_INLINELIMIT=100` |      | 12.7
C#, mono `-O=float32 --llvm` |      | 10.5
C#, mono `-O=float32`        |      | 6.0
C#, mono                     |      | 5.5

* PC is AMD ThreadRipper 1950X (3.4GHz, 16c/16t) with GeForce GTX 1080 Ti.
* Mac is late-2013 MacBookPro (Core i7-4850HQ 2.3GHz, 4c/8t) with Intel Iris Pro.
* Unity version 2018.1 beta 12 with Burst 0.2.3.
* Mono version 5.8.1.
* .NET Core version 2.1.4.

All code is on [github at `10-impl-updates` tag](https://github.com/aras-p/ToyPathTracer/tree/10-impl-updates).


### What's next

I want to switch from a recursion/iteration oriented path tracer setup, into a stream/buffers oriented one, and
see what happens. Just because! My blog, my rules :)
