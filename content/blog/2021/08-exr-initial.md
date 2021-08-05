---
title: "EXR: Lossless Compression"
date: 2021-08-04T17:05:10+03:00
tags: ['rendering']
comments: true
---

One thing led to another, and I happened to be looking at various lossless compression options available in
[OpenEXR](https://en.wikipedia.org/wiki/OpenEXR) image file format.

EXR has several lossless compression options, and most of the available material (e.g.
"[Technical Introduction to OpenEXR](https://www.openexr.com/documentation/TechnicalIntroduction.pdf)" and others)
basically end up saying: Zip compression is slow to write, but fast to read; whereas PIZ compression is faster to
write, but slower to read than Zip. PIZ is the default one used by the library/API.

How "slow" is Zip to write, and how much "faster" is PIZ? I decided to figure that out :)

### Test setup

Hardware: MacBookPro 16" (2019, Core i9 9980HK, 8 cores / 16 threads). I used latest OpenEXR version
([3.1.1](https://github.com/AcademySoftwareFoundation/openexr/releases/tag/v3.1.1)),
compiled with Apple Clang 12.0 in `RelWithDebInfo` configuration.

Everything was tested on a bunch of EXR images of various types: rendered frames, HDRI skyboxes, lightmaps,
reflection probes, etc. All of them tend to be "not too small" -- 18 files totaling 1057 MB of raw uncompressed
(RGBA, 16-bit float) data.

[{{<img src="/img/blog/2021/exr/tnRender2.png" width="100px" title="2048x2048, render of Blender 'Monster Under the Bed' sample scene">}}](/img/blog/2021/exr/tnRender2.png)
[{{<img src="/img/blog/2021/exr/tnLightmap1.png" width="100px" title="1024x1024, lightmap from a Unity project">}}](/img/blog/2021/exr/tnLightmap1.png)
[{{<img src="/img/blog/2021/exr/tnLightmap2.png" width="100px" title="1024x1024, lightmap from a Unity project">}}](/img/blog/2021/exr/tnLightmap2.png)
[{{<img src="/img/blog/2021/exr/tnFxDepth.png" width="100px" title="1180x1244, a depth buffer pyramid atlas">}}](/img/blog/2021/exr/tnFxDepth.png)
[{{<img src="/img/blog/2021/exr/tnFxDistort.png" width="100px" title="256x256, file used for VFX in a Unity project">}}](/img/blog/2021/exr/tnFxDistort.png)
[{{<img src="/img/blog/2021/exr/tnFxExplosion.png" width="100px" title="1024x1024, prerendered explosion flipbook">}}](/img/blog/2021/exr/tnFxExplosion.png)
[{{<img src="/img/blog/2021/exr/tnNormal.png" width="100px" title="4096x4096, 'rocks_ground_02' normal map from Polyhaven">}}](/img/blog/2021/exr/tnNormal.png)
[{{<img src="/img/blog/2021/exr/tnPhotoDLAD.png" width="150px" title="2048x1556, ACES reference 'DigitalLAD' image">}}](/img/blog/2021/exr/tnPhotoDLAD.png)
[{{<img src="/img/blog/2021/exr/tnPhotoStillLife.png" width="150px" title="1920x1080, ACES reference 'SonyF35.StillLife' image">}}](/img/blog/2021/exr/tnPhotoStillLife.png)
[{{<img src="/img/blog/2021/exr/tnRender1.png" width="150px" title="3840x2160, render of Blender 'Lone Monk' sample scene">}}](/img/blog/2021/exr/tnRender1.png)
[{{<img src="/img/blog/2021/exr/tnRender3.png" width="150px" title="4096x2560, render from Houdini">}}](/img/blog/2021/exr/tnRender3.png)
[{{<img src="/img/blog/2021/exr/tnRender4.png" width="150px" title="4096x2560, render from Houdini">}}](/img/blog/2021/exr/tnRender4.png)
[{{<img src="/img/blog/2021/exr/tnSky1.png" width="150px" title="8192x4096, 'Gareoult' from Unity HDRI Pack">}}](/img/blog/2021/exr/tnSky1.png)
[{{<img src="/img/blog/2021/exr/tnSky2.png" width="150px" title="8192x4096, 'Kirby Cove' from Unity HDRI Pack">}}](/img/blog/2021/exr/tnSky2.png)
[{{<img src="/img/blog/2021/exr/tnSky3.png" width="150px" title="4096x2048, 'lilienstein' HDRI from Polyhaven">}}](/img/blog/2021/exr/tnSky3.png)
[{{<img src="/img/blog/2021/exr/tnSky4.png" width="150px" title="2048x1024, 'Treasure Island' from Unity HDRI Pack">}}](/img/blog/2021/exr/tnSky4.png)
[{{<img src="/img/blog/2021/exr/tnRefl1.png" width="300px" title="1536x256, reflection probe from a Unity project">}}](/img/blog/2021/exr/tnRefl1.png)
[{{<img src="/img/blog/2021/exr/tnRefl2.png" width="300px" title="1536x256, reflection probe from a Unity project">}}](/img/blog/2021/exr/tnRefl2.png)


### What are we looking for?

As with any lossless compression, there are at least three factors to consider:

* **Compression ratio**. The larger, the better (e.g. "4.0" ratio means it produces 4x smaller data).
* **Compression performance**. How fast does it compress the data?
* **Decompression performance**. How fast can the data be decompressed?

Which ones are more important than others depends, *as always*, on a lot of factors. For example:

* If you're going to write an EXR image once, and use it *a lot* of times (typical case: HDRI textures), then compression
  performance does not matter that much. On the other hand, if for each written EXR image it will get read just
  once or several times (typical case: capturing rendered frames for later encoding into a movie file), then you
  would appreciate faster compression.
* The slower your storage or transmission medium is, the more you care about compression ratio. Or to phrase it differently:
  the slower I/O is, the more CPU time you are willing to spend to reduce I/O data size.
* Compression ratio can also matter when data size is *costly*. For example, modern SSDs might be fast, but their capacity
  still be a limiting factor. Or a network transmission of files might be fast, but you're paying for bandwidth used.

There are other things to keep in mind about compression: memory usage, technical complexity of compressor/decompressor,
ability to randomly access parts of image without decompressing everything else,
etc. etc., but let's not concern ourselves with those right now :)


### Initial (bad) result

What do we have here? *(click for a larger interactive chart)*

[{{<img src="/img/blog/2021/exr/exr01-initial.png">}}](/img/blog/2021/exr/exr01-initial.html)

This is two plots of compression ratio vs. compression performance, and compression ratio vs. decompression performance.
In both cases, the best place on the chart is top right -- the largest compression ratio, and the best performance.

For performance, I'm measuring it in MB/s, in terms of *uncompressed data size*. That is, if we have 1GB worth of raw image
pixel data and processing it took half a second, that's 2GB/s throughput (even if *compressed data size* might be different).

The time it has taken to write or read the *file itself* is included into the measurement. This does mean that
results are not only CPU dependent, but also storage (disk speed, filesystem speed) dependent. My test is on 2019
MacBookPro, which is "quite fast" SSD for today, and average (not too fast, not too slow) filesystem. I'm flushing the
OS file cache between writing and reading the file (via `system("purge")`) so that EXR file reading is closer to a
"read a new file" scenario.

What we can see from the above is that:

* Writing an uncompressed EXR goes at about 400 MB/s, reading at 1400 MB/s,
* Zip and PIZ compression ratio is roughly the same (2.4x),
* Compression and decompression performance is **quite terrible**. Why?

Turns out, OpenEXR library is single-threaded by default. The file format itself is much better than the image formats of yore
(e.g. PNG, which is *completely single threaded, fully, always*) -- EXR format in most cases splits up the whole image into smaller
chunks that can be compressed and decompressed independently. For example, Zip compression does it on 16 pixel row chunks --
this loses some of the compression ratio, but each 16-row image slice *could* be compressed & decompressed in parallel.

If you tell the library to use multiple threads, that is. By default it does not. So, one call to
`Imf::setGlobalThreadCount()` later...


### Threaded result

[{{<img src="/img/blog/2021/exr/exr02-threaded.png">}}](/img/blog/2021/exr/exr02-threaded.html)

There, much better! (16 threads on this machine)

* **Compression ratio**: `Zip` and `PIZ` EXR compression types both have very similar compression ratio, making the data 2.4x smaller.
* **Writing**: If you want to *write* EXR files fast, you want **PIZ**. It's *faster* than writing them uncompressed (400 -> 600 MB/s),
  and about 3x faster to write than Zip (200 -> 600 MB/s). Zip is about 2x slower to write than uncompressed.
* **Reading**: However, if you mostly care about *reading* files, you want **Zip** instead --
  it's about the same performance as uncompressed (~1600 MB/s), whereas PIZ reads at a lower 1200 MB/s.
* `RLE` compression is fast both at writing and reading, but compression ratio is much lower at 1.7x.
* `Zips` compression is very similar to Zip; it's slightly faster but lower compression ratio. Internally, instead of compressing
16-pixel-row image chunks, it compresses each pixel row independently.


### Next up?

So that was with OpenEXR library and format as-is. In the [next post](/blog/2021/08/05/EXR-Zip-compression-levels/) I'll look at what could be done if, *hypothetically*, one were free to extend of modify the format just a tiny bit. Until then!

