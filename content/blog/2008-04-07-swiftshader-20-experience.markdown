---
tags:
- gpu
- rendering
comments: true
date: 2008-04-07T14:05:09Z
slug: swiftshader-20-experience
status: publish
title: SwiftShader 2.0 experience
url: /blog/2008/04/07/swiftshader-20-experience/
wordpress_id: "165"
---

ShiftShader 2.0, a pure software renderer with a Direct3D 9 interface, [just got released](http://www.transgaming.com/products/swiftshader/). I tried it on rendering unit tests and some benchmark tests we have for Unity.

In short, I'm impressed.

It runs rendering tests almost correctly; the only minor bugs seem to be somewhere in attenuation of fixed function vertex lights. Everything else, including shaders, shadows, render to texture works without any problems.

Performance wise, of course it's dozens to hundreds times slower than a _real_ graphics card, but hey. I also tested with Intel 965 (aka GMA X3000) integrated graphics for comparison. All this on Intel Core2 Quad (Q6600), 3 GB RAM, Windows XP SP2.




  * [Avert Fate demo](http://unity3d.com/gallery/live-demos/avert-fate): Radeon HD 3850 about 300 FPS, SwiftShader about 5 FPS (about 15 FPS if per-pixel lighting is turned off), Intel 965 about 22 FPS (about 50 FPS if per-pixel lighting is turned off).


  * Scene with lots of objects and lots of shadow-casting lights: Radeon HD 3850 about 76 FPS, SwiftShader 2.5 FPS, Intel - _shadows not supported, duh_.


  * High detail terrain with lots of vegetation and four cameras rendering it simultaneously: Radeon HD 3850 about 68 FPS, SwiftShader about 3 FPS, Intel 965 about 12 FPS.



Ok, so SwiftShader loses on performance to Intel 965, but the difference is only "a couple of times", and not in order of magnitude or so. Pretty good I'd say.
