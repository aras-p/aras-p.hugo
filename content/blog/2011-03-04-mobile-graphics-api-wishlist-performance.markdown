---
categories:
- mobile
- opengl
- rendering
comments: true
date: 2011-03-04T08:24:49Z
slug: mobile-graphics-api-wishlist-performance
status: publish
title: 'Mobile graphics API wishlist: performance'
url: /blog/2011/03/04/mobile-graphics-api-wishlist-performance/
wordpress_id: "645"
---

Most mobile platforms currently are based on OpenGL ES 2.0. While it is _much_ better than traditional OpenGL, there are ways where it limits performance or does not expose some interesting hardware features. So here's an unorganized wishlist for GLES2.0 performance part!

_Note that I'm focusing on, in my limited understanding, short term low-hanging fruits how to extend/patch existing GLES2.0 API. A pipe dream would be starting from scratch, getting rid of all OpenGL baggage and hopefully come up with a much cleaner, leaner & better API, especially if it's designed to only support some particular platform. But I digress, back to GLES2.0 for now._

**No guarantees when something expensive might happen.**

Due to some flexibility in GLES2.0, there might be expensive things happening at almost any point in your frame. For example, binding a texture with a different format might cause a driver to recompile a shader at the draw call time. I've seen [60 milliseconds](http://twitter.com/#!/aras_p/status/34628257294852096) on iPhone 3Gs at first draw call with a relatively simple shader, all spent inside shader compiler backend. _60 milliseconds!_ There are various things that can cause performance hiccups like this: texture formats, blending modes, vertex layout, non power of two textures and so on.

_Suggestion_: work with GPU vendors and agree on an API that could make guarantees on when the expensive resource creation / patching work can happen, and when it can't. For example, _somehow_ guarantee that a draw call or a state set will not cause any object recreation / shader patching in the driver. I don't have much experience with D3D10/11, but my impression is that this was one of the things it got right, no?


**Offline shader compilation.**

GLES2.0 has the functionality to load binary shaders, but it's not mandatory. Some of the big platforms (iOS, I'm looking at you) just don't support it.

Now of course, a single platform (like iOS or Android) can have multiple different GPUs, so you can't fully compile a shader offline into final optimized GPU microcode. But _some_ of the full compilation cost could very well be done offline, without being specific to any particular GPU.

_Suggestion_: come up with a platform independent binary shader format. Something like D3D9 shader assembly is probably too low level (it assumes a vector4-based GPU, limited number of registers and so on), but something higher level should be possible. All of the shader lexing, parsing and common optimizations (constant folding, arithmetic simplifications, dead code removal etc.) can be done offline. It won't speed up shader loading by an order of magnitude, but even if it's possible to cut it by 20%, it's worth it. And it would remove a very big bug surface area too!


**Texture loading.**

A lot (all?) of mobile platforms have unified CPU & GPU memories, however to actually load the texture we have to read or memory map it from disk and then copy into OpenGL via glTexture2D and similar functions. Then, depending on the format, the driver would internally do swizzling and alignment of texture data.

_Suggestion_: can't most of this cost be removed? If for some formats it's perfectly, statically known what layout and swizzling the GPU expects... can't we just point the API to the data we already loaded or memory mapped? We could still need to implement the glTexture2D case for when (if ever) a totally new strange GPU comes that needs the data in a different order, but why not provide a faster path for the current GPUs?


**Vertex declarations.**

In unextended GLES2.0 you have to do _a ton_ of calls just to setup vertex data. [OES_vertex_array_object](http://www.khronos.org/registry/gles/extensions/OES/OES_vertex_array_object.txt) is a step in the right direction, providing the ability to create sets of vertex data bindings ("vertex declarations" in D3D speak). However, it builds upon an existing API, resulting in something that feels quite messy. Somehow it feels that by starting from scratch it could result in something much cleaner. Like... vertex declarations that existed in D3D since forever maybe?

_Suggestion_: clean up that shit! It would probably need to be tied to a vertex shader input signature (just like in D3D10/11) to guarantee there would be no shader patching, but we'd be fine with that.


**Shader uniforms are per shader program.**

What it says - shader uniforms ("constants" in D3D speak) are not global; they are tied to a specific shader program. I don't quite understand why, and I don't think any GPU works that way. This is causing complexities and/or performance loss in the driver (it either has to save & restore all uniform values on each shader change, or have dirty tracking on which uniforms have changed etc.). It also causes unneeded uniform sets on the client side - instead of having, for example, view*projection matrix set just once per frame it has to be set for each shader program that we use.

_Suggestion_: just get rid of that? If you need to not break the existing spec, how about adding an extension to make all uniforms global? I propose `glCanHaz(GL_OES_GLOBAL_UNIFORMS_PLZ)`


**Next up:**

Next time, I'll take a look at my unorganized wishlist for mobile graphics features!
