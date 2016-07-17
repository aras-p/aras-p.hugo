---
tags:
- code
- gpu
- rendering
comments: true
date: 2010-01-07T16:27:55Z
slug: screenspace-vs-mip-mapping
status: publish
title: Screenspace vs. mip-mapping
url: /blog/2010/01/07/screenspace-vs-mip-mapping/
wordpress_id: "485"
---

_Just spent half a day debugging this, so here it is for the future reference of the internets._

In a deferred rendering setup (see [Game Angst](http://gameangst.com/?p=141) for a good discussion of deferred shading & lighting), lights are applied using data from screen-space buffers. Position, normal and other things are reconstructed from buffers and lighting is computed "in screen space".

Because each light is applied to a portion of the screen, the pixels it computes can belong to different objects. If in any place of lighting computation you use textures with [mipmaps](http://en.wikipedia.org/wiki/Mipmap), _be careful_. Most common use for mipmapped light textures is light "cookies" (aka "gobo").

Let's say we have a very simple scene with a spot light:

[![](http://aras-p.info/blog/wp-content/uploads/2010/01/DeferredCookieGood.png)](http://aras-p.info/blog/wp-content/uploads/2010/01/DeferredCookieGood.png)

Light's angular attenuation comes from a texture like this:

[![](http://aras-p.info/blog/wp-content/uploads/2010/01/cookie128.png)](http://aras-p.info/blog/wp-content/uploads/2010/01/cookie128.png)

If the texture has mipmaps and you sample it using the "obvious" way (e.g. tex2Dproj), you can get something like this:

[![](http://aras-p.info/blog/wp-content/uploads/2010/01/DeferredCookieBad.png)](http://aras-p.info/blog/wp-content/uploads/2010/01/DeferredCookieBad.png)

_Black stuff around the sphere is no good!_ It's not the infamous half-texel offset in D3D9, not a driver bug, not a shader compiler bug and not the nature trying to prevent you from writing a deferred renderer.

It's the mipmapping.

Mipmaps of your cookie texture look like this (128x128, 16x16, 8x8, 4x4 shown):

![](http://aras-p.info/blog/wp-content/uploads/2010/01/cookie128.png)![](http://aras-p.info/blog/wp-content/uploads/2010/01/cookie16.png)![](http://aras-p.info/blog/wp-content/uploads/2010/01/cookie8.png)![](http://aras-p.info/blog/wp-content/uploads/2010/01/cookie4.png)

Now, take two adjacent pixels, where one belongs to the edge of the sphere, and the other belongs to the background object (technically you take a 2x2 block of pixels, but just two are enough to illustrate the point). When the light is applied, cookie texture coordinates for those pixels are computed. It can happen that the coordinates are _very_ different, especially when pixels "belong" to entirely different surfaces that are quite far away from each other.

What the GPU does when texture coordinates of adjacent pixels are very different? Chooses a lower mipmap level so that texel to pixel density roughly matches 1:1. On the edges of this "wrong" screenshot, it happens that very small mipmap level is sampled, which is either black or white color (see 4x4 mip level).

What to do here? You could disable mip-mapping (which is not good for performance and not good for image quality). You could drop some smallest mip levels which might be enough and not that bad for performance. Another option is to manually supply LOD level or derivatives to sampling instructions, using _something else_ than cookie texture coordinates. For example, derivative in view space position, or something like that. This might not be possible on lower shader models though.
