---
tags:
- code
- d3d
- gpu
- rendering
- unity
- work
comments: true
date: 2009-06-09T08:08:50Z
slug: implementing-fixed-function-tl-in-vertex-shaders
status: publish
title: Implementing fixed function T&L in vertex shaders
url: /blog/2009/06/09/implementing-fixed-function-tl-in-vertex-shaders/
wordpress_id: "364"
---

Almost half a year ago I was wondering [how to implement T&L; in vertex shaders](/blog/2009/01/22/fixed-function-lighting-in-vertex-shader-how/).

Well, finally I implemented it for upcoming Unity 2.6. I wrote some sort of a [**technical report here**](http://aras-p.info/texts/VertexShaderTnL.html).

In short, I'm combining assembly fragments and doing simple temporary register allocation, which seems to work quite well. Performance is very similar to using fixed function (I know it's implemented as vertex shaders internally by the runtime/driver) on several different cards I tried (Radeon HD 3xxx, GeForce 8xxx, Intel GMA 950).

What was unexpected: the most complex piece is not the vertex lighting! Most complexity is in how to route/generate texture coordinates and transform them. Huge combination explosion there.

Otherwise - I like! Here's a link to the [article again](http://aras-p.info/texts/VertexShaderTnL.html).
