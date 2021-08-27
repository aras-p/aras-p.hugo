---
title: "EXR: Filtering and ZFP"
date: 2021-08-27T07:32:10+03:00
tags: ['rendering']
comments: true
---

In the [previous blog post](/blog/2021/08/09/EXR-libdeflate-is-great/) I looked at
using libdeflate for [OpenEXR](https://en.wikipedia.org/wiki/OpenEXR) Zip compression. Let's look at a few other things now!

### Prediction / filtering

As noticed in the [zstd post](/blog/2021/08/06/EXR-Zstandard-compression/), OpenEXR does some *filtering* of the input pixel data
before passing it to a zip compressor. The filtering scheme it does is fairly simple: assume input data is in 16-bit units, split that up into
two streams (all lower bytes, all higher bytes), and delta-encode the result. Then do regular zip/deflate compression.

Another way to look at filtering is in terms of *prediction*: instead of storing the actual pixel values of an image, we try to *predict* what
the next pixel value will be, and store the difference between actual and predicted value. The idea is, that if our predictor is any good,
the differences will often be very small, which then compress really well. If we'd have a 100% perfect predictor, all we'd need
to store is *"first pixel value... and a million zeroes here!"*, which takes up next to nothing after compression.

When viewed this way, delta encoding is then simply a "next pixel will be the same as the previous one" predictor.

But we could build more fancy predictors for sure! [PNG filters](https://en.wikipedia.org/wiki/Portable_Network_Graphics#Filtering) have several
types (delta encoding is then the "Sub" type). In audio land, [DPCM encoding](https://en.wikipedia.org/wiki/Differential_pulse-code_modulation)
is using predictors too, and was invented 70 years ago.

I tried using what is called "ClampedGrad" predictor (from [Charles Bloom blog post](https://cbloomrants.blogspot.com/2010/06/06-20-10-filters-for-png-alike.html)), which turns out to be the same as
[LOCO-I predictor](https://en.wikipedia.org/wiki/Lossless_JPEG#Decorrelation/prediction) in JPEG-LS. It looks like this in pseudocode:

```
// +--+--+
// |NW|N |
// +--+--+
// |W |* |
// +--+--+
//
// W - pixel value to the left
// N - pixel value up (previous row)
// NW - pixel value up and to the left
// * - pixel we are predicting
int grad = N + W - NW;
int lo = min(N,W);
int hi = max(N,W);
return clamp(grad,lo,hi);
```

(whereas the current predictor used by OpenEXR would simply be `return W`)

Does it improve the compression ratio? Hmm, at least on my test image set, only barely. Zstd compression at level 1:

* Current predictor: 2.463x compression ratio,
* ClampedGrad predictor: 2.472x ratio.

So either I did something wrong :), or my test image set is not great, or trying this more fancy predictor sounds like it's not worth it -- the compression ratio gains are *tiny*.


### Lossless ZFP compression

*A topic jump!* Let's try [ZFP](https://computing.llnl.gov/projects/zfp) ([github](https://github.com/LLNL/zfp)) compression. ZFP seems
to be primarily targeted at *lossy* compression, but it also has a lossless ("reversible") mode which is what we're going to use here.

It's more similar to GPU [texture compression](/blog/2020/12/08/Texture-Compression-in-2020/) schemes --
2D data is divided into 4x4 blocks, and each block is encoded completely independently from the others. Inside the block, various
*magic stuff* happens and then, ehh, some bits get out in the end :) The actual algorithm is well
[explained here](https://zfp.readthedocs.io/en/release0.5.5/algorithm.html).

I used ZFP development version (`d83d343` from 2021 Aug 18). At the time of writing, it only supported `float` and `double` floating point
data types, but in OpenEXR majority of data is half-precision floats. I tested ZFP as-is, by converting half float data into floats back and forth as needed, but also tried hacking in native FP16 support ([commit](https://github.com/aras-p/zfp/commit/c8e60c00a)).

Here's what I got (click for an interactive chart): <br/>
[{{<img src="/img/blog/2021/exr/exr06-zfp.png">}}](/img/blog/2021/exr/exr06-zfp.html)

* ▴ - ZFP as-is. Convert EXR FP16 data into regular floats, compress that.
* ■ - as above, but also compress the result with Zstd level 1.
* ● - ZFP, with added support for half-precision (FP16) data type.
* ◆ - as above, but also compress the result with Zstd level 1.

Ok, so basically ZFP in lossless mode for OpenEXR data is "meh". Compression ratio not great (1.8x - 2.0x), compression and decompression
performance is pretty bad too. Oh well! If I'll look at lossy EXR compression at some point, maybe it would be worth revisiting ZFP then.


### Next up?

The two attempts above were both underwhelming. Maybe I should look into lossy compression next, but of course lossy compression is always hard.
In addition to "how fast?" and "how small?", there's a whole additional "how good does it look?" axis to compare with,
and it's a much more complex comparison too. Maybe someday!
