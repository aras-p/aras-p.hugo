---
categories:
- opengl
comments: true
date: 2007-11-08T10:33:53Z
slug: what-opengl-actually-needs
status: publish
title: What OpenGL actually needs
url: /blog/2007/11/08/what-opengl-actually-needs/
wordpress_id: "145"
---

Ok, it looks like OpenGL 3.0 specification [will be delayed](http://www.opengl.org/news/permalink/opengl_arb_announces_an_update_on_opengl_30/) a bit. Oh well, spec now, first drivers a bit later, sort-of-stable drivers a year or two later, and Joe-the-average-user will hopefully have some OpenGL 3.0 support in his Windows box after 5 years. Still, progress has to be made.

The idea of abandoning the old concept of "bind the current object and do stuff on it" and replacing it with direct functions that take object as parameter is very good. Too much state-machine-like functionality in current OpenGL is just a pain for no good reason. Also a very good idea is to make most objects immutable once they are created. Too much flexibility for no good reason just makes the lives of driver developers harder (and gives them much more opportunities to make bugs). All in all, OpenGL's API is becoming more like Direct3D, which is good in my eyes.

What OpenGL needs, besides all the work that goes into OpenGL 3.0? Certainly not [lengthy discussions](http://www.opengl.org/discussion_boards/ubbthreads.php?ubb=showflat&Number=229374) on whether alpha test should be kept or removed _(it does not matter! just pick one)_ or whether shader assembly is actually assembly _(it's not. but current implementations of GLSL are too unusable, so...)_.

What OpenGL needs is implementation quality.

Of all crashes in Unity 1.x web games, close to 100% are inside the dll of OpenGL driver, occurring in totally unpredictable situations. I've yet to see a crash in D3D driver of Unity 2.0 web games. Why is this?

My thinking is because in D3D, quite a chunk of work is done by Microsoft (the D3D runtime). And as it's a component of the OS, they probably try hard to make it stable, and they have WHQL tests at least. It's a somewhat similar situation on the Mac with OpenGL - Apple does the runtime, and IHVs do the drivers. Thus OpenGL in the Mac is _much more_ stable than on Windows (it's not as stable as I'd like it to be, but hey).

Get _someone_ out of whole Khronos conglomerate to write GLSL parsers, format conversions, whatever else that is not directly tied to the hardware. Make it open source if you wish, so that some bugs can be found by mere mortals (instead of waiting indefinitely for IHVs to reply because we're not important enough). Write very extensive testing suites that not just test rasterization rules, but also try to do something more complex than drawing a couple of primitives. The more tests the better. And **make it required** for all implementations to use this common codebase and pass all the tests, otherwise they won't have the right to call themselves "OpenGL".

Oh, and get more games to actually use OpenGL, because right now all drivers have to do is make sure the current id Software engine runs okay :)
