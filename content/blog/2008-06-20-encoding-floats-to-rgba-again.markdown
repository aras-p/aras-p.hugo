---
tags:
- gpu
comments: true
date: 2008-06-20T17:55:56Z
slug: encoding-floats-to-rgba-again
status: publish
title: Encoding floats to RGBA, again
url: /blog/2008/06/20/encoding-floats-to-rgba-again/
wordpress_id: "181"
---

Hey, it looks like the quest for encoding floats to RGBA textures ([part 1](/blog/2007/03/03/a-day-well-spent-encoding-floats-to-rgba/), [part 2](/blog/2007/06/29/encoding-floats-to-rgba-redux/)) did not end yet.

Here's the "best available" code that I have now:


<pre>
inline float4 EncodeFloatRGBA( float v ) {
  return frac( float4(1.0, 255.0, 65025.0, 160581375.0) * v ) + bias;
}
inline float DecodeFloatRGBA( float4 rgba ) {
  return dot( rgba, float4(1.0, 1/255.0, 1/65025.0, 1/160581375.0) );
}
</pre>
 


[Before](/blog/2007/06/29/encoding-floats-to-rgba-redux/) I thought that **bias** should be +0.5/255.0 normally, except it had to be around -0.55/255.0 on Radeon cards (older than Radeon HD series). Well, turns out I was wrong, the bias _mostly_ has to be around -0.5/255.0.

Here's the list (same bias on Windows/D3D9 and OS X/OpenGL, so it seems to be hardware dependent, and not something in API/drivers):




  * Radeon 9500 to X850: -0.61/255
  * Radeon X1300 to X1900: -0.66/255
  * Radeon HD 2xxx/3xxx: -0.49/255
  * GeForce FX, 6, 7, 8: -0.48/255
  * Intel 915, 945, 965: -0.5/255



Those are the best bias values I could find. Still, every once in a while (rarely) encoding the value to RGBA texture and reading it back would produce something where one channel is half a bit off. Not a problem if you were encoding numbers were originally 0..1 range, but for example if you were encoding something that spans over whole range of the camera, then 0..1 range gets expanded into 0..FarPlane...

And all of a sudden there are **huge** precision errors, up to the point of being unusable. I just tried doing a quick'n'dirty depth of field and soft particles implementation using depth encoded this way... not good.

Oh well. Has anyone successfully used encoding of high precision number into RGBA channels before?
