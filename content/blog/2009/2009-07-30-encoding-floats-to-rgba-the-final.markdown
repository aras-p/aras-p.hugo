---
tags:
- gpu
comments: true
date: 2009-07-30T14:58:08Z
slug: encoding-floats-to-rgba-the-final
status: publish
title: Encoding floats to RGBA - the final?
url: /blog/2009/07/30/encoding-floats-to-rgba-the-final/
wordpress_id: "369"
---

The saga continues! In short, I need to pack a floating point number in [0..1) range into several channels of 8 bit/channel render texture. My [previous approach](/blog/2008/06/20/encoding-floats-to-rgba-again/) is not ideal.

Turns out some folks have figured out an approach that finally _seems_ to work.

Here it is for my own reference:



  * [gamedev.net forum post by gjaegy](http://www.gamedev.net/community/forums/topic.asp?topic_id=442138&whichpage=1&#2936108)


  * Suggestion [right there](/blog/2008/06/20/encoding-floats-to-rgba-again/#comment-16380) on my previous blog post comments


  * Repost [gamerendering blog](http://www.gamerendering.com/2008/09/25/packing-a-float-value-in-rgba/)


  * Repost on [gamedev.net forums](http://www.gamedev.net/community/forums/topic.asp?topic_id=463075&whichpage=1&#3054958) again.



So here's the proper way:


```
inline float4 EncodeFloatRGBA( float v ) {
  float4 enc = float4(1.0, 255.0, 65025.0, 16581375.0) * v;
  enc = frac(enc);
  enc -= enc.yzww * float4(1.0/255.0,1.0/255.0,1.0/255.0,0.0);
  return enc;
}
inline float DecodeFloatRGBA( float4 rgba ) {
  return dot( rgba, float4(1.0, 1/255.0, 1/65025.0, 1/16581375.0) );
}
```


That is, the difference from the [previous approach](/blog/2008/06/20/encoding-floats-to-rgba-again/) is that the "magic" (read: hardware dependent) bias is replaced with subtracting next component's encoded value from the previous component's encoded value.
