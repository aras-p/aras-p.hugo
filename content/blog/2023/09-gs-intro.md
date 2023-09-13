---
title: "Gaussian Splatting is pretty cool!"
date: 2023-09-05T21:04:10+03:00
tags: ['rendering', 'code', 'gpu']
---

SIGGRAPH 2023 just had a paper "[**3D Gaussian Splatting for Real-Time Radiance Field Rendering**](https://repo-sam.inria.fr/fungraph/3d-gaussian-splatting/)"
by Kerbl, Kopanas, Leimkühler, Drettakis, and it looks
pretty cool! Check out their website, source code repository, data sets and so on (I should note that
it is *really, really good* to see full source and full data sets being released. Way to go!).

I've decided to try to implement the realtime visualization part (i.e. the one that takes
already-produced gaussian splat "model" file) in Unity. As well as maybe play around with looking at
whether the data sizes could be made smaller (maybe use some of the learnings from
[float compression series](/blog/2023/01/29/Float-Compression-0-Intro/) too?).


[{{<imgright src="/img/blog/2023/gaussian-splat/gs-bike-initial.jpg" width="300px">}}](/img/blog/2023/gaussian-splat/gs-bike-initial.jpg)
*What's a few million badly rendered boxes among friends, anyway?*

For the impatient: I got *something* working over at [aras-p/UnityGaussianSplatting](https://github.com/aras-p/UnityGaussianSplatting), and will tinker with things there some more.

*Meanwhile, some quick random thoughts on this Gaussian Splatting thing.*

#### What are these Gaussian Splats?

Watch the [**paper video**](https://www.youtube.com/watch?v=T_kXY43VZnk) and read
[the paper](https://repo-sam.inria.fr/fungraph/3d-gaussian-splatting/), they are pretty good!

{{<youtube T_kXY43VZnk>}}

I have seen quite many 3rd party explanations of the concept at this point, and some of them, uhh, get a thing or
two wrong about it :)

- This is ***not*** a NeRF (Neural Radiance Field)! There is absolutely nothing "neural" about it.
- It is ***not*** somehow "fast, because it uses GPU rasterization hardware". The official implementation does not
  use the rasterization pipeline at all; it is 100% done with CUDA. In fact, it is fast *because* it does not use
  the fixed function rasterization, as we'll see below.

*Anyway,*

Gaussian Splats are, basically, "**a bunch of blobs in space**". Instead of representing a 3D scene as
polygonal meshes, or voxels, or distance fields, it represents it as (millions of) particles:

- Each particle ("a 3D Gaussian") has position, rotation and a non-uniform scale in 3D space.
- Each particle also has an opacity, as well as color (actually, not a single color, but rather
  3rd order Spherical Harmonics coefficients - meaning "the color" can change depending on the view direction).
- For rendering, the particles are rendered ("splatted") as *2D Gaussians* in screen space, i.e. they
  are *not* rendered as scaled elongated spheres, actually! More on this below.

And *that's it*. The "Gaussian Splatting" scene representation is *just that*, a whole bunch of scaled and colored
blobs in space. The genius part of the paper is several things:

- They found a way **how** to create these millions of blobs that would represent a scene depicted by a bunch of
  photos. This is using gradient descent and "differentiable rendering" and all the other things that are way
  over my head. This *feels* like the major contribution, like maybe previously people assumed that in order
  for gradient descent optimizer to work nicely, you need to use a continuous or connected scene representation (vs
  "just a bunch of blobs"), and this paper proved that wrong? *Anyway, I don't understand this area, so I won't talk
  about it more :)*
- They have developed a fast way to render all these millions of scaled particles. This by itself is not particularly
  ground breaking IMHO, various people have noticed that using something like a tile-based "software, but on GPU"
  rasterizer is a good way to do this.
- They have combined existing established approaches (like gaussian splatting, and spherical harmonics)
  in a nice way.
- And finally, they have resisted the temptation to do "neural" anything ;)

#### Previous Building Blocks

The **Gaussian Splatting** seems to be invented around year 2001-2002, see for example "[EWA Splatting](https://www.cs.umd.edu/~zwicker/publications/EWASplatting-TVCG02.pdf)" paper by Zwicker, Pfister, Van Baar, Gross.
There they have scaled and oriented "blobs" in space, calculate how would they project onto screen, and then
do the actual "blob shape" (a "Gaussian") in 2D, in screen-space. A bunch of signal processing, sampling, aliasing etc.
math presumably supports doing it that way.

Speaking of ellipsoids, [Ecstatica game](https://www.youtube.com/watch?v=dnOXk3QJWN8) from 1994 had a fairly
unique ellipsoid-based renderer.

**Spherical Harmonics** (a way to represent a function over a surface of a sphere) have been around
for several hundred years in physics, but really were popularized in computer graphics around 2000
by Ravi Ramamoorthi and Peter-Pike Sloan. But actually, a 1984 "[Ray tracing volume densities](https://dl.acm.org/doi/abs/10.1145/964965.808594)" paper by Kajiya & Von Herzen might be the first use of them in graphics.
A nice summary of various things related to SH is at [Patapom's page](https://patapom.com/blog/SHPortal/).

**Point-Based Rendering** in various forms has been around for a long time, e.g. particle systems were used since
"forever" (but typically used for vfx / non-solid phenomena).
"[The Use of Points as a Display Primitive](https://graphics.stanford.edu/papers/points/)" is from 1985.
"[Surfels](https://www.cs.umd.edu/~zwicker/projectpages/Surfels-SIG00.html)" paper is from 2000.

Something closer
to my heart, a whole bunch of demoscene demos are using non-traditional rendering approaches. Fairlight & CNCD
have several notable examples, e.g.
[Agenda Circling Forth](https://www.youtube.com/watch?v=-VhecrFsZjc) (2010),
[Ceasefire (all falls down..)](https://www.youtube.com/watch?v=NaPPHUt4K34) (2010),
[Number One / Another One](https://www.youtube.com/watch?v=LyXwmt0EZig) (2018):

{{<youtube -VhecrFsZjc>}}

Real-time VFX tools like [Notch](https://www.notch.one/) have pretty extensive features for creating, simulating
and displaying point/blob based "things".

Ideas of representing images or scenes with a bunch of "primitive shapes", as well as tools to generate those, have
been around too. E.g. [fogleman/primitive](https://github.com/fogleman/primitive) (2016) is nice.

Media Molecule "Dreams" has a splat-based renderer (I think the shipped version is not purely splat-based
but a combination of several techniques). Check out the most excellent "Learning from Failure" talk by Alex Evans:
at [SIGGRAPH 2015](https://advances.realtimerendering.com/s2015/#_TESSELLATION_IN_CALL) (splats start at slide 109)
or video from [Umbra Ignite 2015](https://youtu.be/u9KNtnCZDMI?t=1354) (splats start at 22:34).

{{<youtube u9KNtnCZDMI>}}

**Tiled Rasterization** for particles has been around at least since 2014 (["Holy smoke! Faster Particle Rendering
using Direct Compute"](https://github.com/GPUOpen-LibrariesAndSDKs/GPUParticles11) by Gareth Thomas). And the idea
that dividing screen into tiles, doing a bunch of things "inside the tile" thus cutting on memory traffic,
is how *entire* mobile GPU space operates, and has been operating since "forever", tracing back to first PowerVR
designs (1996) and even Pixel Planes 5 from 1989.

This is all *great*! Taking existing, developed, solid building blocks and combining them in a novel way is excellent
work.


#### My Toy Playground

My current implementation (of just the visualizer of Gaussian Splat models) for Unity is over at github:
[**aras-p/UnityGaussianSplatting**](https://github.com/aras-p/UnityGaussianSplatting). Current state is "it kinda
works, but it is not fast":

* ~~The rendering does not look horrible, but does not exactly match official implementation. Here is official vs
  my rendering of the same scene. Official one has more small detail, and lighting is slightly different~~  _Fixed!_ \
  [{{<img src="/img/blog/2023/gaussian-splat/gs-bike-official.jpg" width="350px">}}](/img/blog/2023/gaussian-splat/gs-bike-official.jpg)
  [{{<img src="/img/blog/2023/gaussian-splat/gs-bike-mine02.jpg" width="350px">}}](/img/blog/2023/gaussian-splat/gs-bike-mine02.jpg)
* Performance is not great. The scene above renders on NVIDIA RTX 3080 Ti at 1200x800 in 7.40ms (135FPS) in
  the official viewer, whereas my attempt is 23.8ms (42FPS) currently, i.e. 4x slower. For sorting
  I'm using some fairly simple GPU bitonic sort (official impl uses CUDA radix sort which is
  based on [OneSweep algorithm](https://arxiv.org/abs/2206.01784)). Rasterization in their case is tile-based and written in CUDA,
  whereas I'm "just" using regular GPU rasterization pipeline and rendering each splat as a screenspace quad.
  * On the plus side, my code is all regular HLSL within Unity, which means it also happens to work on e.g.
    Mac just fine. The scene above on Apple M1 Max renders in 108ms (9FPS) though :/
  * My implementation seems to use 2x less GPU memory right now too (official viewer: 4.8GB, mine: 2.2GB and that's
    including whatever Unity editor takes).

 So all of that could be improved and optimized quite a bit!

 One thing I haven't seen talked much about, by everyone [super excited](https://www.youtube.com/watch?v=SbXmGgJePsk)
 about Gaussian Splats, is data size and memory usage. Yeah, rendering is nice, but this bicycle scene above is
 1.5GB of data on-disk, and then at runtime it needs some more (for sorting, tile based rendering etc.).
 That scene is six million blobs in space, with each of them taking about 250 bytes. There has to be some
 way to make that smaller! Actually the Dreams talk above has some neat ideas.

 *Maybe I should play around with that. [Someday!](/blog/2023/09/13/Making-Gaussian-Splats-smaller/)*


