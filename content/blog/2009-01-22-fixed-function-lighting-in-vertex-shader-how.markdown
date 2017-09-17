---
tags:
- code
- d3d
- gpu
- rendering
- work
comments: true
date: 2009-01-22T22:32:49Z
slug: fixed-function-lighting-in-vertex-shader-how
status: publish
title: Fixed function lighting in vertex shader - how?
url: /blog/2009/01/22/fixed-function-lighting-in-vertex-shader-how/
wordpress_id: "261"
---

Sometime soon I'll have to implement fixed function lighting pipeline in vertex shaders. Why? Because mixing fixed function and vertex shaders in multiple passes does not guarantee identical transformation results, thus requiring depth bias or projection matrix tweaks, which leads to [various artifacts](/blog/2008/06/12/depth-bias-and-the-power-of-deceiving-yourself/) that annoy people to hell.

I don't really know _why_ that happens, because it seems that most modern cards don't have fixed function units, so internally they are running shaders anyway. DX9 runtime on Vista's WDDM also seems to be only handling shaders to the driver internally. Still, for some reason somewhere the precision does not match...

How such a task should be approached?

My requirements are:



  
  * Should handle any possible state combination in D3D fixed function T&L.

  
  * D3D 9.0c, using vertex shader 2.0 is ok. For now I don't care about OpenGL.

  
  * No HLSL at runtime. I don't want to add a megabyte or more to Unity web player just for HLSL. DX9 shader assembly is ok, because we already have the assembler code.

  
  * Should work as fast (or close to) as the regular fixed function pipeline.



I looked at ATI's [FixedFuncShader sample](http://developer.amd.com/samples/FixedFuncShader/Pages/default.aspx). It's an **ubershader approach**; one large (230 instructions or so) shader with static VS2.0 branching. It had some obvious places to optimize, I could get it down to 190 or so instructions, kill some [rcp][1]'s and reduce the amount of constant storage by 2x.

Still, it did not handle some things in the D3D T&L or had some issues:



  
  * It assumes one input UV, one output UV and no texture matrices. This place in T&L gets quite convoluted - any input UVs or a texgen mode can be transformed by matrices of various sizes, and routed into any output UVs.

  
  * It was not using full T&L lighting model. No biggie here.

  
  * I haven't checked with NVShaderPerf or AMD ShaderAnalyzer yet, but last time I checked the static branch instruction was taking two clocks on some NV architecture. So ubershader approach does not come for free.



Another thing I'm considering, is to combine final shader(s) from **assembly fragments**, with some simple register allocation.

In T&L shader code, there's only limited set of could-be-redundant computations, mostly computing world space position, camera space normal, view vector and so on (those could be used lighting, texgen or fog). Those computations can be explicitly put into separate fragments, and later fragments could just use their result.

What is left then is some register allocation. A shader assembly fragment could want some temporary registers for internal use (this is simple, just give it a bunch of unused registers), also want some registers as input (from previous fragments), and save some output in registers.

Again, I haven't checked with shader performance tools, but I _think, guess and hope_ that the drivers do additional register allocation, liveness analysis etc. when converting D3D shader bytecode into hardware format. This would mean that _I_ can be quite sloppy with it, i.e. don't have to implement some super smart allocation scheme.

I wrote some experimental code for the shader assembly combiner and so far it looks like a reasonable approach (and not too hard either).

Does that make sense? Or did everyone solve those problems eons ago already?

**Edit**: half a year later, I wrote a technical report on how I implemented all this: [aras-p.info/texts/VertexShaderTnL.html](/texts/VertexShaderTnL.html)


[1]: http://msdn.microsoft.com/en-us/library/bb147316(VS.85).aspx
