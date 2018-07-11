---
tags:
- gpu
- rendering
comments: true
date: 2012-03-27T17:35:46Z
slug: tiled-forward-shading-links
status: publish
title: Tiled Forward Shading links
url: /blog/2012/03/27/tiled-forward-shading-links/
wordpress_id: "821"
---

Main idea of my [previous post](/blog/2012/03/02/2012-theory-for-forward-rendering/) was roughly this: in forward rendering, there's no reason why we still have to use per-object light lists. We can apply roughly the same ideas as those of tiled deferred shading.

Really nice to see that other people have thought about this before or about the same time; here are some links:




  * [Tiled Shading](http://www.cse.chalmers.se/~olaolss/main_frame.php?contents=publication&id=tiled_shading) by Ola Olsson and Ulf Assarsson; Journal of Graphics Tools. PDF, source code and comparisons between tiled forward & tiled deferred. _Update:_ "clustered shading" was published since then, see next item.


  * [Clustered Deferred and Forward Shading](http://www.cse.chalmers.se/~olaolss/main_frame.php?contents=publication&id=clustered_shading) by Ola Olsson , Markus Billeter and Ulf Assarsson. Takes ideas from tiled shading and brings them to the next level.


  * [Forward+: Bringing Deferred Lighting to the Next Level](http://developer.amd.com/gpu_assets/AMD_Demos_LeoDemoGDC2012.ppsx) by Takahiro Hirada, Jay McKee, Jason C. Yang; GDC 2012. This describes AMD's Leo demo. There's an incomplete Eurographics 2012 [paper here](https://sites.google.com/site/takahiroharada/).


  * [Tile-Based Forward Rendering](http://pjblewis.com/site/posts/2012-03-25-tile-based-forward-rendering.html) by Peter J. B. Lewis. Implementation without using a Compute Shader (but uses other DX11 features like UAVs).


  * [Light Indexed Deferred Rendering](http://mynameismjp.wordpress.com/2012/03/31/light-indexed-deferred-rendering/); new implementation by Matt "MJP" Pettineo. Includes performance comparisons with tiled deferred rendering.


  * Very similar in approach is of course [Light Indexed Deferred Rendering](http://code.google.com/p/lightindexed-deferredrender/) by Damian Trebilco.



As [Andrew Lauritzen](http://portfolio.punkuser.net/) points out in the [comments of my previous post](/blog/2012/03/02/2012-theory-for-forward-rendering/#comment-179964), claiming "but deferred will need super-fat G-buffers!" is an over-simplification. You could just as well store material indices plus data for sampling textures (UVs + derivatives); and going "deferred" you have more choices in how you schedule your computations.

There's no principal difference between "forward" and "deferred" these days. As soon as you have a Z-prepass you already are caching/deferring _something_, and then it's a whole spectrum of options what and how to cache or "defer" for later computation.

Ultimately of course, the best approach depends on a million of factors. The only lesson to learn from this post is that "forward rendering does not have to use per-object light lists".
