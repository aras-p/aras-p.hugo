---
title: "'Infinite' sky shader for Unity"
date: 2019-02-01T18:02:10+03:00
tags: ['rendering', 'unity']
comments: true
---

I saw a discussion today that lamented lack of "infinite projection" in Unity, so here's
a quick post about that.

Unity today (at least in 2017/2018 versions) uses "reversed Z" projection, but does not use
"infinite projection". This means that a Camera has an actual far clipping plane,
and beyond that distance nothing is rendered.

This is fine for almost all cases, except when you want to render custom "skybox-like"
geometry (a sky sphere or whatever); then you'd like to have it be "infinitely large"
and thus guaranteed to always be beyond any actual scene geometry.

You could wait for Unity to implement "infinite projection", and/or write a custom
render pipeline to use your own infinite projection, or do a super small local change
just inside your "sky object" shader to make it effectively be infinite. Let's check out
how to do the last bit!


### "Infinite size" shader

To achieve an effectively "infinite size" (i.e. appears "behind any objects") shader, all
we have to do is to move the vertices to be "on the far plane" in the vertex shader.
If `o.vertex` is a `float4` with clip space position (e.g. computed by `UnityObjectToClipPos`),
then just do this:

```c++
#if defined(UNITY_REVERSED_Z)
// when using reversed-Z, make the Z be just a tiny
// bit above 0.0
o.vertex.z = 1.0e-9f;
#else
// when not using reversed-Z, make Z/W be just a tiny
// bit below 1.0
o.vertex.z = o.vertex.w - 1.0e-6f;
#endif
```

And here it is. Far plane of only 20, and a "sky sphere" object that is 100000 in size. No
clipping, renders behind scene geometry:
[{{<img src="/img/blog/2019/infinite-sky.png">}}](/img/blog/2019/infinite-sky.png)

Here's Unity 2018.2.15 project with the whole shader and a simple scene -
<a href="/img/blog/2019/InfiniteSkyUnityProject.zip">InfiniteSkyUnityProject.zip</a> (350kb)


### What's all this Reversed Z and Infinite Projection anyway?

[Nathan Reed explains it](http://www.reedbeta.com/blog/depth-precision-visualized/)
much better than I ever could, read that post!

Summary is that reversing depth so that far plane is at zero, and near plane is at one,
and using a floating point format depth buffer, results in much better depth precision.

In Unity we have implemented support for this reversed Z a while ago (in Unity 5.5).
Today we don't use infinite projection (yet?), so to achieve the infinite-like sky objects
you'd have to do a shader trick like above.

This is all.
