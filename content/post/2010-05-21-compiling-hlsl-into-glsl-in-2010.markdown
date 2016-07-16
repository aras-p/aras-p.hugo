---
categories:
- code
- d3d
- opengl
- unity
comments: true
date: 2010-05-21T21:59:38Z
slug: compiling-hlsl-into-glsl-in-2010
status: publish
title: Compiling HLSL into GLSL in 2010
url: /blog/2010/05/21/compiling-hlsl-into-glsl-in-2010/
wordpress_id: "523"
---

Realtime shader languages these days have settled down into two camps: HLSL (or Cg, which for all practical reasons is the same) and GLSL (or GLSL ES, which is sufficiently similar). HLSL/Cg is used by Direct3D and the big consoles (Xbox 360, PS3). GLSL/ES is used by OpenGL and pretty much all modern mobile platforms (iPhone, Android, ...).

Since shaders are more or less "assets", having two different languages to deal with is not very nice. What, I'm supposed to write my shader twice just to support both (for example) D3D and iPad? You would think in 2010, almost a decade since high level realtime shader languages have appeared, this problem would be solved... but it isn't!

In [upcoming Unity 3.0](http://unity3d.com/unity/coming-soon/unity-3), we're going to have OpenGL ES 2.0 for mobile platforms, where GLSL ES is the only option to write shaders in. However, almost all other platforms (Windows, 360, PS3) need HLSL/Cg.

I tried a bit making [Cg](http://developer.nvidia.com/object/cg_toolkit.html) spit out GLSL code. In theory it can, and I read somewhere that [id](http://en.wikipedia.org/wiki/Id_Software) uses it for OpenGL backend for [Rage](http://en.wikipedia.org/wiki/Rage_(video_game))... But I just couldn't make it work. What's possible for [John](http://en.wikipedia.org/wiki/John_Carmack) apparently is not possible for mere mortals.

Then I looked at ATI's [HLSL2GLSL](https://github.com/aras-p/hlsl2glslfork). That did produce GLSL shaders that were not absolutely horrible. So I started using it, and _(surprise!)_ quickly ran into small issues here and there. Too bad development of the library stopped around 2006... on the plus side, it's open source!

So I just forked it. Here it is: [**http://code.google.com/p/hlsl2glslfork/**](http://code.google.com/p/hlsl2glslfork/) ([commit log here](https://github.com/aras-p/hlsl2glslfork/commits/master)). There are no prebuilt binaries or source drops right now, just a Mercurial repository. BSD license. Patches welcome.

_Note on the codebase_: I don't particularly like the codebase. It seems somewhat over-engineered code, that was probably taken from reference GLSL parser that 3DLabs once did, and adapted to parse HLSL and spit out GLSL. There are pieces of code that are unused, unfinished or duplicated. Judging from comments, some pieces of code have been in the hands of 3DLabs, ATI and NVIDIA (what good can come out of _that_?!). However, it _works_, and that's the most important trait any code can have.

_Note on the preprocessor_: I bumped into some preprocessor issues that couldn't be easily fixed without first understanding someone else's ancient code and then changing it significantly. Fortunately, Ryan Gordon's project, [MojoShader](http://icculus.org/mojoshader/), happens to have preprocessor that very closely emulates HLSL's one (including various quirks). So I'm using that to preprocess any source before passing it down to HLSL2GLSL. Kudos to Ryan!

_Side note on MojoShader_: Ryan is also working on HLSL->GLSL cross compiler in MojoShader. I like that codebase much more; will certainly try it out once it's somewhat ready.

_You can never have enough notes_: Google's [ANGLE project](http://code.google.com/p/angleproject/) (running OpenGL ES 2.0 on top of Direct3D runtime+drivers) seems to be working on the opposite tool. For obvious reasons, they need to take GLSL ES shaders and produce D3D compatible shaders (HLSL or shader assembly/bytecode). The project seems to be moving fast; and if one day we'll decide to default to GLSL as shader language in Unity, I'll know where to look for a translator into HLSL :)
