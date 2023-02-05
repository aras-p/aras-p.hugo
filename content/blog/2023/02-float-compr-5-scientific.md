---
title: "Float Compression 5: Science!"
date: 2023-02-03T18:25:10+03:00
tags: ['code', 'performance']
---

*Introduction and index of this series [is here](/blog/2023/01/29/Float-Compression-0-Intro/)*.

[Previous post](/blog/2023/02/02/Float-Compression-4-Mesh-Optimizer/) was about mis-using [meshoptimizer](https://github.com/zeux/meshoptimizer)
compression for compressing totally non-mesh data, for pretty good results. This time, let's look at several libraries specifically targeted
at compressing floating point data sets. Most of them are coming from the scientific community -- after all, they do have lots of simulation
data, which is very often floating point numbers, and some of that data is massive and needs some compression.

Let's go!

Reminder: so far I'm only looking at *lossless* compression. Lossy compression investigation might be a future post.

#### zfp

**zfp** ([website](http://zfp.llnl.gov/), [github](https://github.com/LLNL/zfp)) is a library for either lossy or lossless compression
of 1D-4D data (floats and integers, 32 and 64 bit sizes of either). I've seen it before in [EXR post](http://localhost:1313/blog/2021/08/27/EXR-Filtering-and-ZFP/).

It is similar to GPU [texture compression](/blog/2020/12/08/Texture-Compression-in-2020/) schemes – 2D data is divided
into 4x4 blocks, and each block is encoded completely independently from the others. Inside the block, various
*magic stuff* happens and then, ehh, some bits get out in the end :) The actual algorithm is well
[explained here](https://zfp.readthedocs.io/en/release1.0.0/algorithm.html).

Sounds cool! Let's try it (again, I'm using the lossless mode of it right now). zfp is the red 4-sided star point: \
[{{<img src="/img/blog/2023/float-compr/05-float-compt-a-zfp.png">}}](/img/blog/2023/float-compr/05-float-compt-a-zfp.html)

Ouch. I hope I "used it wrong" in some way? But this is not looking great. Ratio is *under* 1, i.e. it makes the data *larger*, and
is *slow* to decompress.

I tried compressing each data file as one "zfp field" (`width*channels` x `height` size), as well as `channels` separate fields of `width` x `height` size,
interleaved in memory as they are, and the results are fairly similarly disappointing. Source code of my usage of it is
[around here](https://github.com/aras-p/float_compr_tester/blob/9f99468/src/compressors.cpp#L527).

Oh well, maybe *zfp* is more targeted at lossy compression? Or more towards 3D or 4D data? Let's move on.

#### fpzip

**fpzip** ([website](http://fpzip.llnl.gov/), [github](https://github.com/LLNL/fpzip)) is from the same research group as *zfp*,
and is their *previous* floating point compressor. It can also be both lossless and lossy, but from the description seems to
be more targeted at lossless case. Let's try it out (fpzip is the 5-sided star): \
[{{<img src="/img/blog/2023/float-compr/05-float-compt-b-fpzip.png">}}](/img/blog/2023/float-compr/05-float-compt-b-fpzip.html)

Ok, this is *way* better compression ratio compared to *zfp*. Here I'm first splitting the data by floats (see
[part 3 post](/blog/2023/02/01/Float-Compression-3-Filters/)) so that all water heights are together, all velocities are together, etc.
Without doing that, fpzip does not get any good compression ratio. Code is [here](https://github.com/aras-p/float_compr_tester/blob/9f99468/src/compressors.cpp#L462).

* Compression ratio and performance is really good! Our data set gets down to 24.8MB (not far from "split bytes + delta + zstd" 22.9MB or
  "mesh optimizer + zstd" 24.3MB). Compresses in 0.6 seconds.
* However decompression performance is disappointing; takes same time as compression at 0.6 seconds -- so between 5x and 10x slower than other
  best approaches so far.

#### SPDP

**SPDP** ([website](https://userweb.cs.txstate.edu/~burtscher/research/SPDPcompressor/), [2018 paper](http://cs.txstate.edu/~mb92/papers/dcc18.pdf))
is interesting, in that it is a "generated" algorithm. They developed a number of building blocks (reorder something, delta, LZ-like compress, etc.)
and then tested millions of their possible combinations on some datasets, and picked the winner. Source code only comes either as a HDF5 filter,
or as a standalone command line utility. I had to modify it slightly ([code](https://github.com/aras-p/float_compr_tester/tree/9f9946846/libs/spdp))
to be usable as a library, and to not use 1MB of stack space which Windows does not appreciate :)

A series of six-sided stars here: \
[{{<img src="/img/blog/2023/float-compr/05-float-compt-c-spdp.png">}}](/img/blog/2023/float-compr/05-float-compt-c-spdp.html)

* Similar to *fpzip* case, I had to split the data by floats ([code](https://github.com/aras-p/float_compr_tester/blob/9f99468/src/compressors.cpp#L592))
  to get better compression ratio.
* Compression ratio is between regular *zstd* and *lz4*, and is a far behind the best options.
* It is about twice as fast as *fpzip* at both compression and decompression, which is still far behind the better options.

The idea of having a bunch of building blocks and automatically picking their best sequence / combination on a bunch of data sets is interesting though.
Maybe they should have had stronger building blocks though (their LZ-like codec might be not super good, I guess).

#### ndzip

**ndzip** ([github](https://github.com/celerity/ndzip),
[2021 paper](https://dps.uibk.ac.at/~fabian/publications/2021-ndzip-a-high-throughput-parallel-lossless-compressor-for-scientific-data.pdf)) promises a
"efficient implementation on modern SIMD-capable multicore processors, it compresses and decompresses data at speeds close to main memory bandwidth,
significantly outperforming existing schemes", let's see about it!

Note that I have not used or tested multi-threaded modes of any of the compressors present. Some can do it; all could do it if incoming data was split into
some sort of chunks (or after splitting "by floats", each float block compressed in parallel). But that's not for today.

`ndzip` does not build on Windows out of the box (I opened a [PR](https://github.com/celerity/ndzip/pull/7) with some fixes), and for the multi-threaded
code path it uses OpenMP features that MSVC 2022 seems to not have. It also is specifically designed for AVX2, and and that needs a bit of juggling to
get compiled on Windows too. On Mac, it does not link due to some symbol issues related to STL (and AVX2 code path would not really work on an arm64).
On Linux, the multi-threaded OpenMP path does not produce correct results, but single-threaded path does. Huge props for releasing source code, but all of
this *does* sound more like a research project that is not *quite* yet ready for production use :)

Anyway, 7-sided yellow star here: \
[{{<img src="/img/blog/2023/float-compr/05-float-compt-d-ndzip.png">}}](/img/blog/2023/float-compr/05-float-compt-d-ndzip.html)

* Similar to others, I split data by floats first, or otherwise it does not achieve pretty much any compression.
* Now this one *does* achieve a place on the Pareto frontier for compression. The ratio is well behind the best possible (file size gets to 38.1MB; best others
  go down to 23MB), but it does compress at over 1GB/s. So if you need that kind of compression performance, this one's interesting.
* They also have CUDA and SYCL code for GPUs too. I haven't tried that.

#### streamvbyte

**streamvbyte** ([github](https://github.com/lemire/streamvbyte),
[blog post](https://lemire.me/blog/2017/09/27/stream-vbyte-breaking-new-speed-records-for-integer-compression/),
[2017 paper](https://arxiv.org/abs/1709.08990)) is ***not*** meant for compressing floating point data; it is targeted as compressing 4-byte integers.
But hey, there's no law saying we can't pretend our floats are integers, right?

[{{<img src="/img/blog/2023/float-compr/05-float-compt-e-streamvbyte.png">}}](/img/blog/2023/float-compr/05-float-compt-e-streamvbyte.html)

* Three-sided star is regular `streamvbyte`. Only 1.2x compression ratio, but it is the fastest of the bunch; compressing at 5.7GB/s, decompressing at \~10GB/s.
* There's also `streamvbyte_delta`, which on unfiltered data is not really good (not shown here).
* However (similar to others), if the data is first split by floats, then `streamvbyte_delta` followed by a general purpose compressor is quite good. Especially
  if you need compression faster than 0.5GB/s, then "split by floats, streamvbyte_delta, zstd" is on the Pareto frontier, reaching 3.5x ratio.

### Conclusion and what's next

On *this* data set, in lossless mode, most of the compression libraries I tried in this post are not very impressive. My guess is that's a combination of
several factors: 1) maybe they are primarily targeted at double precision, and single precision floats somewhat of an afterthought, 2) maybe they are much
better at 3D or 4D data, and are weaker at a mix of 2D and 1D data like in my case, 3) maybe they are much better when used in lossy compression mode.

However it is curious that some of the papers describing these compressors only either compare them to other scientific compressors, or only to regular
lossless compression algorithms. So they go with conclusions like "we surpass zstd a bit!" and declare that a win, without comparing to something like
"filter the data, and then zstd it".

Another interesting aspect is that most of these libraries have symmetric compression and decompression performance, which is very different from
most of regular data compression libraries, where compression part is often much slower.

Next up: either look into lossy compression, or into speeding up the data filtering part. Until then!
