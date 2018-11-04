---
title: "Pathtracer 15: Pause & Links"
aliases: ["/blog/2018/08/01/Pathtracer-15-Pause--Links/"]
date: 2018-08-01T21:41:10+03:00
tags: ['rendering', 'code', 'gpu']
comments: true
---

> Sailing out to sea | A tale of woe is me<br/>
> I forgot the name | Of where we're heading<br/> 
> -- Versus Them "[Don't Eat the Captain](https://vsthem.bandcamp.com/track/dont-eat-the-captain)"

So! This whole [series on pathtracing adventures](/blog/2018/03/28/Daily-Pathtracer-Part-0-Intro/) started
out without a clear goal or purpose. "*I'll just play around and see what happens*" was pretty much it.
Looks like I ran out of steam and will pause doing further work on it. Maybe sometime later
I'll pick it up again, who knows!

One nice thing about 2018 is that there's *a lot* of interest in ray/path tracing again, and
other people have been writing about various aspects of it. So here's a collection of
links I saved on the topic over past few months:

* "[Efficient Incoherent Ray Traversal on GPUs Through Compressed Wide BVHs](http://research.nvidia.com/sites/default/files/publications/ylitie2017hpg-paper.pdf)"
  by Henri Ylitie, Tero Karras, Samuli Laine, 2017 July.
* "[Caffeine path tracing demo & tutorial (WebGL)](https://wwwtyro.net/2018/02/25/caffeine.html)"
  by Rye Terrell, 2018 February.
* "[GPU Ray Tracing in One Weekend](https://medium.com/@jcowles/gpu-ray-tracing-in-one-weekend-3e7d874b3b0f)"
  by Jeremy Cowles, 2018 March.
* "[GDC Retrospective and Additional Thoughts on Real-Time Raytracing](https://colinbarrebrisebois.com/2018/04/07/some-thoughts-on-real-time-raytracing/)"
  by Colin Barré-Brisebois, 2018 April.
* A bunch of [interesting blog posts](https://schuttejoe.github.io/post/) on importance sampling etc.
  by Joe Schutte, 2018 March-May.
* "[minpt: A path tracer in 300 lines of C++](https://github.com/hi2p-perim/minpt)"
  by Hisanari Otsu, 2018 May.
* "[D3D12 Raytracing Samples](https://github.com/Microsoft/DirectX-Graphics-Samples/tree/master/Samples/Desktop/D3D12Raytracing)" by Microsoft, 2018 May.
* "[Writing a Portable CPU/GPU Ray Tracer in C#](https://mellinoe.github.io/graphics/2018/05/19/writing-a-portable-cpu-gpu-ray-tracer-in-c.html)"
  by Eric Mellino, 2018 May.
* "[GPU Ray Tracing in Unity – Part 1](http://blog.three-eyed-games.com/2018/05/03/gpu-ray-tracing-in-unity-part-1/)"
  and "[GPU Path Tracing in Unity – Part 2](http://blog.three-eyed-games.com/2018/05/12/gpu-path-tracing-in-unity-part-2/)"
  by David Kuri, 2018 May.
* "[Stochastic All the Things: Raytracing in Hybrid Real-time Rendering](https://www.ea.com/seed/news/seed-dd18-presentation-slides-raytracing)" by Tomasz Stachowiak, 2018 May.
* "[Adding texturing to a GLSL path tracer](https://chronokun.github.io/posts/2018-05-29--0.html)"
  by Michael Cameron, 2018 May.
* "[Denoising with Kernel Prediction and Asymmetric Loss Functions](http://drz.disneyresearch.com/~jnovak/publications/KPAL/index.html)"
  by Thijs Vogels, Fabrice Rousselle, Brian McWilliams, Gerhard Röthlin, Alex Harvill, David Adler, Mark Meyer, Jan Novák,
  2018 June.
* Series on path tracer in Rust ([initial](https://bitshifter.github.io/2018/04/29/rust-ray-tracer-in-one-weekend/),
  [threading](https://bitshifter.github.io/2018/05/07/path-tracing-in-parallel/), [SIMD](https://bitshifter.github.io/2018/06/04/simd-path-tracing/), [more SIMD](https://bitshifter.github.io/2018/06/20/the-last-10-percent/))
  by Cameron Hart, 2018 May-June.
* "[Gradients, Poisson's Equation and Light Transport (two minute papers)](https://www.youtube.com/watch?v=sSnDTPjfBYU)"
  by Károly Zsolnai-Fehér, 2015 October.
* "[Compressed Leaf Bounding Volume Hierarchies](https://ingowald.blog/2018/06/06/preprint-of-our-hpg2018-compressed-leaf-bvh-short-paper/)"
  by Carsten Benthin, Ingo Wald, Sven Woop, Attila Áfra, 2018 June.
* "The other pathtracer" series ([job system](https://technik90.blogspot.com/2018/06/the-other-pathtracer-basic-job-system.html), [triangles](http://technik90.blogspot.com/2018/06/the-other-pathtracer-2-triangle.html), [complex scenes](http://technik90.blogspot.com/2018/06/the-other-pathtracer-3-complex-scenes.html), [optimizing AABB-Ray](https://technik90.blogspot.com/2018/06/the-other-pathtracer-4-optimizing-aabb.html))
  by Carmelo Fernández-Agüera Tortosa, 2018 June.
* "[Metal for Ray Tracing Acceleration](https://developer.apple.com/videos/play/wwdc2018/606/)"
  by Sean James, Wayne Lister, 2018 June.
* Another raytracing blogpost series ([rendering equation](http://viclw17.github.io/2018/06/30/raytracing-rendering-equation/), [image output](http://viclw17.github.io/2018/07/15/raytracing-image-output/), [ray-sphere intersection](http://viclw17.github.io/2018/07/16/raytracing-ray-sphere-intersection/), [camera and anti-aliasing](http://viclw17.github.io/2018/07/17/raytracing-camera-and-msaa/), [diffuse materials](http://viclw17.github.io/2018/07/20/raytracing-diffuse-materials/), [reflecting materials](http://viclw17.github.io/2018/07/30/raytracing-reflecting-materials/))
  by Victor Li, 2018 July.
* [Ray tracer with Metal Performance Shaders](https://github.com/sergeyreznik/metal-renderer)
  by Serhii Rieznik, 2018 June-July.
* [Quick Path Tracer project in C++](https://github.com/rorydriscoll/RayTracer)
  by Rory Driscoll, 2018 June.
* "[Pathtraced Depth of Field & Bokeh](https://blog.demofox.org/2018/07/04/pathtraced-depth-of-field-bokeh/)"
  by Alan Wolfe, 2018 July.  
* "[GPU based clay simulation and ray tracing tech in Claybook](https://www.youtube.com/watch?v=Xpf7Ua3UqOA)"
  by Sebastian Aaltonen, 2018 July.
* "[Sampling Anisotropic Microfacet BRDF](https://agraphicsguy.wordpress.com/2018/07/18/sampling-anisotropic-microfacet-brdf/)"
  by Cao Jiayin, 2018 July.
* [CUDA Pathtracer](https://voxel-tracer.github.io/cuda-pathtracer-index/) blog series ([preparation](https://voxel-tracer.github.io/Code-Preparation/), [first project](https://voxel-tracer.github.io/Your-First-Cuda-Project/), [lightweight kernel](https://voxel-tracer.github.io/lightweight-kernel/), [optimizing memory transfers](https://voxel-tracer.github.io/Optimize-Memory-Transfers/), [compacting non-active rays](https://voxel-tracer.github.io/compact-non-active-rays/)) by @voxel_tracer, 2018 June-July.
* "[Swallowing the elephant (optimizing pbrt for Moana scene)](http://pharr.org/matt/blog/2018/07/16/moana-island-pbrt-all.html)"
  by Matt Pharr, 2018 July.

Thanks for the adventure so far, everyone!

> Put the fork away | It's not a sailor's way<br/>
> We are gentlemen | Don't eat the captain<br/>
