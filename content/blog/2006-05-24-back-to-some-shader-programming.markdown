---
tags:
- code
- work
comments: true
date: 2006-05-24T20:14:00Z
slug: back-to-some-shader-programming
status: publish
title: Back to some shader programming
url: /blog/2006/05/24/back-to-some-shader-programming/
wordpress_id: "93"
---

There is something magic in programming shaders. Like, when you edit one of our [standard shaders](http://unity3d.com/support/documentation/Components/Built-in%20Shader%20Guide.html) and save, say, nine instructions in it - the feeling is really good. Maybe because, well, it's a standard shader - so that means everyone's graphics will actually render faster. Nice!

Maybe it's because shaders are such a short piece of code, without too complex dependencies... I'm sure anyone who knows graphics hardware will corect me here, but let's oversimplify and pretend that shaders actually execute in a simple way... So when you make a shader shorter, you pretty much know it's going to be faster. When you make it "look better", it almost certainly will look better. Try doing that in your regular big codebase - by optimizing something you may break something else; and in general you have no clue what to optimize unless you do your profiling homework. So, my take is that shaders are much simpler, so the joys of looking at assembly output actually make sense.

So, yeah, I'm back to some shader programming.
