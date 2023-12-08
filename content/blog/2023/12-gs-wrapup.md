---
title: "Gaussian explosion"
date: 2023-12-08T09:44:10+03:00
tags: ['rendering', 'code', 'gpu']
---

Over the past month it seems like Gaussian Splatting (see my [first post](/blog/2023/09/05/Gaussian-Splatting-is-pretty-cool/))
is experiencing a ~~Cambrian~~ Gaussian explosion of new research. The [seminal paper](https://repo-sam.inria.fr/fungraph/3d-gaussian-splatting/)
came out in July 2023, and starting about mid-November, it feels like every day there's a new paper or two coming out,
related to Gaussian Splatting in some way. @MrNeRF and @henrypearce4D maintain an excellent list of all things related to 3DGS,
check out their [**Awesome 3D Gaussian Splatting Resources**](https://github.com/MrNeRF/awesome-3D-gaussian-splatting).

By no means an exhaustive list, just random selection of interesting bits:

#### Ecosystem and tooling

- PlayCanvas has released SuperSplat, an [online splat editor](https://playcanvas.com/super-splat) ([source](https://github.com/playcanvas/super-splat)),
  have added 3DGS support to the engine [v1.67.0](https://github.com/playcanvas/engine/releases/tag/v1.67.0) and made their
  own splat data compression approach ([blog post](https://blog.playcanvas.com/compressing-gaussian-splats/)) that is somewhat based on my blog posts, yay.
- Luma AI has released [Unreal Engine 5 plugin](https://lumaai.notion.site/Luma-Unreal-Engine-Plugin-0-4-8005919d93444c008982346185e933a1#c4c8d006b08c47fe8d901c2beca3f3bf)
  and a WebGL Three.js based viewing library: [Luma WebGL Library](https://lumalabs.ai/luma-web-library).
- Spline has added [gaussian splat support](https://docs.spline.design/e17b7c105ef0433f8c5d2b39d512614e), with simple editing tools.
- Discussion about possible "[universal splat format](https://github.com/mkkellogg/GaussianSplats3D/issues/47)" among various open source tool authors.

#### Research

- [GaussianEditor: Swift and Controllable 3D Editing with Gaussian Splatting](https://buaacyw.github.io/gaussian-editor/) applies ML-based
  editing and inpainting tools for gaussian splats.
- [Relightable 3D Gaussian: Real-time Point Cloud Relighting with BRDF Decomposition and Ray Tracing](https://nju-3dv.github.io/projects/Relightable3DGaussian/) attempts to make gaussians re-lightable by associating typical PBR material parameters (normal, BRDF properties),
incident light information, etc.
- [GS-IR: 3D Gaussian Splatting for Inverse Rendering](https://lzhnb.github.io/project-pages/gs-ir.html) extends 3DGS pipeline to derive
  surface normals, albedo and roughness, to achieve re-lighting of gaussian splats.
- [**LightGaussian: Unbounded 3D Gaussian Compression with 15x Reduction**](https://lightgaussian.github.io/) reduces gaussian splat data sizes
  by pruning and merging splats to reduce their count, makes spherical harmonics data smaller by reducing their order more cleverly than just
  "drop some coefficients", and applies several quantization schemes to final data. This is clever stuff!
- [**Mip-Splatting Alias-free 3D Gaussian Splatting**](https://niujinshuchong.github.io/mip-splatting/) fixes some aliasing and dilation issues
  of original 3DGS paper, by effectively doing something smarter than "just add 0.3 here, lol" :)
- [Compact3D: Compressing Gaussian Splat Radiance Field Models with Vector Quantization](https://github.com/UCDvision/compact3d) uses K-means
  clustering on colors, rotations, scales and spherical harmonics of 3DGS, to reduce data size.
- [GaussianShader: 3D Gaussian Splatting with Shading Functions for Reflective Surfaces](https://asparagus15.github.io/GaussianShader.github.io/)
  is another go at making gaussians relightable by estimating normal and other material parameters.
- [**Relightable Gaussian Codec Avatars**](https://shunsukesaito.github.io/rgca/) is targeted at face avatars. They place gaussians on a coarse 3D
  mesh, and use a learnable radiance transfer with diffuse SH and specular SG. Clever way of plugging gaussian splats into an existing mesh-based
  avatar pipeline.
- [**SuGaR: Surface-Aligned Gaussian Splatting for Efficient 3D Mesh Reconstruction and High-Quality Mesh Rendering**](https://imagine.enpc.fr/~guedona/sugar/)
  aligns gaussians closer with underlying surfaces, which then allows extracting the mesh, which then allows it to be animated or skinned. The final
  mesh still has gaussian splats "attached" to the polygons.


### Unity Gaussian Splatting

The [**Unity Gaussian Splatting**](https://github.com/aras-p/UnityGaussianSplatting) project that I created with intent of
"eh, lemme try to make a quick toy 3DGS renderer in Unity, and maybe play around with data size reductions", has somewhat surprisingly
reached 1300+ GitHub stars. Since the [previous blog post](/blog/2023/09/05/Gaussian-Splatting-is-pretty-cool/) it got a bunch
of random things:
- Support for HDRP and URP rendering pipelines in adition to the built-in one.
- Fine grained splat editing tools in form of selection and deletion ([short video](https://www.youtube.com/watch?v=MKIGtEIjRi0)).
- High level splat editing tools in form of ellipsoid and box shaped "cutouts". @hybridherbst did the
  [initial implementation](https://github.com/aras-p/UnityGaussianSplatting/pull/24), and then shortly afterwards all other
  3DGS editing tools got pretty much the same workflow. Nice! \
  [{{<img src="/img/blog/2023/gaussian-splat/splat-cutouts.png" width="450px">}}](/img/blog/2023/gaussian-splat/splat-cutouts.png)
- Ability to export modified/edited splats back into a .PLY file.
- Faster rendering via more tight oriented screenspace quads, instead of axis-aligned quads.
- I made the gaussian splat rendering+editing piece an actual package ([OpenUPM page](https://openupm.com/packages/org.nesnausk.gaussian-splatting/)),
  and clarified license to be MIT.
- *(not part of github release, but in latest main branch)* More fine grained editing tools (move individual splats), ability to bake
  splat transform when exporting .PLY, and multiple splats can be merged together.

Aaaand with that, I'm thinking that my toying around will end here. I've made a toy renderer and integration into Unity,
learned a bunch of random things in the process, it's time to call it a day and move onto something else. I suspect there will be another
kphjillion gaussian splatting related papers coming out over the next year. Will be interesting to see where all of this ends up at!



