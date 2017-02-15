+++
tags = ['random']
comments = true
date = "2017-02-15T21:02:38+02:00"
title = "Font Rendering is Getting Interesting"
+++

> Caveat: I know nothing about font rendering! But looking at the internets, it feels like things are getting
> interesting. I had exactly the same outsider impression watching some discussions unfold
> between [Yann Collet](http://fastcompression.blogspot.lt/), [Fabian Giesen](https://fgiesen.wordpress.com/about/)
> and [Charles Bloom](http://www.cbloom.com/) a few years ago -- and out of that came rANS/tANS/FSE, and Oodle and
> Zstandard. Things were super exciting in compression world! My guess is that about "right now" things
> are getting exciting in font rendering world too.


### Ye Olde CPU Font Rasterization

A true and tried method of rendering fonts is doing rasterization on the CPU, caching the result (of glyphs, glyph
sequences, full words or at some other granularity) into bitmaps or textures, and then rendering them somewhere on the
screen.

[**FreeType**](https://www.freetype.org/) library for font parsing and rasterization has existed since "forever", as well
as operating system specific ways of rasterizing glyphs into bitmaps. Some parts of the hinting process have been patented,
leading to "fonts on Linux look bad" impressions in the old days (my understanding is that all these expired around year
2010, so it's all good now). And [subpixel optimized rendering](https://en.wikipedia.org/wiki/Subpixel_rendering)
happened at some point too, which slightly complicates the whole thing. There's a good overview of the whole thing
in 2007 [Texts Rasterization Exposures](http://www.antigrain.com/research/font_rasterization/) article by Maxim Shemanarev.

In addition to FreeType, these font libraries are worth looking into:

* [**stb_truetype.h**](https://github.com/nothings/stb/blob/master/stb_truetype.h) -- single file C library by
  Sean Barrett. Super easy to integrate! Article on how the innards of the
  rasterizer work [is here](http://nothings.org/gamedev/rasterize/).
* [**font-rs**](https://github.com/google/font-rs) -- fast font renderer by Raph Levien, written in Rust \o/, and
  [an article](https://medium.com/@raphlinus/inside-the-fastest-font-renderer-in-the-world-75ae5270c445)
  describing some aspects of it. Not sure how "production ready" it is though.

But at the core the whole idea is still rasterizing glyphs into bitmaps at a specific point size and caching the result
somehow.

Caching rasterized glyphs into bitmaps works well enough. If you don't do a lot of different font sizes. Or very large
font sizes. Or large amounts of glyphs (as happens in many non-Latin-like languages) coupled with different/large font sizes.


### One bitmap for varying sizes? Signed distance fields!

A 2007 paper from Chris Green,
[**Improved Alpha-Tested Magnification for Vector Textures and Special Effects**](http://www.valvesoftware.com/publications/2007/SIGGRAPH2007_AlphaTestedMagnification.pdf), introduced game development world to the concept of "signed distance field textures
for vector-like stuffs".

[{{<imgright src="/img/blog/2017-02/font-valve-paper.png" width="300">}}](/img/blog/2017-02/font-valve-paper.png)
The paper was mostly about solving "signs and markings are hard in games" problem, and the idea is pretty clever. Instead of
storing rasterized shape in a texture, store a special texture where each pixel represents distance to the closest shape
edge. When rendering with that texture, a pixel shader can do simple alpha discard, or more complex treatments on the distance
value to get anti-aliasing, outlines, etc. The SDF texture can end up really small, and still be able to decently represent high
resolution line art. _Nice!_

Then of course people realized that hey, the same approach could work for font rendering too! Suddenly, rendering smooth
glyphs at _super large_ font sizes does not mean "I just used up all my (V)RAM for the cached textures"; the cached SDFs
of the glyphs can remain fairly small, while providing nice edges at large sizes.

[{{<imgright src="/img/blog/2017-02/font-sdf-32.png" width="200">}}](/img/blog/2017-02/font-sdf-32.png)
Of course the SDF approach is not without some downsides:

* Computing the SDF is not trivially cheap. While for most western languages you could pre-cache all possible glyphs off-line into
  a SDF texture atlas, for other languages that's not practical due to sheer amount of glyphs possible.
* Simple SDF has artifacts near more complex intersections or corners, since it only stores a single distance to closest edge.
  Look at the letter `A` here, with a 32x32 SDF texture - outer corners are not sharp, and inner corners have artifacts.
* SDF does not quite work at very small font sizes, for a similar reason. There it's probably better to just rasterize the glyph
  into a regular bitmap.

Anyway, SDFs are a nice idea. For some examples or implementations, could look at
[libgdx](https://github.com/libgdx/libgdx/wiki/Distance-field-fonts) or
[TextMeshPro](http://digitalnativestudios.com/textmeshpro/docs/shaders/).

[{{<imgright src="/img/blog/2017-02/font-msdf-16.png" width="200">}}](/img/blog/2017-02/font-msdf-16.png)
The original paper hinted at the idea of storing _multiple_ distances to solve the SDF sharp corners problem, and a recent
implementation of that idea is "multi-channel distance field" by Viktor Chlumský which seems to be pretty nice:
[**msdfgen**](https://github.com/Chlumsky/msdfgen). See associated
[thesis too](https://dspace.cvut.cz/bitstream/handle/10467/62770/F8-DP-2015-Chlumsky-Viktor-thesis.pdf).
Here's letter `A` as a MSDF, at even smaller size than before -- the corners are sharp now!

That is pretty good. I guess the "tiny font sizes" and "cost of computing the (M)SDF" can still be problems though.


### Fonts directly on the GPU?

One obvious question is, "why do this caching into bitmaps at all? can't the GPU *just* render the glyphs directly?" The question
is good. The answer is not necessarily simple though ;)

GPUs are not ideally suited for doing vector rendering. They are mostly rasterizers, mostly deal with triangles, etc etc.
Even something simple like "draw thick lines" is pretty hard (great post on that -- [Drawing Lines is Hard](https://mattdesl.svbtle.com/drawing-lines-is-hard)). For more involved "vector / curve rendering", take a look at
[NVIDIA Path Rendering](https://developer.nvidia.com/gpu-accelerated-path-rendering), or
[Loop/Blinn Curve Rendering](http://http.developer.nvidia.com/GPUGems3/gpugems3_ch25.html), or more recent research into this area by
Rui Li, Qiming Hou and Kun Zhou: [Efficient GPU Path Rendering Using Scanline Rasterization](http://gaps-zju.org/pathrendering/).

_That stuff is not easy!_ But of course that did not stop people from trying. Good!


#### Vector Textures
[{{<imgright src="/img/blog/2017-02/font-vector-textures.png" width="200">}}](/img/blog/2017-02/font-vector-textures.png)
Here's one approach, [**GPU text rendering with vector textures**](http://wdobbie.com/post/gpu-text-rendering-with-vector-textures/)
by Will Dobbie - divides glyph area into rectangles, stores which curves intersect it, and evaluates coverage from said curves
in a pixel shader.

Pretty neat! However, seems that it does not solve "very small font sizes" problem (aliasing), has limit on glyph complexity (number of
curve segments per cell) and has some [robustness issues](https://twitter.com/EricLengyel/status/831763297607286786).

#### Glyphy

Another one is [**Glyphy**](https://github.com/behdad/glyphy), by Behdad Esfahbod (بهداد اسفهبد).
There's [video](https://vimeo.com/83732058) and [slides](http://behdad.org/glyphy_slides.pdf) of the talk about it.
Seems that it approximates Bézier curves with circular arcs, puts them into textures, stores indices of some closest
arcs in a grid, and evaluates distance to them in a pixel shader. Kind of a blend between
SDF approach and vector textures approach.


#### Pathfinder

[{{<imgright src="/img/blog/2017-02/font-pathfinder.png" width="300">}}](/img/blog/2017-02/font-pathfinder.png)
A new one is [**Pathfinder**](https://github.com/pcwalton/pathfinder), a Rust _(again!)_ library by Patrick Walton. Nice
overview of it in [this blog post](http://pcwalton.github.io/blog/2017/02/14/pathfinder/).

This looks promising!

Downsides, from a quick look, is dependence on GPU features that some platforms _(mobile...)_
might not have -- tessellation / geometry shaders / compute shaders (not a problem on PC). Memory for the coverage buffer,
and geometry complexity depending on the font curve complexity.

#### Hints at future on twitterverse

[{{<imgright src="/img/blog/2017-02/font-twitter.png" width="200">}}](/img/blog/2017-02/font-twitter.png)
From game developers/middleware space, looks like Sean Barrett and Eric Lengyel are independently working on some sort of
GPU-powered font/glyph rasterization approaches, as seen by their tweets
([Sean's](https://twitter.com/nothings/status/831751412031877121) and
[Eric's](https://twitter.com/EricLengyel/status/831773058235060230)).

Can't wait to see what they are cooking!

Did I say this is all very exciting? It totally is. Here's to clever new approaches to font rendering happening in 2017!


<hr>
<small>
Some figures in this post are taken from papers or pages I linked to above:

* SDF figure from Chris Green's [paper](http://www.valvesoftware.com/publications/2007/SIGGRAPH2007_AlphaTestedMagnification.pdf).
* A letter SDF and MSDF images from [msdfgen github page](https://github.com/Chlumsky/msdfgen).
* Vector textures illustration from [vector textures demo](http://wdobbie.com/pdf/).
* Pathfinder performance charts from [pathfinder post](http://pcwalton.github.io/blog/2017/02/14/pathfinder/).

</small>
