---
categories:
- code
comments: true
date: 2007-02-01T11:49:00Z
slug: whats-wrong-with-this-code
status: publish
title: What's wrong with this code?
url: /blog/2007/02/01/whats-wrong-with-this-code/
wordpress_id: "100"
---

Here's a short function:


     inline int SecondsToEnergy( float time )
     {
       return FastFloorfToInt( time * (float)(1 << kEnergyFixedPoint) );
     }

It's used in the particle system, and converts particle lifetime to an internal fixed point representation (10 bits for fractional part, i.e. kEnergyFixedPoint=10).

Some of the emitted particles are okay on a Mac, but completely not visible on Windows. This function is to blame.

Of course, what's wrong is the possible overflow in float-to-int conversion. Whenever someone tries to use lifetime longer than about 2097151, the conversion to signed 32 bit integer is undefined. It seems to clamp result in gcc and produce something like -1 in msvc.

Using multiple compilers can be hard, but it can also help in finding obscure bugs. Ha!
