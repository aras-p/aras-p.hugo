---
title: "Float Compression 2: Oodleflate"
date: 2023-01-31T16:10:10+03:00
tags: ['code', 'performance']
---

*Introduction and index of this series [is here](/blog/2023/01/29/Float-Compression-0-Intro/)*.

In the [previous part](/blog/2023/01/29/Float-Compression-1-Generic/) I looked at generic
lossless data compressors (zlib, lz4, zstd, brotli),
and was thinking of writing about data filtering next. But then,
[libdeflate](https://github.com/ebiggers/libdeflate) and [Oodle](http://www.radgametools.com/oodlecompressors.htm) entered
the chat!

#### libdeflate

[**libdeflate**](https://github.com/ebiggers/libdeflate) is an independent implementation of zlib/deflate
compression, that is heavily optimized. I've seen it before ([EXR blog post](/blog/2021/08/09/EXR-libdeflate-is-great/)),
and indeed it is very impressive, considering it produces completely zlib-compatible data, but much faster and at
a bit higher compression ratio too.

Here are the results *(click for an interactive chart)*. The compressors from the previous post are faded out; libdeflate
addition is dark green: \
[{{<img src="/img/blog/2023/float-compr/float-compr-generic-pc-libdeflate.png">}}](/img/blog/2023/float-compr/02-float-comp-generic-amd-msvc-libdeflate.html)

It is not better than zstd, but definitely way better than zlib (\~3x faster compression, \~2x faster decompression, slightly higher ratio).
If you *need* to produce or consume deflate/zlib/gzip formatted data, then absolutely look into libdeflate.

#### Oodle

[**Oodle**](http://www.radgametools.com/oodlecompressors.htm) is a set of compressors by Epic Games Tools (née RAD Game Tools).
It is ***not*** open source or freely available, but the good people there graciously gave me the libraries to play around with.

There are several compressors available, each aiming at different ratio vs. performance tradeoff. It's easiest to think of them as:
"Kraken" is "better zstd" (great ratio, good decompression performance), "Selkie" is "better lz4" (ok ratio, very fast decompression),
and "Mermaid" is in the middle of the two, with no clear open source competitor.

Here they are, additions in various shades of purple: \
[{{<img src="/img/blog/2023/float-compr/float-compr-generic-pc-oodle.png">}}](/img/blog/2023/float-compr/02-float-comp-generic-amd-msvc-oodle.html)

And... yeah. On the compression front, Mermaid leaves everyone else (zstd, brotli) in the dust on the Pareto front (remember: upper right is better).
And then Kraken improves on top of that 🤯 Decompression performance is a similar story: everything (except lz4) is behind.

Wizardry! It's a shame they are not freely available, eh :/



#### What's next?

Next up, we'll *really* look at some simple data filtering (i.e. fairly similar to [EXR post](/blog/2021/08/27/EXR-Filtering-and-ZFP/) from a while ago).
[Until then](/blog/2023/02/01/Float-Compression-3-Filters/)!
