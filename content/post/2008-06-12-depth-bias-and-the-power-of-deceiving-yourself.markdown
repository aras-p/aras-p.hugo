---
categories:
- code
- d3d
- opengl
- unity
- work
comments: true
date: 2008-06-12T08:52:19Z
slug: depth-bias-and-the-power-of-deceiving-yourself
status: publish
title: Depth bias and the power of deceiving yourself
url: /blog/2008/06/12/depth-bias-and-the-power-of-deceiving-yourself/
wordpress_id: "176"
---

In Unity we very often mix fixed function and programmable vertex pipelines. In our lighting model, some amount of brightest lights per object are drawn in pixel lit mode, and the rest are drawn using fixed function vertex lighting. Naturally the pixel lights most often use vertex shaders, as they want to calculate some texcoords for light cookies, or do something with tangent space, or calculate some texcoords for shadow mapping, and so on. The vertex lighting pass uses fixed function, because it's the easiest way. It is possible to implement fixed function lighting equivalent in vertex shaders, but we haven't done that yet because of complexities of Direct3D _and_ OpenGL, the need to support shader model 1.1 and various other issues. Call me lazy.

And herein lies the problem: most often precision of vertex transformations is not the same in fixed function versus programmable vertex pipelines. If you'd just draw some objects in multiple passes, mixing fixed function and programmable paths, this is roughly what you will get (excuse my programmer's art):

[![Mixing fixed function and vertex shaders](http://aras-p.info/blog/wp-content/uploads/2008/06/scenenobias-300x225.png)](http://aras-p.info/blog/wp-content/uploads/2008/06/scenenobias.png)

_Not pretty at all!_ This should have looked like this:

[![All good here](http://aras-p.info/blog/wp-content/uploads/2008/06/scenegoodbias-300x225.png)](http://aras-p.info/blog/wp-content/uploads/2008/06/scenegoodbias.png)

So what do we do to make it look like this? We "pull" (bias) some rendering passes slighly towards the camera, so there is no depth fighting.

Now, at the moment Unity editor runs only on the Macs, which use OpenGL. In there, most of hardware configurations do not need this depth bias at all - they are able to generate same results in fixed function and programmable pipelines. Only Intel cards do need the depth bias on Mac OS X (on Windows, AMD and Intel cards need depth bias). So people author their games using OpenGL, where it does not need depth bias in most cases.

How do you apply depth bias in OpenGL? Enable GL_POLYGON_OFFSET_FILL and set [glPolygonOffset](http://www.opengl.org/documentation/specs/man_pages/hardcopy/GL/html/gl/polygonoffset.html) to something like -1, -1. This works.

How do you apply depth bias in Direct3D 9? _Conceptually_, you do the same. There are [DEPTHBIAS and SLOPESCALEDEPTHBIAS][1] render states that do just that. And so we did use them.

[And people complained](http://forum.unity3d.com/viewtopic.php?t=8443) about funky results on Windows.

And I'd look at their projects, see that they are using something like 0.01 for camera's near plane and 1000.0 for the far plane, and tell them something along the lines of _"increase your near plane, stupid!"_ (well ok, without the "stupid" part). And I'd explain all the above about mixing fixed function and vertex shaders, and how we do depth bias in that case, and how on OpenGL it's often not needed but on Direct3D it's pretty much always needed. And yes, how sometimes that can produce "double lighting" artifacts on close or intersecting geometry, and how the only solution is to increase the near plane and/or avoid close or intersecting geometry.

Sometimes this helped! I was _so convinced_ that their too-low-near-plane was always the culprit.

And then one day I decided to check. This is what I've got on Direct3D:

[![Depth bias artefacts](http://aras-p.info/blog/wp-content/uploads/2008/06/scenebadbias-300x225.png)](http://aras-p.info/blog/wp-content/uploads/2008/06/scenebadbias.png)

Ok, this scene is intentionally using a low near plane, but let me stress this again. This is what I've got:

[![Epic fail!](http://aras-p.info/blog/wp-content/uploads/2008/06/scenebadbiasfail-300x225.png)](http://aras-p.info/blog/wp-content/uploads/2008/06/scenebadbiasfail.png)

_Not good at all._

What happened? It happened in roughly this way:




  1. First, depth bias [documentation][1] on Direct3D is wrong. Depth bias is _not_ in 0..16 range, it is in 0..1 range which corresponds to entire range of depth buffer.
  2. Back then, our code was always using 16 bit depth buffers, so the equivalent of -1,-1 depth bias in OpenGL was multiplied with something like 1.0/65535.0, and that was fed into Direct3D. _Hey, it seemed to work!_
  3. Later on, the device setup code was modified to do proper format selection, so most often it ended up using 24 bit depth buffer. _Of course_ <del>no one</del> I never modified the depth bias code to account for this change...
  4. And it stayed there. And I kept deceiving myself that the content of the users is to blame, and not some stupid code of mine.


**It's good to check your assumptions once in a while.**

So yeah, the proper multiplier for depth bias on Direct3D with 24 bit depth buffer should be not 1.0/65535.0, but something like 1.0/(2^24-1). Except that this value is _really small_, so something like 4.8e-7 should be used instead (see [Lengyel's GDC2007 talk](http://terathon.com/gdc07_lengyel.ppt)). Oh, but for some reason it's not really enough in practice, so something like 2.0*4.8e-7 should be used instead (tested so far on GeForce 8600, Radeon HD 3850, Radeon 9600, Intel 945, reference rasterizer). Oh, and the same value should be used even when a 16 bit depth buffer is used; using 1.0/65535.0 multiplier with 16 bit depth buffer produces way too large bias.

With proper bias values the image is good on Direct3D again. Yay for that (fix is coming in Unity 2.1 soon).

_...and yes, I know that real men fudge projection matrix instead of using depth bias... someday maybe._


[1]: http://msdn.microsoft.com/en-us/library/bb205599(VS.85).aspx
