---
tags:
- opengl
- rant
comments: true
date: 2008-08-20T11:28:03Z
slug: opengl-3-a-big-step-in-no-direction-at-all
status: publish
title: 'OpenGL 3: a big step in no direction at all?'
url: /blog/2008/08/20/opengl-3-a-big-step-in-no-direction-at-all/
wordpress_id: "195"
---

Well, the post title pretty much summarizes my take on it, doesn't it? I guess I could just stop typing now... but I won't!

So after some promises, delays and a period of deadly silence, OpenGL 3.0 was [released](http://www.khronos.org/news/press/releases/khronos_releases_opengl_30_specifications_to_support_latest_generations_of/).

Response to it was "[interesting](http://www.opengl.org/discussion_boards/ubbthreads.php?ubb=showflat&Number=243193)", to say at least. Some part of that response is related to seriously mishandled communication on Khronos part. Some part is because GL 3.0 is not what it was promised to be. Let's just ignore the communication issue, it does not affect OpenGL _itself_ in a direct way (it affects the developer community though).

_By the way, I borrowed part of the post title from a [blog post](http://fireuser.com/blog/opengl_30_a_big_step_in_the_right_direction/) linked from opengl.org. In general, I do not agree with that blog post, but it's a valid point of view. Unlike some other [blog posts](http://zerias.blogspot.com/2008/08/why-fud-against-opengl-30.html) linked from opengl.org that are just pure garbage..._

I am not sure what are the goals of OpenGL at this point. OpenGL's current position, as far as games are concerned, seems to be roughly this:


> Be the graphics API on various platforms where no alternatives are available.



Why? Because Windows has got D3D, which is far more stable, comes with useful tools, more often updated and actually works for variety of users (I'll get to this point in a second). Mobile platforms have OpenGL ES, which is decent. All consoles have their own APIs (some of them similar to D3D, _none_ of them similar to GL). So that leaves OpenGL as the choice on OS X, Linux and such. Not because it's better. Because it's the only choice.

_"Oh, but look, [id](http://www.idsoftware.com/) uses OpenGL! Two other games use OpenGL as well!"_ Well, good for them. But they are in a different league than "the rest of us". For _some games_, driver writers will do whatever it takes to get those games running correct & fast. Surprise surprise, id games fall into this category. For the rest of us - no such luxury. Hey, try talking to your friendly IHV, the most likely answer is _"yeah, but are really busy with some high profile games right now, ping us back in two months"_. After two months, repeat.

So the rest comes from somone who is _not_ working on the high-profile games that IHVs specially tune drivers to.

If OpenGL's goals are to stay in this current position, then GL 3.0 is okay. It adds some new features, brings some extensions into core, hey, it even says "it's quite likely that maybe perhaps someday some of the old cruft in the API will be removed, if we feel like it". No problem with that.

However, OpenGL is advertised as something different, as if it wants to:


> Be **the** graphics API on **various platforms**.



Which is quite different from it's current position. I'm not sure if that's the goal of OpenGL. Myself, I don't care about the mythical cross-platform API that would _actually work_ on those different platforms. API is a tool to do stuff; if different platforms have different APIs - no problem with that.

However, if OpenGL _wants_ to achieve this advertised goal, it has to do several things. First and foremost:

**Actually work**

Stable drivers and runtime. In it's current state, GL is too complex to implement good quality drivers/runtime. Complexity can be reduced in several ways:




  * Cleanup the API. This was what GL 3.0 was supposed to be. Actual 3.0 did not do any of that, instead it just postponed the cleanup "until we feel like it".


  * Share some of the hard work. Why does everyone and their dog have to write GLSL preprocessor, lexer, parser and basic optimizer themselves? Define precompiled shader format, write frontend once, make it open. This would also be actually useful to reduce load times.



GL 3.0 could have done both of the above, instead it did none. It could have cleaned up the API, and provide one platform independent GL 1.x/2.x library that calls into actual 3.0 runtime. All the fixed function, immediate mode, display lists, whatever would be in one nice library. Even existing apps could continue to function transparently this way (with the benefit of actually simpler = more stable drivers).

**Support platforms/hardware/features user needs**

This is of course dependent on the user in question. For someone like [us](http://unity3d.com/), we still have to support 10 year old hardware.

D3D9 does a fine job for that (provided you have drivers installed, and DX9 runtime installed - which comes included in XP SP2 and upwards). OpenGL 2.1 and earlier would do a fine job for that, provided it would "actually work" (see above).

If GL 3.0 would be as was originally promised - almost new API, shader model 2.0+ hardware, it would be sort of fine. In our case, that would mean writing and supporting two renderers - "old GL" and "new GL", where old one would be used on old hardware or old platforms where "new GL" is not available. If the new runtime were much leaner, much more stable and generally nicer, this would not be a big problem.

With actual GL 3.0, in theory one does not have to write two renderers. Minimum hardware level for GL 3.0 is shader model 4+ though. So to support both old hardware/platforms and new hardware/platforms, quite a lot of duplication has to be done. Especially if you intend to go towards proposed "future GL path", i.e. start dropping deprecated functionality from the codebase. At which point you'll probably write two separate renderers already. So we're back to where original GL 3.0 would have been, just without any extra niceness/stability/leanness right now.

Oh, and look at [vendor announcements](http://www.khronos.org/library/detail/2008_siggraph_opengl_bof_slides/) from 2008 OpenGL BOF. NVIDIA: we have almost full drivers now. AMD: we're committed to having drivers. Intel: look for GL 3.0 on future platforms. In other words, looks like current Intel's cards won't ever have GL 3.0 drivers. And in our target market, Intel has the majority of cards.

That sounds very much like "just ignore whole GL 3.0 thing" plan to me.

**Be nice**

This is a point of far lesser importance than "actually work" and "support what is needed" ones. Having good tools (PIX, ...), documentation, code examples etc. is nice. But not much more; being nicest API in the world does not do much if it does not actually work or does not support what you need. Even in this area, actual GL 3.0 is _not_ nice - it's full of redundancies and crap that goes 15 years back in history.


**Summing it up**

To me, GL 3.0 looks like a blunder. Instead of fixing the core problems, they just postponed that. Well, _Keep up the good work!_
