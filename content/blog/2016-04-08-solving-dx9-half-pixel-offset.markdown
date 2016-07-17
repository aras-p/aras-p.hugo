---
categories: code d3d rendering
comments: true
date: 2016-04-08T00:00:00Z
title: Solving DX9 Half-Pixel Offset
url: /blog/2016/04/08/solving-dx9-half-pixel-offset/
---

Summary: the Direct3D 9 "half pixel offset" problem that manages to annoy everyone can be
solved in a single isolated place, robustly, and in a way where you don't have to think about it
ever again. Just add two instructions to all your vertex shaders, *automatically*.

*...here I am wondering if the target audience for D3D9 related blog post in 2016 is more than
7 people in the world. Eh, whatever!*



### Background

Direct3D before version 10 had this pecularity called "[half pixel offset](https://www.google.com/search?q=half%20pixel%20offset)", where viewport coordinates are shifted by half a pixel compared
to everyone else (OpenGL, D3D10+, Metal etc.). This causes various problems, particularly with
image post-processing or UI rendering, but elsewhere too.

The official documentation ("[Directly Mapping Texels to Pixels](https://msdn.microsoft.com/en-us/library/windows/desktop/bb219690.aspx)"),
while being technically correct, is not exactly summarized into three easy bullet points.

The typical advice is various: "shift your quad vertex positions by half a pixel" or "shift texture
coordinates by half a texel", etc. Most of them talk almost exclusively about screenspace
rendering for image processing or UI.

[{{<imgright src="/img/blog/2016-04/halfpixel-CodeRemoval.png" width="200">}}](/img/blog/2016-04/halfpixel-CodeRemoval.png)
The problem with all that, is that this requires you to *remember to do things* in various little places.
Your postprocessing code needs to be aware. Your UI needs to be aware. Your baking code needs to be aware.
Some of your shaders need to be aware. When 20 places in your code need to remember to deal with this, you
know you have a problem.



### 3D has half-pixel problem too!

While most of material on D3D9 half pixel offset talks about screen-space operations, the problem exists in 3D
too! 3D objects are rendered *slightly shifted* compared to what happens on OpenGL, D3D10+ or Metal.

Here's a crop of a scene, rendered in D3D9 vs D3D11:

[{{<img src="/img/blog/2016-04/halfpixel-Full-dx9.png">}}](/img/blog/2016-04/halfpixel-Full-dx9.png)[{{<img src="/img/blog/2016-04/halfpixel-Full-dx11.png">}}](/img/blog/2016-04/halfpixel-Full-dx11.png)

And a crop of a crop, scaled up even more, D3D9 vs D3D11:

[{{<img src="/img/blog/2016-04/halfpixel-Crop-dx9.png">}}](/img/blog/2016-04/halfpixel-Crop-dx9.png)[{{<img src="/img/blog/2016-04/halfpixel-Crop-dx11.png">}}](/img/blog/2016-04/halfpixel-Crop-dx11.png)



### Root Cause and Solution

The root cause is that viewport is shifted by half a pixel compared to where we want it to be. Unfortunately
we can't fix it by changing all coordinates passed into SetViewport, shifting them by half a pixel
([D3DVIEWPORT9](https://msdn.microsoft.com/en-us/library/windows/desktop/bb172632.aspx)
coordinate members are integers).

However, we have vertex shaders. And the vertex shaders output clip space position. We can adjust the clip space
position, to shift everything by half a viewport pixel. Essentially we need to do this:

``` c++
// clipPos is float4 that contains position output from vertex shader
// (POSITION/SV_Position semantic):
clipPos.xy += renderTargetInvSize.xy * clipPos.w;
```

That's it. Nothing more to do. Do this in *all* your vertex shaders, setup shader constant that contains
viewport size, and you are done.

I must stress that this is done across the board. Not only postprocessing or UI shaders. *Everything*. This
fixes the 3D rasterizing mismatch, fixes postprocessing, fixes UI, etc.



### Wait, why no one does this then?

Ha. Turns out, they do!

* [Simon Brown](https://twitter.com/sjb3d) has blogged about this *in 2003*:
  "[How To Fix The DirectX Rasterisation Rules](https://web.archive.org/web/20090812055103/http://www.sjbrown.co.uk/2003/05/01/fix-directx-rasterisation)"
  *(actual site down at the moment, web archive link)*.
* WebGL [ANGLE](https://github.com/google/angle) uses this, and wrote about it in "The ANGLE Project:
  Implementing OpenGL ES 2.0 on Direct3D" article, part of [OpenGL Insights](http://openglinsights.com/) book in 2012.
* Microsoft's Direct3D 11 [Feature Levels 9.x](https://msdn.microsoft.com/en-us/library/windows/desktop/ff476876.aspx)
  do this behind the scenes; the shader compiler inserts the fixup and the runtime sets up the shader constant.
  Avery Lee blogged about this in 2012, "[Pixel center positioning with 10level9](http://www.virtualdub.org/blog/pivot/entry.php?id=366)".

Maybe it's common knowledge, and only *I* managed to be confused? Sorry about that then! Should have realized
this years ago...



### Solving This Automatically

The "add this line of HLSL code to all your shaders" is nice if you are writing or generating all the shader
source yourself. But what if you don't? (e.g. Unity falls into this camp; zillions of shaders already
written out there)

Turns out, it's not that hard to do this at D3D9 bytecode level. No HLSL shader code modifications needed.
Right after you compile the HLSL code into D3D9 bytecode (via D3DCompile or fxc), just slightly modify it.

D3D9 bytecode is documented in MSDN, "[Direct3D Shader Codes](https://msdn.microsoft.com/en-us/library/windows/hardware/ff552891.aspx)".

I thought whether I should be doing something flexible/universal
(parse "instructions" from bytecode, work on them, encode back into bytecode), or just write up minimal
amount of code needed for this patching. Decided on the latter; with any luck D3D9 is nearing it's end-of-life.
It's very unlikely that I will ever need *more* D3D9 bytecode manipulation. If in 5 years from now we'll
still need this code, I will be very sad!

The basic idea is:

1. Find which register is "output position" (clearly defined in shader model 2.0; can be arbitrary
  register in shader model 3.0), let's call this `oPos`.
1. Find unused temporary register, let's call this `tmpPos`.
1. Replace all usages of `oPos` with `tmpPos`.
1. Add `mad oPos.xy, tmpPos.w, constFixup, tmpPos` and `mov oPos.zw, tmpPos` at the end.

Here's what it does to simple vertex shader:
``` c++
vs_3_0           // unused temp register: r1
dcl_position v0
dcl_texcoord v1
dcl_texcoord o0.xy
dcl_texcoord1 o1.xyz
dcl_color o2
dcl_position o3  // o3 is position
pow r0.x, v1.x, v1.y
mul r0.xy, r0.x, v1
add o0.xy, r0.y, r0.x
add o1.xyz, c4, -v0
mul o2, c4, v0
dp4 o3.x, v0, c0 // -> dp4 r1.x, v0, c0
dp4 o3.y, v0, c1 // -> dp4 r1.y, v0, c1
dp4 o3.z, v0, c2 // -> dp4 r1.z, v0, c2
dp4 o3.w, v0, c3 // -> dp4 r1.w, v0, c3
                 // new: mad o3.xy, r1.w, c255, r1
                 // new: mov o3.zw, r1
```

Here's [**the code in a gist**](https://gist.github.com/aras-p/c2ea7b45ff3fbd5312eb9904c4bb8415).

At runtime, each time viewport is changed, set vertex shader constant (I picked c255) to contain
`(-1.0f/width, 1.0f/height, 0, 0)`.

*That's it!*



### Any downsides?

Not much :) The whole fixup needs shaders that:

* Have an unused constant register. Majority of our shaders are shader model 3.0, and I haven't seen
  *vertex* shaders that use all 32 temporary registers. If that is a problem, "find unused register"
  analysis could be made smarter, by looking for an unused register just in the place between
  earliest and latest position writes. I haven't done that.
* Have an unused constant register at some (easier if fixed) index. Base spec for both shader model 2.0 and 3.0
  is that vertex shaders have 256 constant registers, so I just picked the last one (c255) to contain
  fixup data.
* Have instruction slot space to put two more instructions. Again, shader model 3.0 has 512 instruction slot limit
  and it's very unlikely it's using more than 510.



### Upsides!

Major ones:

* No one ever needs to think about D3D9 half-pixel offset, *ever*, again.
* 3D rasterization positions match exactly between D3D9 and everything else (D3D11, GL, Metal etc.).

*Fixed up D3D9 vs D3D11. Matches now:*

[{{<img src="/img/blog/2016-04/halfpixel-Crop-dx9new.png">}}](/img/blog/2016-04/halfpixel-Crop-dx9new.png)[{{<img src="/img/blog/2016-04/halfpixel-Crop-dx11.png">}}](/img/blog/2016-04/halfpixel-Crop-dx11.png)


I ran all the graphics tests we have, inspected all the resulting differences, and compared the results
with D3D11. Turns out, this revealed a few minor places where we got the half-pixel offset wrong
in our shaders/code before. So additional advantages (all Unity specific):

* Some cases of GrabPass were sampling in the middle of pixels, i.e. slightly blurred results. Matches DX11 now.
* Some shadow acne artifacts slightly reduced; matches DX11 now.
* Some cases of image postprocessing effects having a one pixel gap on objects that should have been
  touching edge of screen exactly, have been fixed. Matches DX11 now.

All this will probably go into Unity 5.5. Still haven't decided whether it's too invasive/risky change to
put into 5.4 at this stage.
