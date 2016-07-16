---
categories:
- gpu
comments: true
date: 2007-03-03T18:33:00Z
slug: a-day-well-spent-encoding-floats-to-rgba
status: publish
title: A day well spent (encoding floats to RGBA)
url: /blog/2007/03/03/a-day-well-spent-encoding-floats-to-rgba/
wordpress_id: "103"
---

[{% img right http://aras-p.info/blog/wp-content/uploads/2007/03/rgba01.thumbnail.png 'RGBA encoding 01' %}](http://aras-p.info/blog/wp-content/uploads/2007/03/rgba01.png)Breaking news: sometimes seemingly trivial tasks take insane amounts of time! I am sure no one knew this before! So it was yesterday - almost whole day spent fighting rounding/precision errors when encoding floating point numbers into regular 8 bit RGBA textures. You know, the trivial stuff where you start with
 

     
     inline float4 EncodeFloatRGBA( float v ) {
       return frac( float4(1.0, 256.0, 65536.0, 16777216.0) * v );
     }
     inline float DecodeFloatRGBA( float4 rgba ) {
       return dot( rgba, float4(1.0, 1.0/256.0, 1.0/65536.0, 1.0/16777216.0) );
     }
 
 

and everything is fine until sometimes, somewhere there's "something wrong". Must be rounding or quantizations errors; or maybe I should use 255 instead of 256; plus optionally add or subtract 0.5/256.0 (or would that be 0.5/255.0?). Or maybe the error is entirely somewhere else, and I'm just chasing ghosts here!

[{% img right http://aras-p.info/blog/wp-content/uploads/2007/03/rgba02.thumbnail.png 'RGBA encoding 02' %}](http://aras-p.info/blog/wp-content/uploads/2007/03/rgba02.png)What would you do then? Why, of course, build an Encoding Floats Into Textures Studio 2007! (don't tell me it's not a great idea for a commercial software package! game studios would pay insane amounts of money for a tool like this!) The images here are exactly that - render into a texture, encoding UV coordinate as RGBA, then read from that texture, displaying RGBA and error from the expected value in some weird way. Turns out image postprocessing filters in Unity are a pretty good tool to do all this. Yay!

[{% img right http://aras-p.info/blog/wp-content/uploads/2007/03/rgba03.thumbnail.png 'RGBA encoding 03' %}](http://aras-p.info/blog/wp-content/uploads/2007/03/rgba03.png)Sometimes in situations like this I figure out that graphics hardware still leaves a lot to be desired. This last image shows some calculations that depend only on the horizontal UV coordinate, so they should produce some purely vertical pattern (sans the part at the bottom, that is expected to be different). Heh, you wish!
