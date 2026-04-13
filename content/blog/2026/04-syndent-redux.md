---
title: "Syntonic Dentiforms redux"
date: 2026-04-13T11:33:10+03:00
tags: ['code', 'demos']
---

Some 22 years ago [*nesnausk!*](https://nesnausk.org/) made a demo [Syntonic Dentiforms](/projSynDent.html).
That was 2004! So of course the demo was written for Windows, 32 bit, Direct3D 9, used D3DX Effects Framework,
and was compiled with Visual Studio 6. It used fairly-new at the time pixel shader model 2.0 *(heck yeah!)*, but
also had fallback rendering paths for shader models 1.4 and 1.1. Good times.

[{{<img src="/img/syndent_00.jpg" width="500px">}}](/img/syndent_00.jpg)

Now I took the source code of it, looked at it in horror, and rebuilt it for current platforms.

- Replaced D3D9 / D3DX with [**sokol_gfx**](https://github.com/floooh/sokol?tab=readme-ov-file#sokol_gfxh),
- Replaced FMOD for audio playback with [**sokol_audio**](https://github.com/floooh/sokol?tab=readme-ov-file#sokol_audioh) and [**stb_vorbis**](https://github.com/nothings/stb/blob/master/stb_vorbis.c),
- Instead of Windows / DX9 32 bit, now it compiles for Windows / DX11, Linux / OpenGL, macOS / Metal (all 64 bit),
  as well as Web (Emscipten / WebGL2).
- Replaced Object-ID based shadowing with regular shadow maps, using Castaño's
  [5x5 PCF filter](https://www.ludicon.com/castano/blog/articles/shadow-mapping-summary-part-1/),
- All lighting is now per-pixel (previously reflections were lit per-vertex), lighting vectors are normalized
  more properly and the reflections are anti-aliased.

Here are the builds and the source code:

- [**Web build here**](https://aras-p.github.io/SyntonicDentiforms/) *(please do not run on a phone in portrait orientation, thx)*
- PC builds: [Windows](https://aras-p.info/files/demos/2026/SyntonicDentiforms-windows.zip),
  [Linux](https://aras-p.info/files/demos/2026/SyntonicDentiforms-linux.zip) and
  [macOS](https://aras-p.info/files/demos/2026/SyntonicDentiforms-mac.zip), 6MB each,
- Source code [repo on github](https://github.com/aras-p/SyntonicDentiforms).
- Video capture on youtube: <br/>
  {{< youtube id="Qlelznjdgws" >}}

#### Musings on source code

This made me realize that the code I was writing 22 years ago has been *really bad*, judging by my today's
standards. *So. much. pointless. abstractions. and. design. patterns. and. inheritance.* Out of curiosity,
I tried rewriting the parts of the code that I understand (there are some that I don't; I left them as they are),
just to see how much simpler and smaller the code can get.

For example everything related to "animations" initially [was this](https://github.com/aras-p/SyntonicDentiforms/tree/13228979/dingus/dingus/animator): 16 files, with interfaces, and listeners, and traits, and whatever.
`IAnimChannel`, `CAnimChannel<T>`, `CAnimContext`, `CAnimCurve<T>`, `CAnimImmediateMixer<T>`,
`IAnimListener<T>`, `IAnimStream<T>`, `CAbstractTimedAnimStream<T>`, `CAnimStreamMixer<T>`,
`traits::anim_type<T>`, `IAnimation<T>`, `CAnimationBunch`, `CSampledAnimation<T>`, `CTimedAnimStream<T>` --
just, *whyyyy*. All of that can be simplified into [two files](https://github.com/aras-p/SyntonicDentiforms/tree/a3aee4f48/src/animator) with way fewer parts (`AnimCurve`, `SampledAnimation`, `AnimationBunch`).
Same story with "graphics" or "resource loading" related parts.

So, what was 24 thousand lines of code across 216 source files, became 6 thousand lines of code across 49 files.
Does anyone care? *No, of course not.* But I did it anyway :)

The executable became a megabyte smaller, by the way. Mostly because it was using D3DX (effects framework,
texture loading, math), and I replaced them with other, smaller, libraries that do less stuff.
I ❤️ [**sokol**](https://github.com/floooh/sokol) libraries by Andre Weissflog; they are simple,
straight to the point, and let me get this working across all of windows/linux/mac/web. It is funny
that back then, Andre's Nebula Device game engine design was pretty influential for us, with all the abstractions
and object-orientation. Sokol is almost *complete opposite*, and I love that.

[{{<img src="/img/syndent_08.jpg" width="500px">}}](/img/syndent_08.jpg)

The demo has a special place in my heart since this is the first "not complete shit" demo that I worked on :)
We also managed to get a scene.org Breakthrough Performance award for it! Ren here is completely unfazed by
the award though. She shows that awards are just a social construct.

[{{<img src="/img/blog/2026/scene_award_ren.jpg" width="500px">}}](/img/blog/2026/scene_award_ren.jpg)
