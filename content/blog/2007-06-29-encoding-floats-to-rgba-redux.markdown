---
tags:
- gpu
comments: true
date: 2007-06-29T09:58:24Z
slug: encoding-floats-to-rgba-redux
status: publish
title: Encoding floats to RGBA, redux
url: /blog/2007/06/29/encoding-floats-to-rgba-redux/
wordpress_id: "124"
---

Gleserg has interesting comments in [my earlier post](/blog/2007/03/03/a-day-well-spent-encoding-floats-to-rgba). So I thought I'd share what I am using right now, and try to throw some more complexities in :)

Here is what I am doing right now:

     
     inline float4 EncodeFloatRGBA( float v ) {
       return frac( float4(1.0, 255.0, 65025.0, 160581375.0) * v ) + 0.5/255.0;
     }
     inline float DecodeFloatRGBA( float4 rgba ) {
       return dot( rgba, float4(1.0, 1/255.0, 1/65025.0, 1/160581375.0) );
     }
 
 



And this seems to work fine almost everywhere (see below). Why am I doing this - good question, I don't have a hard theory on which bits go where and so on. I think I saw someone on gamedev.net forums saying that in hardware 0 == 0.0 and 255 == 1.0, and that truncation is actually done on the values (not rounding). So that would mean you multiply by 255 and add a half of a bit.

Now, the trick: the above does not quite work on Radeons (at least the X1600 that I'm mostly developing on while I'm on a Mac). Instead of adding 0.5/255.0, you have to subtract 0.55/255.0 - and that value is still not perfect, but that's the best I could come up with by plowing through various combinations. I have no idea why this must be performed (24 bit internal precision? or does it round _up_? something else?). On GeForces and even Intel's shader-capable hardware, the expected +0.5/255.0 value works.

...anyone up to figuring out the mathematical proof on why encoding/decoding this way actually works? :) And yes, the last component (the one that uses 160581375) is pretty much meaningless.
