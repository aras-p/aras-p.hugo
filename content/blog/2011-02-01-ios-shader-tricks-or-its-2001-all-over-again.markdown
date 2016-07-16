---
categories:
- code
- gpu
- mobile
- opengl
- rendering
comments: true
date: 2011-02-01T09:43:57Z
slug: ios-shader-tricks-or-its-2001-all-over-again
status: publish
title: iOS shader tricks, or it's 2001 all over again
url: /blog/2011/02/01/ios-shader-tricks-or-its-2001-all-over-again/
wordpress_id: "592"
---

I was recently optimizing some OpenGL ES 2.0 shaders for iOS/Android, and it was funny to see how performance tricks that were cool in 2001 are having their revenge again. Here's a small example of starting with a normalmapped Blinn-Phong shader and optimizing it to run several times faster. Most of the clever stuff below was actually done by [ReJ](http://twitter.com/#!/__ReJ__), props to him!

Here's a small test I'll be working on: just a single plane with albedo and normal map textures:

[![](http://aras-p.info/blog/wp-content/uploads/2011/02/iosbump1-150x150.jpg)](http://aras-p.info/blog/wp-content/uploads/2011/02/iosbump1.jpg)

I'll be testing on iPhone 3Gs with iOS 4.2.1. Timer is started before glClear() and stopped after glFinish() that I added just after drawing the mesh.

Let's start with an initial naive shader version:

{% gist 783784 %}

Should be pretty self-explanatory to anyone who's familiar with tangent space normal mapping and Blinn-Phong BRDF. Running time: **24.5 milliseconds**. On iPhone 4's Retina resolution, this would be about 4x slower!

What can we do next? On mobile platforms using appropriate precision of variables is often very important, especially in a fragment shader. So let's go and add highp/mediump/lowp qualifiers to the fragment shader: [shader source](https://gist.github.com/783703/05e78340b12739e853ce031bd0388430ea95f2a6)

Still the same running time! Alas, iOS does not have low level shader analysis tools, so we can't really tell why that is happening. We could be limited by something else (e.g. normalizing vectors and computing pow() being the bottlenecks that run in parallel with all low precision stuff), or the driver might be promoting most of our computations to higher precision because it feels like it. It's a magic box!

Let's start approximating instead. How about computing normalized view direction per vertex, and interpolating that for the fragment shader? It won't be entirely "correct", but hey, it's a phone we're talking about. [shader source](https://gist.github.com/783703/1e4fd0daa384d308d125a748985e8e203e49625a)

[{% img right http://aras-p.info/blog/wp-content/uploads/2011/02/iosbump3-150x150.jpg %}](http://aras-p.info/blog/wp-content/uploads/2011/02/iosbump3.jpg)
15 milliseconds! But... the rendering is wrong; everything turned white near the bottom of the screen. Turns out PowerVR SGX (the GPU in all current iOS devices) is really meaning "low precision" when we want to add two lowp vectors and normalize the result. Let's try promoting one of them to medium precision with a "varying mediump vec3 v_viewdir": [shader source](https://gist.github.com/783703/591eb83dacaae3840cc4e4d3d8b95a4fc3abdd65)

That fixed rendering, but we're back to 24.5 milliseconds. _Sad shader writers are sad... oh shader performance analysis tools, where art thou?_

Let's try approximating some more: compute half-vector in the vertex shader, and interpolate normalized value. This would get rid of all normalizations in the fragment shader. [shader source](https://gist.github.com/783703/6360c2912b860aa30415e5120ef147169274cd71)

**16.3** milliseconds, not too bad! We still have pow() computed in the fragment shader, and that one is probably not the fastest operation there...

Almost a decade ago, a very common trick was to use a lookup texture to do the lighting. For example, a 2D texture indexed by (N.L, N.H). Since all lighting data would be "baked" into the texture, it does not necessarily have to be Blinn-Phong even; we can prepare faux-anisotropic, metallic, toon-shading or other fancy BRDFs there, as long as they can be expressed in terms of N.L and N.H. So let's try creating 128x128 RGBA lookup texture and use that: [shader source](https://gist.github.com/783703/87f1cf5529d644cab16123550e809e9f7598f4f3)

A fast & not super efficient code to create the lighting lookup texture for Blinn-Phong:

{% gist 783759 %}


**9.1** milliseconds! We lost some precision in the specular though (it's dimmer):

[![](http://aras-p.info/blog/wp-content/uploads/2011/02/iosbump6-150x150.jpg)](http://aras-p.info/blog/wp-content/uploads/2011/02/iosbump6.jpg)

What else can be done? Notice that we clamp N.L and N.H values in the fragment shader, but this could be done just as well by the texture sampler, if we set texture's addressing mode to CLAMP_TO_EDGE. Let's get rid of the clamps: [shader source](https://gist.github.com/783703/e24a2475fded83d2196372c8092a0d8de80a98eb)

This is 8.3 milliseconds, or **7.6** milliseconds if we reduce our lighting texture resolution to 32x128.

Should we stop there? Not necessarily. For example, the shader is still multiplying albedo with a per-material color. Maybe that's not very useful and can be let go. Maybe we can also make specular be always white?

{% gist 783703 %}


How fast is this? **5.9 milliseconds**, or over **4 times** faster than our original shader.

Could it be made faster? Maybe; that's an exercise for the reader :) I tried computing just the RGB color channels and setting alpha to zero, but that got slightly slower. Without real shader analysis tools it's hard to see where or if additional cycles could be squeezed out.

I'm adding [Xcode project with sources, textures and shaders of this experiment](http://aras-p.info/blog/wp-content/uploads/2011/02/iOSShaderPerf.zip). Notes about it: only tested on iPhone 3Gs (probably will crash on iPhone 3G, and iPad will have wrong aspect ratio). Might not work at all! Shader is read from Resources/Shaders/shader.txt, next to it are shader versions of the steps of this experiment. Enjoy!

_This is a cross post from altdevblogaday: [http://altdevblogaday.com/ios-shader-tricks-or-its-2001-all-over-again](http://altdevblogaday.com/ios-shader-tricks-or-its-2001-all-over-again)_
