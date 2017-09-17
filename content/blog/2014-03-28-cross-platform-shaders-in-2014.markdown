---
tags:
- code
- d3d
- opengl
comments: true
date: 2014-03-28T00:00:00Z
title: Cross Platform Shaders in 2014
url: /blog/2014/03/28/cross-platform-shaders-in-2014/
---

A while ago I wrote a [Cross platform shaders in 2012](/blog/2012/10/01/cross-platform-shaders-in-2012/) post. What has changed since then?

Short refresher on the problem: people need to do 3D things on multiple platforms, and different platforms use different shading languages (big ones are HLSL and GLSL). However, no one wants to write their shaders twice. It would be kind of stupid if one had to write *different* C++ for, say, Windows & Mac. But right now we have to do it for shader code.


Most of the points from my [previous post](/blog/2012/10/01/cross-platform-shaders-in-2012/); I'll just link to some new tools that appeared since then:


### #1. Manual / Macro approach

Write some helper macros to abstract away HLSL & GLSL differences, and make everyone aware of all the differences. Examples:
Valve's Source 2 ([DevDays talk](https://www.youtube.com/watch?v=45O7WTc6k2Y)), bkaradzic's bgfx library ([shader helper macros](https://github.com/bkaradzic/bgfx/blob/master/src/bgfx_shader.sh)), FXAA 3.11 source etc.

Pros: Simple to do.

Cons: Everyone needs to be aware of that macro library and other syntax differences.


### #2. Invent your own language with HLSL/GLSL backends

Or generate HLSL/GLSL from some graphical shader editor, and so on.


### #3. Translate compiled shader bytecode into GLSL

*Pros*: Simpler to do than full language translation. Microsoft's HLSL compiler does some decent optimizations, so resulting GLSL would be fairly optimized.

*Cons*: Closed compiler toolchain (HLSL) that only runs on Windows. HLSL compiler in some cases does *too many* optimizations that don't make much sense these days.

Tools:

* [HLSLCrossCompiler](https://github.com/James-Jones/HLSLCrossCompiler) by James Jones. Supports DX10-11 bytecode and produces
  various GLSL versions as output. Under active development.
* [MojoShader](https://icculus.org/mojoshader/) by Ryan Gordon. Supports DX9 (shader model 1.1-3.0).
* [TOGL](https://github.com/ValveSoftware/ToGL) from Valve. Again DX9 only, and only partial one at that (some shader model 3.0 features
  aren't there).




### #4. Translate HLSL into GLSL at source level, or vice versa


* [hlsl2glslfork](https://github.com/aras-p/hlsl2glslfork) from Unity. DX9 level HLSL in, GLSL 1.xx / OpenGL ES (including ES3) out.
  Does work (used in production at Unity and some other places), however quite bad codebase and we haven't shoehorned DX10/11 style HLSL support into it yet.
* [ANGLE](https://chromium.googlesource.com/angle/angle) from Google. OpenGL ES 2.0 (and possibly 3.0?) shaders in, DX9/DX10 HLSL out.
  This is whole OpenGL ES emulation on top of Direct3D layer, that also happens to have a shader cross-compiler.
* [OpenGL Reference Compiler](http://www.khronos.org/opengles/sdk/tools/Reference-Compiler/) from Khronos. While itself it's only a GLSL validator, it has a full GLSL parser (including partial support for GL4.x at this point). Should be possible to make it emit HLSL with some work. A bit weird that source is on some subversion server though - not an ideal platform for contributing changes or filing bugs.
* [HLSL Cross Compiler](https://docs.unrealengine.com/latest/INT/Programming/Rendering/ShaderDevelopment/HLSLCrossCompiler/index.html) from Epic. This is in Unreal Engine 4, and built upon Mesa's GLSL stack (or maybe [glsl optimizer](https://github.com/aras-p/glsl-optimizer)), with HLSL parser in front. *Note that this isn't open source, but hey one can dream!*
* [hlslparser](https://github.com/unknownworlds/hlslparser) from Unknown Worlds. Converts DX9-style HLSL (with constant buffers) into GLSL 3.1.
* [MojoShader](https://icculus.org/mojoshader/) by Ryan Gordon. Seems to have some code for parsing DX9-style HLSL, not quite sure how production ready.

I thought about doing similar thing like Epic folks did for UE4: take [glsl optimizer](https://github.com/aras-p/glsl-optimizer) and add a HLSL parser. These days Mesa's GLSL stack already has support for compute & geometry shaders, and I think tessellation shaders will be coming soon. This would be much better codebase than hlsl2glslfork. However, never had time to actually do it, besides thinking about it for a few hours :(


### Call to action?

Looks like we'll stay with two shading languages for a while now *(Windows and all relevant consoles use HLSL; Mac/Linux/iOS/Android use GLSL)*. So each and every graphics developer who does cross platform stuff is facing this problem.

I don't think IHVs will solve this problem. NVIDIA did try once with Cg (perhaps too early), but Cg is pretty much dead now.

DX9-level shader translation is probably a solved problem (hlsl2glslfork, mojoshader, ANGLE). However, we need a DX10/11-level translation - with compute shaders, tessellation and all that goodness.

We have really good collaboration tools in forms of github & bitbucket. Let's do this. Somehow.

