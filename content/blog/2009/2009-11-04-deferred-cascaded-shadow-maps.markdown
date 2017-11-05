---
tags:
- rendering
- unity
- work
comments: true
date: 2009-11-04T16:42:08Z
slug: deferred-cascaded-shadow-maps
status: publish
title: Deferred Cascaded Shadow Maps
url: /blog/2009/11/04/deferred-cascaded-shadow-maps/
wordpress_id: "434"
---

Reading "[Rendering Technology at Black Rock Studios](http://www.bungie.net/News/content.aspx?type=topnews&link=Siggraph_09)" made me realize that cascaded shadow maps I did 2+ years ago in Unity 2.0 are _probably_ called "deferred shadowing". Since I never wrote how they are done... here:

The process is roughly this (all of this is DX9 level tech on PCs; later tech or consoles could and should use more optimizations):



	
  1. Render shadow map cascades. All of them packed into one shadow map via viewports.

	
  2. Collect shadows into screen sized render target. This is the shadow term.

	
  3. Blur the shadow term.

	
  4. In regular forward rendering, use shadow term in screen space.



More detail:

**Render Shadow Cascades**

Nothing fancy here. All cascades packed into a single shadow map. For example two 512x512 cascades would be packed into 1024x512 shadow map side by side.

**Screen-space Shadow Term**

Render all shadow receivers with a shader that "collects" shadow map term. In effect, shadows from all cascades are collected into a screen-sized texture. After this step, original cascaded shadowmaps are not needed anymore.

Unity supports up to 4 shadow map cascades, which neatly fit into a float4 register in the pixel shader. Correct cascade is sampled just once, _without_ using static or dynamic branching. Pixel shader pseudocode:

 
     float4 near = float4 (z >= _LightSplitsNear);
     float4 far = float4 (z < _LightSplitsFar);
     float4 weights = near * far;
     float2 coord =
         i._ShadowCoord[0] * weights.x +
         i._ShadowCoord[1] * weights.y +
         i._ShadowCoord[2] * weights.z +
         i._ShadowCoord[3] * weights.w;
     float sm = tex2D (_ShadowMapTexture, coord.xy).r;


Additionally, shadow fadeout is applied here (shadows in Unity can be cast up to specified distance from the camera, and they fade out when approaching that distance).

After this I end up having shadow term in screen space. Note that here I do not do any shadow map filtering; that is done in screen space later.

On PCs in DX9 there is (or there was?) no easy/sane way to read depth buffer in the pixel shader, so while collecting shadows the shader also outputs depth packed into two channels of the render target.

**Screen-space Shadow Blur**

Previous step results in screen space shadow term and depth. Shadow term is blurred into another render target, using a spatially varying Poisson disc-like filter.

Filter size depends on depth (shadow boundaries closer to the camera are blurred more). Filter also discards samples if difference in depth is larger _than something_, to avoid blurring over object boundaries. It's not totally robust, but seems to work quite well.

**Using shadow term in forward rendering**

In forward rendering, this blurred shadow term texture is used. Here shadow term already has filtering & fadeout applied, and the shaders do not need to know anything about shadow cascades. Just read pixel from the texture and use it in lighting computation. Done!

**Fin**

Back then I didn't know this would be called "deferred" _(that would probably have scared me away!)_. I don't know if this approach is any good, but so far it works quite well for Unity needs. Also, reduces shader permutation count a lot, which I like.
