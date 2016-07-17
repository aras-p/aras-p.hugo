---
tags:
- code
- rendering
comments: true
date: 2012-10-01T00:00:00Z
title: Cross Platform Shaders in 2012
url: /blog/2012/10/01/cross-platform-shaders-in-2012/
---

*Update: [Cross Platform Shaders in 2014](http://aras-p.info/blog/2014/03/28/cross-platform-shaders-in-2014/).*

Since about 2002 to 2009 the de facto shader language for games was HLSL. Everyone on PCs was targeting Windows through Direct3D, Xbox 360 uses HLSL as well, and Playstation 3 uses Cg, which for all practical purposes is the same as HLSL. There were very few people targeting Mac OS X or Linux, or using OpenGL on Windows. One shader language ruled the world, and everything was rosy. You could close your eyes and pretend OpenGL with it's GLSL language just did not exist.

Then a series of events happened, and all of a sudden OpenGL is needed again! iOS and Android are too big to be ignored (which means OpenGL ES + GLSL), and making games for Mac OS X or Linux isn't a crazy idea either. This little WebGL thing that excites many hackers uses a variant of GLSL as well.

**Now we have a problem; we have two similar but subtly different shading languages to deal with**. I wrote about how we [deal with this at Unity](/blog/2010/05/21/compiling-hlsl-into-glsl-in-2010/), and not much has changed since 2010. The "recommended way" is still writing HLSL/Cg, and we cross-compile into GLSL for platforms that need it.

But what about the future?

*It could happen that importance of HLSL (and Direct3D) will decrease over time; this largely depends on what Microsoft is going to do. But just like OpenGL became important again just as it seemed to become irrelevant, so could Direct3D. Or something completely new. I'll assume that for several years into the future, we'll need to deal with at least two shading languages.*

There are several approaches at handling the problem, and several solutions in that space, at varying levels of completeness.


### #1. Do it all by hand!

"Just write all shaders twice". Ugh. That's not "web scale" so we'll just discard this approach.

Slightly related approach is to have a library of preprocessor macros & function definitions, and use them in places where HLSL & GLSL are different. This is certainly doable, take a look at [FXAA](http://timothylottes.blogspot.com/2011/07/fxaa-311-released.html) for a good example. Downsides are, you really need to know all the tiny differences between languages. HLSL's `fmod()` and GLSL's `mod()` sound like they do the same thing, but are subtly different - and there are many more places like this.


### #2. Don't use HLSL nor GLSL: treat them as shader backends

You could go for fully graphical based shader authoring. Drag some nodes around, connect them, and have shader "baking" code that can spit out HLSL, GLSL, or anything else that is needed. This is a big enough topic by iself; graphical shader editing has a lot more uses at "describing material properties" level than it has at lower level (who'd want to write a deferred rendering light pass shader using nodes & lines?).

You could also use a completely different language that compiles down to HLSL or GLSL. I'm not aware of any big uses in realtime graphics, but recent examples could be [Open Shading Language](https://github.com/imageworks/OpenShadingLanguage/) (in film) or [AnySL](http://www.cdl.uni-saarland.de/projects/anysl/) (in research).


### #3. Cross-compile HLSL source into GLSL or vice versa

Parse shader source in one language, produce some intermediate representation, massage / verify that representation as needed, "print" it into another language. Some solutions exist here, for example:

* [hlsl2glslfork](https://github.com/aras-p/hlsl2glslfork) does DX9 HLSL -> GLSL 1.10 / ES 1.00 translation. Used by Unity, and judging from pull requests and pokes I get, in several dozen other game development shops.
* [ANGLE](http://code.google.com/p/angleproject/) does GLSL ES 1.00 -> DX9 HLSL. Used by WebGL implementation in Chrome and Firefox.
* [Cg](http://developer.nvidia.com/cg-toolkit) compiles Cg ("almost the same as HLSL") into various backends, including D3D9 shader assembly and various versions of GLSL, with mixed success. No support for compiling into D3D10+ shader bytecode as far as I can tell.

Big limitation of two libraries above, is that they only do "DX9 level" shaders, so to speak. No support for DX10/11 style HLSL syntax (which Microsoft has changed *a lot*), and no support for correspondingly higher GLSL versions (GLSL 3.30+, GLSL ES 3.00). At least right now.

> **Call to action!**
> There seems to be a need for source level translation libraries for DX10/GL3+ style language syntax & feature sets.
> I'm not sure if it makes sense to extend the above libraries, or to start from scratch... But we need a good quality, open source
> with liberal license, well maintained & tested package to do this. It shouldn't be hard, and it probably doesn't make sense for everyone
> to try to roll their own. **github & bitbucket makes collaboration a snap, let's do it**.

If anyone at Microsoft is reading this: it would *really help* to have formal grammar of HLSL available. "Reference for HLSL" on MSDN has tiny bits and pieces scattered around, but that seems both incomplete and hard to assemble into a single grammar.


A building block could be [Mesa](http://cgit.freedesktop.org/mesa/mesa) or its smaller fork, [GLSL Optimizer](https://github.com/aras-p/glsl-optimizer) (see related [blog post](/blog/2010/09/29/glsl-optimizer/)). It has a decent intermediate representation (IR) for shaders, a bunch of cleanup/optimization/lowering passes, a GLSL parser and GLSL printer (in GLSL Optimizer). Could be extended to parse HLSL and/or print HLSL. Currently lacking most of DX11/GL4 features, and some DX10/GL3 features in the IR. But under active development, so will get those soon I hope.

[MojoShader](http://icculus.org/mojoshader/) also has an in-progress HLSL parser and translator to GLSL.


### #4. Translate compiled shader bytecode into GLSL

Take HLSL, compile it down to bytecode, parse that bytecode and generate corresponding "low level" GLSL. Right now this would only go one way, as GLSL does not have a cross platform "compiled shader" representation. *Though with recent [OpenCL getting SPIR](http://www.khronos.org/news/permalink/khronos-spir-1.0-specification-for-opencl-now-available), maybe there's hope in OpenGL getting something similar in the future?*

This is a lot simpler to do than parsing full high level language, and a ton of platform differences go away (the ones that are handled purely at syntax level, e.g. function overloading, type promotion etc.). A possible downside is that HLSL bytecode might be "too optimized" - all the hard work about register packing & allocation, loop unrolling etc. is not that much needed here. Any conventions like whether your matrices are column-major or row-major is also "baked into" the resulting shader, so your D3D and GL rendering code better match there.

Several existing libraries in this space:

* [MojoShader](http://icculus.org/mojoshader/) translates DX9 shader model 1.1 to 3.0 into GLSL or ARB assembly programs. Used in many Linux game ports and probably somewhere else.
* [James Jones' HLSLCrossCompiler](https://github.com/James-Jones/HLSLCrossCompiler), a recent project on github that translates DX10/11 shader model 4/5 to GLSL 3.30. Seems like in active development, a [blog post about it here](http://jamesjonesdeveloper.com/wordpress/?p=25).
* [Rich Geldreich's fxdis-d3d1x](http://code.google.com/p/fxdis-d3d1x/), a shader model 4/5 bytecode disassembler. Based on Mesa's [D3D11 state tracker](http://cgit.freedesktop.org/mesa/mesa/commit/?id=92617aeac109481258f0c3863d09c1b8903d438b).


### What now?

Go and make solutions to the approaches above, especially #3 and #4! Cross-platform shader developers all around the world will thank you. *All twenty of them, or something ;)*

If you're a student looking for an entry into the industry as a programmer: *this is a perfect example of a freetime / university project*! It's self-contained, it has clear goals, and above all, it's *actually useful* for the real world. A non-crappy implementation of a library like this would almost certainly land you a job at [Unity](http://unity3d.com/company/jobs/overview) and I guess many other places.
