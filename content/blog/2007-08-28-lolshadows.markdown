---
tags:
- random
- unity
- work
comments: true
date: 2007-08-28T20:53:13Z
slug: lolshadows
status: publish
title: Lolshadows!
url: /blog/2007/08/28/lolshadows/
wordpress_id: "134"
---

In this age of the interwebs we have [Lolcats](http://en.wikipedia.org/wiki/Lolcat), we even have [LOLCODE](http://en.wikipedia.org/wiki/LOLCODE)... why can't we have Lolshadows?

![CAN I HAS SHADOWS? PLZ?](/img/unity/CanIHasShadows.jpg)

This is actually me debugging point light shadows (that happen to use depth encoded into RGBA8 cubemaps).

![OMG ITS POISSON!](/img/unity/PoissonedShadows.jpg)

This is what happens when you use a too wide Poisson disc blurring in screen space _and_ no prevention of "shadow leakage" over different depths.

LOL! Internet!
