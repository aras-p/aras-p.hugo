---
categories:
- gpu
comments: true
date: 2005-12-22T14:06:00Z
slug: speculation-pipelining-geometry-shaders
status: publish
title: 'Speculation: pipelining geometry shaders'
url: /blog/2005/12/22/speculation-pipelining-geometry-shaders/
wordpress_id: "82"
---

A followup to the older "[discussion](/blog/2005/12/16/reading-dx10-docs/)" about how/why geometry shaders would be okay/slow:

The graphics hardware has been quite successful so far at hiding memory latencies (i.e. when sampling textures). It does so (according to my understanding) by having a looong pixel pipeline, where hundreds (or thousands) pixels might be at one or another processing stage. ATI talks about this in big letters ([R520 dispatch processor](http://www.beyond3d.com/reviews/ati/r520/)) and speculations suggest that GeForceFX had something like that ([article](http://www.extremetech.com/article2/0,3973,710337,00.asp)). I have no idea about the older cards, but presumably they did something similar as well.

I am not sure how the vertex texture fetches are pipelined - pretty slow performance on GeForce6/7 suggest that they aren't :) Probably vertex shaders in current cards operate in a simpler way - just fetch the vertices and run whole shaders on them (in contrast to pixel shaders, which seem to run just several instructions, then go to another pixels, return back, etc.).

With DX10, we have arbitrary memory fetches in any stage of the pipeline. Even the boundary between different fetch types is somewhat blurry (constant buffers vs. arbitrary buffers vs. textures) - perhaps they will differ only in bandwidth/latency (e.g. constant buffers live near the GPU while textures live in video memory).

So, with arbitrary memory fetches anywhere (and some of them being high latency), everything needs to have long pipelines (again, just my guess). This is all great, but the longer the pipeline, the worse it performs in non-friendly scenarios: pipeline flush is more expensive, drawing just a couple of "things" (primitives, vertices, pixels) is inefficient, etc.

I guess we'll just learn a new set of performance rules for tomorrow's hardware!

Back to GS pipelining: I imagine that the "slow" scenarios would be like this: vertices have shaders with dynamic branches or memory fetches differing vastly in execution lengths - so GS has to wait for all vertex shaders of the current primitive (optional: plus topology) to finish; and then each GS has dynamic branches or memory fetches, and outputs different number of primitives to the rasterizer. If I'd were hardware, I'd be scared :)

