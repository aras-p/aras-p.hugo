---
title: "Float Compression 0: Intro"
date: 2023-01-29T14:04:10+03:00
tags: ['code', 'performance']
---

I was playing around with compression of some floating point data, and decided to write up
my findings. None of this will be any news for anyone who *actually* knows anything about compression,
but eh, did that ever stop me from blogging? :)

#### Situation

Suppose we have some sort of "simulation" thing going on, that produces various data. And we need to
take snapshots of it, i.e. save simulation state and later restore it. The amount of data is in the
order of several hundred megabytes, and for one or another reason we'd want to compress it a bit.

Let's look at a more concrete example. Simulation state that we're saving consists of:

**Water simulation**. This is a 2048x2048 2D array, with four floats per array element (water height,
flow velocity X, velocity Y, pollution). If you're a graphics programmer, think of it as a 2k square
texture with a `float4` in each texel.

Here's the data visualized, and the actual 64MB size raw data file ([link](https://github.com/aras-p/float_compr_tester/blob/main/data/2048_sq_float4.bin)): \
[{{<img src="/img/blog/2023/float-compr/data-water-height-vis.jpg" width="208px">}}](/img/blog/2023/float-compr/data-water-height-vis.jpg)
[{{<img src="/img/blog/2023/float-compr/data-water-height.png" width="150px">}}](/img/blog/2023/float-compr/data-water-height.png)
[{{<img src="/img/blog/2023/float-compr/data-water-velocity.png" width="150px">}}](/img/blog/2023/float-compr/data-water-velocity.png)
[{{<img src="/img/blog/2023/float-compr/data-water-pollution.png" width="150px">}}](/img/blog/2023/float-compr/data-water-pollution.png)

**Snow simulation**. This is a 1024x1024 2D array, again with four floats per array element (snow amount,
snow-in-water amount, ground height, and the last float is actually always zero).

Here's the data visualized, and the actual 16MB size raw data file ([link](https://github.com/aras-p/float_compr_tester/blob/main/data/1024_sq_float4.bin)): \
[{{<img src="/img/blog/2023/float-compr/data-snow.png" width="150px">}}](/img/blog/2023/float-compr/data-snow.png)
[{{<img src="/img/blog/2023/float-compr/data-snow-height-vis.jpg" width="234px">}}](/img/blog/2023/float-compr/data-snow-height-vis.jpg)
[{{<img src="/img/blog/2023/float-compr/data-snow-height.png" width="150px">}}](/img/blog/2023/float-compr/data-snow-height.png)

A bunch of **"other data"** that is various float4s (mostly colors and rotation quaternions) and various float3s
(mostly positions). Let's assume these are not further structured in any way; we just have a big array of
float4s ([raw file](https://github.com/aras-p/float_compr_tester/blob/main/data/232630_float4.bin), 3.55MB) and another
array of float3s ([raw file](https://github.com/aras-p/float_compr_tester/blob/main/data/953134_float3.bin), 10.9MB).

Don't ask me why the "water height" seems to be going the other direction as "snow ground height", or why there's a full float in each snow simulation
data point that is not used at all. Also, in this particular test case, "snow-in-water" state is always zero too. 🤷

Anyway! So we do have 94.5MB worth of data that is all floating point numbers. Let's have a go at making it a bit smaller.


#### Post Index

Similar to the [toy path tracer](/blog/2018/03/28/Daily-Pathtracer-Part-0-Intro/) series I did back in 2018,
I generally have no idea what I'm doing. I've heard a thing or two about compression, but I'm not an expert by
any means. I never wrote my own LZ compressor or a Huffman tree, for example. So the whole series is part
exploration, part discovery; who knows where it will lead.

* [Part 1: Generic Lossless Compression](/blog/2023/01/29/Float-Compression-1-Generic/).

