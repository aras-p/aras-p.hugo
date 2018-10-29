---
title: "Pathtracer 16: Burst SIMD Optimization"
date: 2018-10-29T20:41:10+03:00
tags: ['rendering', 'code', 'unity']
comments: true
---

*Introduction and index of this series [is here](/blog/2018/03/28/Daily-Pathtracer-Part-0-Intro/)*.

When I originally played with the [Unity Burst](https://unity3d.com/unity/features/job-system-ECS) compiler
in ["Part 3: C#, Unity, Burst"](/blog/2018/03/28/Daily-Pathtracer-Part-3-CSharp-Unity-Burst/), I just
did the simplest possible *"get C# working, get it working on Burst"* thing and left it there. Later on in
["Part 10: Update C#"](/blog/2018/04/16/Daily-Pathtracer-10-Update-CsharpGPU/) I updated it to use Structure-of-Arrays
data layout for scene objects, and that was about it. Let's do something about this.

> Meanwhile, I have switched from late-2013 MacBookPro to mid-2018 one, so the performance
> numbers on a "Mac" will be different from the ones in previous posts.

#### Update to latest Unity + Burst + Mathematics versions

First of all, let's update the Unity version we use from some random 2018.1 beta to the latest stable 2018.2.13,
and update Burst (to `0.2.4-preview.34`) & Mathematics (to `0.0.12-preview.19`) packages along the way.
Mathematics renamed `lengthSquared` to `lengthsq`,
and introduced a `PI` constant that clashed with our own one :) These trivial updates in
[this commit](https://github.com/aras-p/ToyPathTracer/commit/467573c9a99).

Just that got performance on PC from 81.4 to 84.3 Mray/s, and on Mac from 31.5 to 36.5 Mray/s. I guess
either Burst or Mathematics (or both) got some optimizations during this half a year, nice!

#### Add some "manual SIMD" to sphere intersection

Very similar to how in [Part 8: SSE HitSpheres](/blog/2018/04/11/Daily-Pathtracer-8-SSE-HitSpheres/)
I made the C++ `HitSpheres` function do intersection testing of one ray against 4 spheres at once, we'll do the
same in our Unity C# Burst code.

The thought process and work done is *extremely* similar to the C++ side done in
[Part 8](/blog/2018/04/11/Daily-Pathtracer-8-SSE-HitSpheres/) and [Part 9](/blog/2018/04/13/Daily-Pathtracer-9-A-wild-ryg-appears/);
basically:

* Since data for our spheres is laid out nicely in SoA style arrays, we can easily load data for 4 of them at once.
* Do all ray intersection math on these 4 spheres,
* If any are hit, pick the closest one and calculate final hit position & normal.

`HitSpheres` function code gets to be extremely similar between
[C++ version](https://github.com/aras-p/ToyPathTracer/blob/16-burst-simd/Cpp/Source/Maths.cpp#L89) and
[C# version](https://github.com/aras-p/ToyPathTracer/blob/16-burst-simd/Unity/Assets/Maths.cs#L183).
In fact the C# one is cleaner since `float4`, `int4` and `bool4` types in Mathematics package are way more complete
SIMD wrappers than my toy manual implementations in the C++ version.

The full change commit [is here](https://github.com/aras-p/ToyPathTracer/commit/7554871b35).

Performance: PC from 84.3 to **133 Mray/s**, and Mac from 35.5 to **60.0 Mray/s**. Not bad!


#### Updated numbers for new Mac hardware

 Implementation              | PC   | Mac
-----------------------------|------|-----
GPU                          | 1854 | 246
C++, SSE+SoA HitSpheres      | 187  | 74
C#, Unity Burst, 4-wide HitSpheres | 133 | 60
C++, SoA HitSpheres          | 100  | 36
C#, Unity Burst              | 82 | 36
C#, .NET Core                | 53.0 | 23.6
C#, mono `-O=float32 --llvm` w/ `MONO_INLINELIMIT=100` |      | 22.0
C#, mono `-O=float32 --llvm` |      | 18.9
C#, mono `-O=float32`        |      | 11.0
C#, mono                     |      | 6.1

* PC is AMD ThreadRipper 1950X (3.4GHz, 16c/16t - SMT disabled) with GeForce GTX 1080 Ti.
* Mac is mid-2018 MacBookPro (Core i9-8950HK 2.9GHz, 6c/12t) with AMD Radeon Pro 560X.
* Unity version 2018.2.13 with Burst `0.2.4-preview.34` and Mathematics `0.0.12-preview.19`.
* Mono version 5.12.
* .NET Core version 2.1.302.

All code is on [github at `16-burst-simd` tag](https://github.com/aras-p/ToyPathTracer/tree/16-burst-simd).



