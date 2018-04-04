---
title: "Daily Pathtracer Part 6: D3D11 GPU"
date: 2018-04-04T19:40:50+03:00
tags: ['rendering', 'code', 'gpu', 'd3d']
comments: true
---

*Introduction and index of this series [is here](/blog/2018/03/28/Daily-Pathtracer-Part-0-Intro/)*.

In the [previous post](/blog/2018/04/03/Daily-Pathtracer-Part-5-Metal-GPU/), I did a naïve Metal
GPU "port" of the path tracer. Let's make a Direct3D 11 / HLSL version now.

* This will allow testing performance of this "totally not suitable for GPU" port on a desktop GPU.
* HLSL is familiar to more people than Metal.
* Maybe someday I'd put this into a Unity version, and having HLSL is useful, since Unity
  [uses HLSL](https://docs.unity3d.com/Manual/SL-ShadingLanguage.html) as the shading language.
* Why not D3D12 or Vulkan? Because those things are too hard for me ;) Maybe someday, but not just yet.


### Ok let's do the HLSL port

The final [**change is here**](https://github.com/aras-p/ToyPathTracer/pull/6/files), below are just some
notes:

* Almost everything from [Metal post](/blog/2018/04/03/Daily-Pathtracer-Part-5-Metal-GPU/) actually applies.
* Compare [Metal shader](https://github.com/aras-p/ToyPathTracer/pull/5/files#diff-9f78fb585687669f7c1668e544a97325R34)
  with [HLSL one](https://github.com/aras-p/ToyPathTracer/pull/6/files#diff-da6a05a7f02e84722833a64df8f8d263):
  * Metal is "more C++"-like: there are references and pointers (as opposed to `inout` and `out` HLSL
    alternatives), structs with member functions, enums etc.
  * Overall most of the code is very similar; largest difference is that I used
    [global variables](https://github.com/aras-p/ToyPathTracer/pull/6/files#diff-da6a05a7f02e84722833a64df8f8d263R347)
    for shader inputs in HLSL, whereas Metal requires [function arguments](https://github.com/aras-p/ToyPathTracer/pull/5/files#diff-9f78fb585687669f7c1668e544a97325R371).
* I used [StructuredBuffers](https://msdn.microsoft.com/en-us/library/windows/desktop/ff476335.aspx#Structured_Buffer)
  to pass data from the application side, so that it's easy to match data layout on C++ side.
  * On AMD or Intel GPUs, my understanding is that there's no *big* difference between structured
    buffers and other types of buffers.
  * However NVIDIA seems to quite like constant buffers for some usage patterns (see their blog posts:
    [Structured Buffer Performance](https://developer.nvidia.com/content/understanding-structured-buffer-performance),
    [Latency in Structured Buffers](https://developer.nvidia.com/content/redundancy-and-latency-structured-buffer-use),
    [Constant Buffers](https://developer.nvidia.com/content/how-about-constant-buffers)). If I were
    optimizing for GPU performance *(which I am not, yet)*, that's one possible area to look into.
* For reading GPU times, I just do the simplest possible [timer query](http://reedbeta.com/blog/gpu-profiling-101/)
  approach, without any double buffering or anything (see
  [code](https://github.com/aras-p/ToyPathTracer/pull/6/files#diff-03713fb07bf0346cad8ba83aa2f8b4ceR361)). Yes,
  this does kill any CPU/GPU parallelism, but here I don't care about that. Likewise, for reading
  back traced ray counter I read it immediately without any frame delays or async readbacks.
  * I did run into an issue where even when I get the results from the "whole frame" disjoint timer
    query, the individual timestamp queries still don't have their data yet (this was on AMD GPU/driver).
    So initially I had "everything works" on NVIDIA, but "returns nonsensical GPU times" on AMD.
    Testing on different GPUs is still useful, yo!


### What's the performance?

Again... this is *definitely not* an efficient implementation for the GPU. But here are the numbers!

* GeForce GTX 1080 Ti: **2780 Mray/s**,
* Radeon Pro WX 9100: **3700 Mray/s**,
* An old Radeon HD 7700: **417 Mray/s**,
* C++ CPU implementation, on this AMD Threadripper with SMT off: 135 Mray/s.

For reference, Mac Metal numbers:

* Radeon Pro 580: 1650 Mray/s,
* Intel Iris Pro: 191 Mray/s,
* GeForce GT 750M: 146 Mray/s.

What can we learn from that?

* Similar to Mac C++ vs GPU Metal speedups, here the speedup is also between 4 and 27 **times** faster.
  * And again, not a fair comparison to a "real" path tracer; this one doesn't have *any* BVH to traverse etc.
* The Radeon here handily beats the GeForce. On paper it has slightly more TFLOPS, and I suspect
  some other differences might be at play (structured buffers? GCN architecture being better at
  "bad for GPU, port from C++" type of code? I haven't investigated yet).

So there! The code is at `06-gpud3d11` [tag on github repo](https://github.com/aras-p/ToyPathTracer/tree/06-gpud3d11).


### What's next

I don't know. Have several possible things, will do one of them. Also, geez, doing these posts
every day is hard. Maybe I'll take a couple days off :)
