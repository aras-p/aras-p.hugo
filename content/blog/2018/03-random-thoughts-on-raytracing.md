---
title: "Random Thoughts on Raytracing"
date: 2018-03-21T07:59:54+02:00
tags: ['d3d', 'gpu', 'rant']
comments: true
---
Big graphics news at this GDC seem to be
[DirectX Raytracing](https://blogs.msdn.microsoft.com/directx/2018/03/19/announcing-microsoft-directx-raytracing/).
Here's some incoherent _(ha!)_ thoughts about it.

#### Raytracing: Yay

"Traditional" rasterized graphics is hard. When entire books are written on how to deal with shadows, and some of
the aliasing/efficiency problems are _still_ unsolved, it would be nice to throw something as _elegant_ as a raytracer
at it. Or screen-space reflections, another "kinda works, but zomg piles upon piles of special cases, tweaks and fallbacks"
area.

There's a reason why movie industry over the last 10 years has almost exclusively moved into path tracing renderers.
Even at Pixar's Renderman -- where their [Reyes](https://en.wikipedia.org/wiki/Reyes_rendering) was in fact an acronym for
"Renders Everything You Ever Saw" -- they switched to full path tracing in 2013 (for
[Monsters University](https://en.wikipedia.org/wiki/Monsters_University)), and completely removed Reyes from Renderman in
[2016](http://www.cgchannel.com/2016/07/pixar-releases-renderman-21/).


#### DirectX Raytracing

Mixed thoughts about having raytracing in DirectX as it is now.

A quick glance at the API overall seems to make sense. You get different sorts of ray shaders, acceleration structures,
tables to index resources, zero-cost interop with "the rest" of graphics or compute, etc etc.

> Conceptually it's not _that_ much different from what Imagination Tech has been trying to do for many years with
> OpenRL & [Wizard](https://www.imgtec.com/blog/tag/wizard/) chips. Poor Imgtec, either inventing so much or being so ahead
> of it's time, and failing to capitalize on that in a fair way. Capitalism is hard, yo :| Fun fact: [Wizard GPU
> pages](https://www.imgtec.com/legacy-gpu-cores/ray-tracing/) are under "Legacy GPU Cores" section of their website now...

On the other hand, as [Intern Department](https://twitter.com/InternDept/status/975802984025247745) quipped, DirectX has
a long history of "revolutionary" features that turned out to be duds too. DX7 retained mode, DX8 Matrox tessellation, DX9
ATI tessellation, DX10 geometry shaders & removal of FP16, DX11 shader interfaces, deferred contexts etc.

Yes, predicting the future is hard, and once in a while you do a bet on something that turns out to be not that good, or
not that needed, or something entirely else happens that forces everyone else to go in another direction. So that's
fair enough, in best case the raytracing abstraction & APIs become an ubiquitous & loved thing, in worst case
no one will use it.

I'm not concerned about "ohh vendor lock-in" aspect of DXR; Khronos is apparently working on
[something there too](https://twitter.com/thekhronosgroup/status/976071695374213121). So that will cover your "other platforms"
part, but whether that will be a conceptually similar API or not remains to be seen.

What I am slightly uneasy about, however, is...


#### Black Box Raytracing

The API, as it is now, is a bit of a "black box" one.

* What acceleration structure is used, what are the pros/cons of it, the costs to update it, memory consumption etc.? Who knows!
* How is scheduling of work done; what is the balance between lane utilization vs latency vs register
  pressure vs memory accesses vs (tons of other things)? Who knows!
* What sort of "patterns" the underlying implementation (GPU + driver + DXR runtime) is good or bad at?
  Raytracing, or path tracing, can get super bad for performance at divergent rays (while staying conceptually elegant);
  what and how is that mitigated by any sort of ray reordering, bundling, coalescing (insert N other buzzwords here)?
  Is that done on some parts of the hardware, or some parts of the driver, or DXR runtime? Who knows!
* The "oh we have BVHs of triangles that we can traverse efficiently" part might not be enough. How do you do LOD?
  As [Sebastien](https://twitter.com/SebAaltonen/status/976135026633945089) and [Brian](https://twitter.com/BrianKaris/status/976161513160433664)
  point out, there's quite some open questions in that area.

There's been a _massive_ work with modern graphics APIs like Vulkan, D3D12 and partially Metal to move away from black
boxes in graphics. DXR seems to be a step against that, with a bunch of "ohh, you never know! might be your GPU, might be your
driver, might be your executable name lacking a `quake3.exe`" in it.


It _probably_ would be better to expose/build whatever "magics" the upcoming GPUs might have to **allow people to _build_
efficient tracers** themselves. Ability to spawn GPU work from other GPU work; whatever instructions/intrinsics
GPUs might have for efficient tracing/traversal/intersection math; whatever fixed function hardware might exist
for scheduling, re-scheduling and reordering of work packets for improved coherency & memory accesses, etc. etc.

I have a suspicion that the above is probably not done "because patents". Maybe Imagination has an imperial ton of patents in the
area of ray reordering, and Nvidia has a metric ton of patents in all the raytracing research they've been doing for _decades_
by now, and so on. And if that's true, then indeed "just expose these bits to everyone" is next to impossible, and DXR
type approach is "best we can do given the situation"... Sad!


#### I'll get back to my own devices :)

So, yeah. Will be interesting to see where this all goes. It's exciting, but also a bit worrying, and a whole bunch of
open questions. Here's to having it all unfold in a good way, good luck everyone!

And I just realized I've never written even a toy path tracer myself; and the only raytracer I've done was for an
[OCaml](https://en.wikipedia.org/wiki/OCaml) course in the university, some 17 years ago. 
So I got myself Peter Shirley's [Ray Tracing in One Weekend](http://in1weekend.blogspot.lt/) and two other minibooks, and will play
around with it. Maybe as a test case for Unity's new [Job System, ECS](https://forum.unity.com/forums/entity-component-system-and-c-job-system.147/)
& Burst compiler, or as an excuse to learn Rust, or whatever.

[{{<img src="/img/blog/2018/rt-pathtracer.png">}}](/img/blog/2018/rt-pathtracer.png)
