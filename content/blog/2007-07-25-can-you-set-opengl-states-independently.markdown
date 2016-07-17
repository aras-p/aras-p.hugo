---
tags:
- opengl
- rant
- work
comments: true
date: 2007-07-25T22:50:11Z
slug: can-you-set-opengl-states-independently
status: publish
title: Can you set OpenGL states independently?
url: /blog/2007/07/25/can-you-set-opengl-states-independently/
wordpress_id: "128"
---

Most of the time, yes, you can just set the needed states! You can set alpha blending on and turn light #0 off, and often nothing bad will happen. Blending will be on, and light #0 will be off. Fine.

Until you hit a graphics card (quite new - from 2006, it can even do pixel shader 2.0) that completely hangs up the machine in one of your unit tests. In fact, in the first unit test, that does almost nothing. Debugging that thing is _total awesomeness_ - try something out, and the machine either hangs up or it does not. Reboot, repeat.

After something like 30 hang-ups I found the cause: _you are damned_ if you set GL_SEPARATE_SPECULAR_COLOR and GL_COLOR_SUM to different values (i.e. use separate specular but don't turn on color sum). Because, you know, some code was there that did not see a point in changing light mode color control when no lighting was on. So yeah, always set those two in sync. Just to please this card's drivers.

It's hard for me to have any faith in driver developers. I know that their job is hard, walking the fine line between correctness and getting decent benchmark scores... But still - hanging up the machine when two OpenGL 1.2 states are set to different values? Would you trust those people to write [full fledged compilers](http://www.opengl.org/documentation/glsl)?
