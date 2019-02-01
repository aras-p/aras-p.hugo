---
tags:
- rendering
comments: true
date: 2005-05-13T12:38:00Z
slug: ambient-occlusion-takes-ages-to-compute
status: publish
title: Ambient occlusion takes ages to compute!
url: /blog/2005/05/13/ambient-occlusion-takes-ages-to-compute/
wordpress_id: "33"
---

Really. Three hours for a model at 1024x1024 and 5x supersampling!

Now I'm using ATI's [NormalMapper](http://www.ati.com/developer/tools.html) to compute normal/AO maps (Previously was using nVidia's [Melody](http://developer.nvidia.com/object/melody_home.html), but switched for no obvious reason). The good thing with NormalMapper is that it comes with sourcecode; I've already sped up AO computation about 20% by capping octree traversal distances (that took less than an hour). I suspect with some thought it could be optimized even more.

{{<imgright src="/img/blog/050513.jpg">}}
Previously I was using a hacked solution - compute normal map with either tool (that doesn't take long), then use my custom small tool that does low-order GPU PRT simulation on low-poly normalmapped model with D3DX. Get the first term of results, scale it and there you have ambient occlusion. I was thinking it produces good results, but in the truth is that 'real' AO maps look somewhat better, especially for small ornaments that aren't captured in low-poly geometry.

The good thing about this hacked approach is that it takes ~10 seconds for a model (compare to 3hrs). Using it as a quick preview is great, and the differences between hacked-AO and real-AO aren't _that much_ visible once you add textures and conventional lighting.

I'm thinking about doing GPU-based AO simulation _on the high poly_ model, with quick-n-dirty UV parametrization; then just fetching the resulting texture in normal map computation tool (afaik, Melody can do that natively; for NormalMapper it would be easy to add I think). With recent DX9 SDK such tool should not take more than 200-300 lines of code (D3DX has both UVAtlas and GPU PRT simulation now). _On the other hand, I know that nVidia guys are preparing something similar :)_

_Update:_ added image - on the left is hacked-n-fast AO, on the right is real-AO. Ornaments inside aren't present in low-poly model (only in normal map). Differences are less visible when the model is textured and other stuff is added.
