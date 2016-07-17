---
tags:
- d3d
- gpu
- mobile
- opengl
- rendering
comments: true
date: 2012-10-17T00:00:00Z
title: Non Power of Two Textures
url: /blog/2012/10/17/non-power-of-two-textures/
---

Support for non power of two ("NPOT", i.e. arbitrary sized) textures has been in GPUs for quite a while, but the state of support can be confusing. Recent question from [@rygorous](http://twitter.com/rygorous):

> Lazyweb, <...> GL question: <...> ARB NPOT textures is fairly demanding, and texture rectangle
> are awkward. Is there an equivalent to ES2-style semantics in regular GL? Bonus Q: Is there something
> like the Unity HW survey, but listing supported GL extensions? :)

There are generally three big types of texture size support:

* Full support for arbitrary texture sizes. This includes mipmaps, all texture wrap & filtering modes, and most often compressed texture formats as well.
* "Limited" support for non-power-of-two sizes. No mipmaps, texture wrap mode has to be Clamp, but does allow texture filtering. This makes such textures not generally useful in 3D space, but just good enough for anything in screen space (UI, 2D, postprocessing).
* No support, texture sizes have to be powers of two (16,32,64,128,...). If you're running on really, *really* old GPU then textures might also need to be square (width = height).


### Direct3D 9

Things are quite simple here. [D3DCAPS9.TextureCaps](http://msdn.microsoft.com/en-us/library/windows/desktop/bb172513.aspx) has capability bits, `D3DPTEXTURECAPS_POW2` and `D3DPTEXTURECAPS_NONPOW2CONDITIONAL` both being *off* indicates full support for NPOT texture sizes. When both `D3DPTEXTURECAPS_POW2` and `D3DPTEXTURECAPS_NONPOW2CONDITIONAL` bits are *on*, then you have limited NPOT support.

*I've no idea what would it mean if NONPOW2CONDITIONAL bit is set, but POW2 bit is not.*

Hardware wise, limited NPOT has been generally available since 2002-2004, and full NPOT since 2006 or so.


### Direct3D 11

Very simple; feature level 10_0 and up has full support for NPOT sizes; while feature level 9_x has limited support for NPOT. [MSDN link](http://msdn.microsoft.com/en-us/library/windows/desktop/ff476876.aspx).


### OpenGL

Support for NPOT textures has been a core OpenGL feature since 2.0; promoted from earlier [ARB_texture_non_power_of_two](http://www.opengl.org/registry/specs/ARB/texture_non_power_of_two.txt) extension. The extension specifies "full" support for NPOT, including mipmaps & wrapping modes. There's no practical way to detect hardware that can only do "limited" NPOT textures.

However, in traditional OpenGL spirit, presence of something in the API does not mean it can run on the GPU... E.g. Mac OS X with [Radeon X1600 GPU is an OpenGL 2.1+](https://developer.apple.com/graphicsimaging/opengl/capabilities/) system, and as such pretends there's full support for NPOT textures. In practice, as soon as you have NPOT size with mipmaps or a non-clamp wrap mode, you drop into software rendering. *Ouch.*

A rule of thumb that seems to work: try to detect "DX10+" level GPU, and in that case assume full NPOT support is *actually* there. Otherwise, in GL2.0+ or when `ARB_texture_non_power_of_two` is present, only assume *limited* NPOT support.

Then the question of course is, how to detect DX10+ level GPU in OpenGL? If you're using OpenGL 3+, then you are on DX10+ GPU. In earlier GL versions, you'd have to use some heuristics. For example, if you have `ARB_fragment_program` and `GL_MAX_PROGRAM_NATIVE_INSTRUCTIONS_ARB` is less than 4096 is a pretty good indicator of pre-DX10 hardware, on Mac OS X at least. Likewise, you could query `MAX_TEXTURE_SIZE`, lower than 8192 is a good indicator for pre-DX10.


### OpenGL ES

OpenGL ES 3.0 has full NPOT support in core; ES 2.0 has limited NPOT support (no mipmaps, no Repeat wrap mode) in core; and ES 1.1 has no NPOT support.

For ES 1.1 and 2.0, full NPOT support comes with `GL_ARB_texture_non_power_of_two` or `GL_OES_texture_npot` extension. In practice, iOS devices don't support this; and on Android side there's support on Qualcomm Adreno and ARM Mali. Possibly some others.

For ES 1.1, limited NPOT support comes with `GL_APPLE_texture_2D_limited_npot` (all iOS devices) or `GL_IMG_texture_npot` (some ImgTec Android devices I guess).

WebGL and Native Client currently are pretty much OpenGL ES 2.0, and thus support limited NPOT textures.



### Flash Stage3D

Sad situation here; current version of Stage3D (as of Flash Player 11.4) has no NPOT support whatsoever. Not even for render targets.



### Consoles

I wouldn't be allowed to say anything about them, now would I? :) Check documentation that comes with your developer access on each console.



### Is there something like the Unity HW survey, but listing supported GL extensions?

Not a single good resource, but there are various bits and pieces:

* [Unity Web Player hardware stats](http://unity3d.com/webplayer/hardware-stats) - no GL extensions, but we do map GPUs to "DX-like" levels and from there you can sort of infer things.
* [Steam Hardware Survey](http://store.steampowered.com/hwsurvey) - likewise.
* [Apple OpenGL Capabilities](https://developer.apple.com/graphicsimaging/opengl/capabilities/) - tables of detailed GL info for recent OS X versions and all GPUs supported by Apple. Excellent resource for Mac programmers! Apple does remove pages of old OS X versions from there, you'd have to use [archive.org](http://archive.org/) to get them back.
* [GLBenchmark Results](http://www.glbenchmark.com/result.jsp) - results database of GLBenchmark, has list of OpenGL ES extensions and some caps bits for each result ("GL config." tab).
* [Realtech VR GLView](http://www.realtech-vr.com/) - OpenGL & OpenGL ES extensions and capabilities viewer, for Windows, Mac, iOS & Android.
* [Omni Software Update Statistics](http://update.omnigroup.com/) - Mac specific, but does list some OpenGL stats from "Graphics" dropdown.
* [KluDX](http://kludx.com/) - Windows app that shows information about GPU; also has reports of submitted data.
* [0 A.D. OpenGL capabilities database](http://feedback.wildfiregames.com/report/opengl/) - OpenGL capabilities submitted by players of 0 A.D. game.
