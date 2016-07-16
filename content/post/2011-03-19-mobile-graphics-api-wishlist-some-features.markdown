---
categories:
- mobile
- opengl
- rendering
comments: true
date: 2011-03-19T15:50:15Z
slug: mobile-graphics-api-wishlist-some-features
status: publish
title: 'Mobile graphics API wishlist: some features'
url: /blog/2011/03/19/mobile-graphics-api-wishlist-some-features/
wordpress_id: "653"
---

In my [previous post](/blog/2011/03/04/mobile-graphics-api-wishlist-performance/) I talked about things I'd want from OpenGL ES 2.0 in the performance area. Now it's time to look at what extra features it might expose with an extension here or there.

_Note that I’m focusing on, in my limited understanding, low-hanging fruits. The features I want already exist in the current GPUs or platforms; or could be easily made available. Of course more radical new architectures would bring more & fancier features, but that's a topic for another story._


**Programmable blending**

At least two out of three big current mobile GPU families (PVR SGX, Adreno, Tegra 2) support programmable blending in the hardware. Maybe all of them do this and I just don't have enough data. By "support it in the hardware" I mean either: 1) the GPU has no blending hardware, the drivers add "read current pixel & blend" instructions to the shaders or 2) has blending hardware for commonly used modes, but fancier modes use shader patching with no severe performance penalties.

Programmable blending is useful for various things; from deferred-style decals (blending normals is hard in fixed function!) to fancier Photoshop-like blend modes to potentially faster single-pixel image postprocessing effects (like color correction).

Currently only NVIDIA exposes this capability via [NV_shader_framebuffer_fetch](http://developer.download.nvidia.com/tegra/docs/tegra_gles2_development.pdf) extension.

_Suggestion_: expose it on other hardware that can do this! It's fine to not handle hard edge cases (for example, what happens when multisampling is used?), we can live with the limitations.


**Direct, fast access to frame buffer on the CPU**

Most (all?) mobile platforms use unified memory approach, where there's no physical distinction between "system memory" and "video memory". Some of those platforms are slightly unbalanced, e.g. a strong GPU coupled with a weak CPU or vice versa. More and more of those systems will have multicore CPUs. It might make sense to do similar approaches that PS3 guys are doing these days - offload some of the GPU work to the CPU(s).

Image processing, deferred lighting and similar things could be done more efficiently on a general purpose CPU, where you aren't limited to "one pixel at a time" model of current mobile GPUs.

_Suggestion_: can haz get a pointer to framebuffer memory perhaps? Of course this is grossly oversimplifying all the synchronization & security issues, but _something_ should be possible to do in order to exploit the unified memory model. Right now it just sits there largely unused, with GLES2.0 still pretending CPU is talking to a GPU over a ten meter high concrete wall.


**Expose Tile Based GPU capabilities**

PowerVR GPUs found in all iOS and some Android devices are so called "tile based" architectures. So is, to some extent, Qualcomm Adreno family.

Currently this capability is mostly sitting behind a black box. On PowerVR GPUs the programmer does know that "overdraw of opaque objects does not matter", or that "alpha testing is really slow" but that's about it. There's no control over the whole rendering process, even if some of the things could benefit from having more control over the whole tiling thing.

Take, for example, deferred lighting/shading. The cool folks are doing it tile-based already on [DirectX 11](http://www.slideshare.net/DICEStudio/directx-11-rendering-in-battlefield-3?from=ss_embed) or [PS3](http://www.slideshare.net/DICEStudio/spubased-deferred-shading-in-battlefield-3-for-playstation-3?from=ss_embed).

On a tile-based GPU, all rendering is _already_ happening in tiles, so what if we could say "now, you work on this tile, render this, render that; now we go this this tile"? Maybe that way we could achieve two things at once: 1) better light culling because it's at tile level, and 2) most of the data could stay on this super-fast on-chip memory, without having to be written into system memory & later read again. Memory bandwidth is very often a limiting factor in mobile graphics performance, and ability to keep deferred lighting buffers on-chip through the whole process could cut down bandwidth requirements a lot.

_Suggestion_: somehow _(I'm feeling very hand-wavy today)_ expose more control over tiled rendering. For example, explicitly say that rendering will only happen to the given tiles; and these textures are very likely to be read just after they are rendered into - so don't resolve them to memory if they fit into on-chip one.

There's already a Qualcomm extension of something towards that area - [QCOM_tiled_rendering](http://www.khronos.org/registry/gles/extensions/QCOM/QCOM_tiled_rendering.txt) - though it seems to be more concerned about where does rendering happen. More control is needed on how to mark FBO textures as "keep in on-chip memory for sampling as a texture plz".


**OpenCL**

Current mobile GPUs already are, or very soon will be, OpenCL capable. Also OpenCL can be implemented on the CPU, nicely SIMDified via NEON, and use multicore. _DO WANT!_ (and while you're at it, everything that's doable to make interop between CL & GL faster)

This can be used for a ton of things; skinning, culling, particles, procedural animations, image postprocessing and so on. And with a much less restrictive programming model, it's easier to reuse computation results across draw calls or frames.

Couple this with "direct access to memory on the CPU" and OpenCL could be used for more things than graphics (again I'm grossly oversimplifying here and ignoring the whole synchronization/latency/security elephant...).

**MOAR?**

Now of course there are more things I'd want to see, but for today I'll take just those above, thank you. Have a nice day!
