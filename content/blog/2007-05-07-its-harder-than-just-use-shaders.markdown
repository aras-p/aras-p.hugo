---
categories:
- rant
comments: true
date: 2007-05-07T17:17:58Z
slug: its-harder-than-just-use-shaders
status: publish
title: It's harder than "just use shaders"!
url: /blog/2007/05/07/its-harder-than-just-use-shaders/
wordpress_id: "115"
---

I'm always puzzled why people think that some interesting effect is "shaders". The most recent example I saw is [this message](http://nuttybar.drama.uga.edu/pipermail/dir3d-l/2007-May/013005.html) on Dir3d mailing list - it's about NVIDIA's skin rendering demo (*).

The most interesting effects are in fact not achieved by the use of shaders - rather it's the complex interplay of render-to-textures, postprocessing them, using them somewhere else, postprocessing that, using them somewhere else again, combine the intermediate results in various interesting ways, and finally get something that represents the colors on the screen.

So the effect is much more than the shaders; it's more like "an orchestration of different stuff". It's the shaders, textures, models (often with custom data that is required for the effect to work), specially precomputed textures (more custom data for the effect), multiple render textures to store intermediate results, and finally some sort of "script" that controls the whole process.

Shaders are (relatively) small, isolated thingies that operate on small, isolated pieces of data. So they are only a small part of the whole picture, and in fact often they are the easiest part. Both to the effect author, and even for the "engine" to support.

Adding "shader support" to the engine/toolset is a piece of cake. The hard parts are:



	
  * How shaders integrate with the lighting? Once you start doing shaders, you lose _all_ lighting that used to be computed for you.

	
  * How shaders integrate with various data the mesh may have or it might not? For example, to support per-vertex colors you either have to write a single (sub-optimal) shader that always assumes vertex colors are present, or write multiple shaders: when vertex colors are present, and when they are not.

	
  * How shaders integrate with ... lots of other things that were taken for granted in fixed function world. Scaled meshes. Various fog types. Texture transforms. The list goes on.

	
  * How do you expose the rest of the "stuff" that makes interesting effects possible? Shaders alone are nothing. You need render-to-texture with various interesting formats. You need to store custom data in the meshes. You need to store custom data in the textures, in various interesting formats. You need custom cameras to render intermediate results into render textures. You need a way to replace object shaders with some other shaders so that these objects _can_ render these intermediate results. The list goes on.

	
  * Hardware support. Once you enter shader world, you get a totally new set of inconsistencies between operating systems, graphics APIs, hardware vendors, graphics card models and driver versions. And a new bag of driver bugs of course.


So shaders do present three big challenges.

1) Shader explosion. How do you do lighting? Do you say "there shall only ever be a single directional light" (that is what most tech-demos do)? Do you try to support the general case and write some dozen shader combinations? How do you handle all other cases that were "just handled" by fixed function pipeline? Vertex formats, fog, texture transforms, etc. Congratulations, multiply your shader count by 10 or so. _Or_ don't multiply the shader count, but use static/dynamic branching _if_ you have hardware support for it.

2) Platform differences. How you write shaders in the general case, when some things don't work on AMD cards, some other things don't work on NVIDIA cards, yet some other things don't work in OpenGL, some other things don't work in Direct3D, and something entirely else does not work on Mac OS X (with different things not working on PPC vs Intel, yay)? Congratulations again, multiply your already large shader count by 10 or so.

3) Shaders are just a small part of the equation. You need the whole infrastructure around them to _actually_ enable those interesting effects.

To return to the original message about the skin rendering demo: no, "just shaders" won't enable that. You need render textures in several formats (color & depth) at least. And some dozen shaders to do the effect for a single light type, for a single mesh configuration, and for a single hardware platform. Multiply the shader count by, hmm..., hundred or so to cover the general case.

Real life is harder than a tech demo. Real life is more than just the shaders.

(*) I'm taking the message out of the context on purpose. Nothing is wrong with noisecrime's message, I just want to point out that _just_ exposing shaders does not get you very far.
