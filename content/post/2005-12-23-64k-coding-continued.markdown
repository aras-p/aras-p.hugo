---
categories:
- code
- demos
comments: true
date: 2005-12-23T18:58:00Z
slug: 64k-coding-continued
status: publish
title: 64k coding continued
url: /blog/2005/12/23/64k-coding-continued/
wordpress_id: "83"
---

I'm making a steady, but very slow progress on "my" [64k intro](/blog/2005/11/03/a-crazy-thought-64k-intro). Over the last week I couldn't get over 13 kilobytes, so you can see that the progress is really slow. Not because I don't code anything, but all code increase was cancelled by data size optimizations.

So far coding and data design for small sizes is not that much pain at all. Just, well, code and, well, keep your data small :) We're only talking about the size of initial data, not the runtime size though.

A few obvious or new notes:


  * Code to construct a cylinder is more complex than the one to construct a sphere. That's what I expected. However, code to construct a box with multiple segments per side is the most complex of all!
  * Dropping last byte from floats is usually okay. And instant 25% save! For some of the numbers, I plan to switch to half-style float (2 bytes) if space becomes a concern.

  * Storing quaternions in 4 bytes (byte per component) is good. Actually, now that I think of it, it makes more sense to store three components at 10 bits each, and just store the sign of 4th component - better precision for the same size.
  * This intro literally has the most complex and most automated "art pipeline" of any demo/game I (directly) worked on! I've got maxscripts generating C++ sources, custom commandline tools preprocessing C++ sources (mostly floats packing - due to lack of maxscript functionality), lua scripts for batch-compiling HLSL shaders, "development code" generating .obj models for import back into max, etc. It's wacky, weird and cool!
  * Compiling HLSL in two steps (HLSL->asm and asm->bytecode) instead of direct (HLSL->bytecode) gets rid of the constant table, some copyright strings and hence is good. (thanks [blackpawn](http://www.blackpawn.com/blog/?p=6)!)
  * Getting FFD code to behave remotely similar to how 3dsmax does FFD is hard :)

The best thing so far is that I've got the music track from [x_dynamics](http://www.x-dynamics.de.vu/) - it's already done in V2 synth, takes small amount of space and is really good. Now I "just" have to finish the intro...

