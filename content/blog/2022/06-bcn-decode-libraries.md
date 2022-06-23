---
title: "Comparing BCn texture decoders"
date: 2022-06-23T10:11:10+03:00
tags: ['code', 'performance', 'rendering']
---

PC GPUs use "BCn" texture compression formats *(see "[Understanding BCn Texture Compression Formats](https://www.reedbeta.com/blog/understanding-bcn-texture-compression-formats/)"
by Nathan Reed or "[Texture Block Compression in Direct3D 11](https://docs.microsoft.com/en-us/windows/win32/direct3d11/texture-block-compression-in-direct3d-11)" by Microsoft)*.
While most of the interest is in developing BCn *compressors* *(see "[Texture Compression in 2020](/blog/2020/12/08/Texture-Compression-in-2020/)" post)*, I decided to
look into various available BCn *decompressors*.

Why would you want that? After all, isn't that done by the GPU, magically and efficiently? Normally, yes. Except if for "some reason" you need to access pixels on the CPU,
or perhaps to use BCn data on a GPU that does not support it, or perhaps just to decode a BCn image in order to evaluate the compression quality/error.

Given that the oldest BCn formats (BC1..BC3, aka DXT1..DXT5, aka S3TC) are over twenty years old,
you would think this is a totally solved problem, with plenty of *great* libraries that all
do it correctly, fast and are easy to use.

So did I think :)

The task at hand is this: given an input image or BCn block bits, we want to know resulting
pixel values. Our ideal library would: 1) do this correctly, 2) support all or most of BCn
formats, 3) do it as fast as possible, 4) be easy to use, 5) be easy to build.


### BCn decoding libraries

There are many available out there. Generally more libraries support older BCn formats like BC1 or BC3, with only several that support the more modern ones
like BC6H or BC7 *("modern" is relative; they came out in year 2009)*. Here's the ones I could find, that are usable from C or C++, along with
the versions I tested, licenses and format support:

| Library                                                    |Version              | License         |1 &nbsp;|2 &nbsp;|3 &nbsp;|4U|5U|6U|6S|7 &nbsp;|
| ---                                                        | ---                 | ---             | --- | --- | --- | --- | --- | --- | --- | --- |
| [amd_cmp](https://github.com/GPUOpen-Tools/compressonator) | 2022 Jun 20 (b2b4e) | MIT             |✓|✓|✓|✓|✓|✓\*| |✓|
| [bc7enc_rdo](https://github.com/richgel999/bc7enc_rdo)     | 2021 Dec 27 (e6990) | MIT/PublicDomain|✓| |✓|✓|✓| | |✓|
| [bcdec](https://github.com/iOrange/bcdec)                  | 2022 Jun 23 (e3ca0) | Unlicense       |✓|✓|✓|✓|✓|✓|✓|✓|
| [convection](https://github.com/elasota/ConvectionKernels) | 2022 Jun 23 (35041) | MIT             | | | | | |✓|✓|✓|
| [dxtex](https://github.com/microsoft/DirectXTex)           | 2022 Jun 6  (67953) | MIT             |✓|✓|✓|✓|✓|✓|✓|✓|
| [etcpak](https://github.com/wolfpld/etcpak)                | 2022 Jun 4  (a77d5) | BSD 3-clause    |✓| |✓| | | | | |
| [icbc](https://github.com/castano/icbc)                    | 2022 Jun 3  (502ec) | MIT             |-| |-| | | | | |
| [mesa](https://github.com/mesa3d/mesa)                     | 2022 Jun 22 (ad3d6) | MIT             |✓|✓|✓|✓|✓|✓|✓|✓|
| [squish](https://sourceforge.net/projects/libsquish/)      | 2019 Apr 25 (v1.15) | MIT             |✓|✓|✓| | | | | |
| [swiftshader](https://github.com/google/swiftshader)       | 2022 Jun 16 (2b79b) | Apache-2.0      |✓|✓|✓|✓|✓|✓|✓|✓|

Notes:
* Majority are "texture compression" libraries, that happen to also have decompression functionality in there.
* Some, like `mesa` or `swiftshader`, are much larger projects where texture decoding is only a tiny part of everything they do.
* There's not that many libraries out there that do support all the BCn formats decoding: only `bcdec`, `dxtex`, `mesa` and `swiftshader`.
  Compressonator (`amd_cmp`) gets close, except it does not have BC6H *signed* support.
* I have not looked at BC4 and BC5 *signed* formats, so they are missing from the table.
* `icbc` has functions to decode BC1 and BC3, but produces wrong results. So I have not tested it further.
* `bc7enc` also includes `rgbcx`. Multiple versions of them exist in several repositories, I picked this one
  since it has a BC7 decoder with additional performance optimizations.
* Compressonator (`amd_cmp`) produces visually "ok" results while decoding BC6H format, but it does not match the other decoders
  bit-exactly.

### Decoding performance

I made a small program that loads a bunch of DDS files and decodes them with all the libraries: [**bcn_decoder_tester**](https://github.com/aras-p/bcn_decoder_tester).

Here's decoding performance, in Mpix/s, single-threaded, on Apple M1 Max (arm64 arch, clang13). Higher values are better: \
[{{<img src="/img/blog/2022/bcn-decode-mac.png">}}](/img/blog/2022/bcn-decode-mac.png)

What I did not expect: there's a **up to 20x** speed difference between various decoding libraries!

Here's decoding performance a Windows PC (Ryzen 5950X, x64 arch), using Visual Studio 2022 and clang 14 respectively: \
[{{<img src="/img/blog/2022/bcn-decode-win-vs2022.png">}}](/img/blog/2022/bcn-decode-win-vs2022.png) \
[{{<img src="/img/blog/2022/bcn-decode-win-clang14.png">}}](/img/blog/2022/bcn-decode-win-clang14.png)

### Ease of use / build notes

For "build complexity", I'm indicating source file count & source file size. This is under-counting
in many cases where the library overall is larger and it includes possibly a bunch of other header files it has.
Only read these numbers as very rough ballpark estimates!

| Library   | Source, files | Source, KB |Notes |
| ---       | ---:| ---:| --- |
| bcdec | 1 | 69 | 💙 Everything is great about this one! 💛 |
| dxtex | 4 | 220 | Does not easily build on non-Windows: requires bits of `DirectX-Headers` and `DirectXMath` github projects, as well as an empty `<malloc.h>` and some stub `<sal.h>` [header](https://github.com/aras-p/bcn_decoder_tester/blob/main/libs/sal.h). |
| swiftshader | 6 | 77 | Part of a giant project, but "just the needed" source files can be taken out surprisingly easily. Nice! |
| amd_cmp | 21 | 960 | The github repo is 770MB payload (checkout 60MB). Does not decode BC6H bit-exact like other libraries. |
| mesa | 17 | 137 | Part of a giant project, with quite a lot of header dependencies. Most of decoding functionality is in "give me one pixel" fashion (so not expected to be fast); only BC6H/BC7 operate on whole blocks/images. |
| bc7dec_rdo | 4 | 181 | Two libraries: `bc7decomp` for BC7, and `rgbcx` for BC1/3/4/5. *Way* fastest BC7 decoder (incl. SSE code for x64). |
| squish | 24 | 160 | The library claims to support BC4/BC5, but really only for compression; decoding produces wrong results. Official repository in Subversion only. |
| etcpak | 3 | 7 | Hard to use only the decoder functionality without building the whole library. I took only the decoder-related files and modified them to remove everything not relevant. |
| convection | 9 | 487 | Simple to use, only BC6H/BC7 though. Fastest BC6H decoder (by a small margin). |


### So which BCn decoder should I use?

For me, using [**bcdec**](https://github.com/iOrange/bcdec) was easiest; it's also the most "direct fit" -- it's a library that does one thing, and one thing only (decode BCn).
Trivial to build, easy to use, supports all BCn formats. Initially I was a bit reserved about decoding performance, but then I did some
[low hanging fruit speedups](https://github.com/iOrange/bcdec/pull/1) and they got prompty merged, nice :) So right now: bcdec is easiest to use, and *almost always* the fastest
one too. 🎉

I can't quite recommend [DirectXTex](https://github.com/microsoft/DirectXTex) -- while it sounds like the most natural place to search for BCn decoders, it's both quite a hassle to build (at least outside of Windows), and is quite slow. Maybe the code is supposed to be "reference", and not fast.

If you need fastest BC7 decoding, use [bc7enc_rdo](https://github.com/richgel999/bc7enc_rdo) decoder.
For fastest BC1/BC3 decoding, use [etcpak](https://github.com/wolfpld/etcpak) (but it's a bit of
a hassle to build only the decoder).



*That's it!*


