---
title: "EXR: Zip compression levels"
date: 2021-08-05T07:16:10+03:00
tags: ['rendering', 'code']
comments: true
---

In the [previous blog post](/blog/2021/08/04/EXR-Lossless-Compression/) I looked at lossless compression options that
are available in [OpenEXR](https://en.wikipedia.org/wiki/OpenEXR).

The `Zip` compression in OpenEXR is just the standard [DEFLATE](https://en.wikipedia.org/wiki/Deflate) algorithm
as used by Zip, gzip, PNG and others. That got me thinking - the compression has different "compression levels" that
control ratio vs. performance. Which one is OpenEXR using, and would changing them affect anything?

OpenEXR seems to be *mostly* using the default zlib compression level (6). It uses level 9 in several places (within the
lossy DWAA/DWAB compression), we'll ignore those for now.

Let's try all the zlib compression levels, 1 to 9 *(click for an interactive chart)*:

[{{<img src="/img/blog/2021/exr/exr03-zip-levels.png">}}](/img/blog/2021/exr/exr03-zip-levels.html)

* The Zip compression level used in current OpenEXR is level 6, marked with the triangle shape point on the graph.
* Compression ratio is not affected much by the level settings - fastest level (1) compresses data 2.344x; slowest (9) compresses
  at 2.473x.
* Levels don't affect decompression performance much.
* Maybe **level 4 should be the default** (marked with a star shape point on the graph)? It's a *tiny* compression ratio drop
  (2.452x -> 2.421x), but compression is ***over 2x faster*** (206 -> 437 MB/s)! At level 4, writing a Zip-compressed EXR
  file becomes faster than writing an uncompressed one.
  * Just a [tiny 4 line change](https://github.com/aras-p/openexr/commit/467deda3f5a) in OpenEXR library source code would be
    enough for this.
  * A huge advantage is that this does not change the compression format at all. All the existing EXR decoding software
    can still decode the files just fine; it's still exactly the same compression algorithm.

With a [bit more changes](https://github.com/aras-p/openexr/commit/da7086d976d4), it should be possible to
make the Zip compression level be configurable, like so:

```c++
Header header(width, height);
header.compression() = ZIP_COMPRESSION;
addZipCompressionLevel(header, level); // <-- new!
RgbaOutputFile output(filePath, header);
```

So that's it. I think **switching OpenEXR from Zip compression level 6 to level 4 by default** should be a no-brainer.


### Next up

In the next post I'll try adding a new lossless compression algorithm to OpenEXR and see what happens.

