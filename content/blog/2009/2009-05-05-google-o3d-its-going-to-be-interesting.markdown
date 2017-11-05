---
tags:
- rendering
- unity
comments: true
date: 2009-05-05T14:01:24Z
slug: google-o3d-its-going-to-be-interesting
status: publish
title: Google O3D - it's going to be interesting
url: /blog/2009/05/05/google-o3d-its-going-to-be-interesting/
wordpress_id: "317"
---

A couple of weeks ago Google [announced O3D](http://google-code-updates.blogspot.com/2009/04/toward-open-web-standard-for-3d.html): an open source web browser plugin for low level accelerated 3D graphics. The website for O3D project [is here](http://code.google.com/apis/o3d/).

Of course this created some buzz (hey, it's Google after all). And it is in some way a competing technology with [Unity](http://unity3d.com/). I think it's going to be interesting, so I say "welcome competition!"

_Preemptive blah blah: this website is my personal opinion and does not represent the views of my employer, former employers or anyone else other than myself._

Unity is one of the players in "3D on the web" space. 3D graphics in the browser are in fact nothing new. [Unity's browser plugin](http://unity3d.com/unity-web-player-2.x) has existed since 2005 and is now in eight digits installations count. There is [VRML](http://en.wikipedia.org/wiki/VRML), [X3D](http://en.wikipedia.org/wiki/X3D), [Adobe Shockwave](http://en.wikipedia.org/wiki/Adobe_Shockwave), [3DVIA/Virtools](http://en.wikipedia.org/wiki/Virtools), software rendering approaches on top of [Flash](http://en.wikipedia.org/wiki/3D_Flash) and so on.

In my view, major advantages that Unity has compared to O3D:



	
  * It's not only about the graphics. Unity has physics, audio, input, scripting, streaming, networking, asset pipeline and whatnot. O3D is only about the graphics, and at a lower level.

	
  * Unity runs on wider range of hardware. O3D requires Shader Mode 2.0 or later hardware, so about 30% of the "machines on the internet" can't run O3D (based on our [2009Q1 data](http://unity3d.com/webplayer/hwstats/pages/web-2009Q1-shadergen.html)). Couple that with lots of compatibility workarounds that we have and it's probably safe to say that Unity is more _stable and mature_ at this point.

	
  * Unity is not only about the web. There's support for iPhone, Nintendo Wii, standalone games, and with time more console and mobile platforms will come.

	
  * Creating and improving Unity is our primary and only focus as a company. In Google's case, O3D is just another technology in their vast portfolio.


_Of course_, O3D also has advantages:



	
  * It's done by Google! When Google does <del>something</del> anything, people notice immediately :)

	
  * O3D is free and open source. Hard to beat the free price, and open source does have it's benefits. O3D is not a "standard" of any sort right now, but it looks like Google would want it to become one.

	
  * Only focusing on low level graphics has it's benefits: it's lightweight, it appeals to hackers and graphics programmers who want to be in control. Unity's higher level is much easier and faster to use, but low level hacking can be fun.



Of course there are tons of other differences (I might have missed something important as well).

For me as a rendering guy, it's interesting to see O3D taking similar decisions here and there (e.g. they don't use GLSL on OpenGL either because it does not really work in the real world).

So... we'll see where things will go. It's going to be interesting!

