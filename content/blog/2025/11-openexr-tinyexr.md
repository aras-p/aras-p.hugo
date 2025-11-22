---
title: "OpenEXR vs tinyexr"
date: 2025-11-22T12:25:10+03:00
tags: ['compression', 'performance']
---

[**tinyexr**](https://github.com/syoyo/tinyexr) is an excellent simple library for loading and saving OpenEXR
files. It has one big advantage, in that it is _very_ simple to start using: just one
source file to compile and include! However, it also has some downsides, namely that
not all features of OpenEXR are supported (for example, it can't do PXR24, B44/B44A,
DWAA/DWAB, HTJ2K compression modes), and performance might be behind the official
library. *It probably can't do some of more exotic EXR features either (e.g. "deep" images),
but I'll ignore those for now.*

But how _large_ and how _complex to use_ is the "official" [OpenEXR library](https://github.com/AcademySoftwareFoundation/openexr),
anyways?

> I do remember that a decade ago it was quite painful to build it, especially on anything
> that is not Linux. However these days (2025), that seems to be much simpler: it
> uses a CMake build system, and either directly vendors or automatically fetches whatever
> dependencies it needs, unless you really ask it to "please don't do this".

It is not exactly a "one source file" library though. However, I noticed that [OpenUSD](https://github.com/PixarAnimationStudios/OpenUSD)
vendors OpenEXR "Core" library, builds it as a single C source file, and uses their
own "nanoexr" wrapper around the API; see
[pxr/imaging/plugin/hioOpenEXR/OpenEXR](https://github.com/PixarAnimationStudios/OpenUSD/tree/8d2d14db0/pxr/imaging/plugin/hioOpenEXR/OpenEXR).
So I took that, adapted it to more recent OpenEXR versions (theirs uses 3.2.x, I updated to 3.4.4).

So I wrote a tiny app ([github repo](https://github.com/aras-p/test_exrcore_tinyexr))
that reads an EXR file, and writes it back as downsampled EXR
(so this includes both reading & writing parts of an EXR library). And compared how large
is the binary size between `tinyexr` and `OpenEXR`, as well as their respective
source code sizes and performance.

Actual process was:

- Take OpenEXR source repository (v3.4.4, 2025 Nov),
  - Take only the `src/lib/OpenEXRCore` and `external/deflate` folders from it.
  - `openexr_config.h`, `compression.c`, `internal_ht.cpp` have local changes!
    Look for `LOCAL CHANGE` comments.
- Take [OpenJPH](https://github.com/aous72/OpenJPH) source code, used 0.25.3 (2025 Nov),
  put under `external/OpenJPH`.
- Take `openexr-c.c`, `openexr-c.h`, `OpenEXRCoreUnity.h` from the OpenUSD repository.
  They were for OpenEXR v3.2, and needed some adaptations for later versions.
  OpenJPH part can't be compiled as C, nor compiled as "single file",
  so just include these source files into the build separately.
- Take `tinyexr` source repository (v1.0.12, 2025 Mar).

## Results

| Library | Binary size, KB | Source size, KB | read+write time, s | Notes |
|----|---:|---:|---:|---|
|tinyexr 1.0.12       |  251 |  726 | 6.55 | |
|OpenEXR 3.2.4        | 2221 | 8556 | 2.19 | |
|OpenEXR 3.3.5        |  826 | 3831 | 1.68 | Removed giant DWAA/DWAB lookup tables. |
|OpenEXR 3.4.3        | 1149 | 5373 | 1.68 | Added HTJ2K compression (via OpenJPH). |
|OpenEXR 3.4.4        |  649 | 3216 | 1.65 | Removed more B44/DWA lookup tables. |
| + no HTJ2K          |  370 | 1716 |      | Above, with HTJ2K/OpenJPH compiled out. |
| + no DWA            |  318 |      |      | Above, and with DWAA/DWAB compiled out. |
| + no B44            |  305 |      |      | Above, and with B44/B44A compiled out. |
| + no PXR24          |  303 |      |      | Above, and with PXR24 compiled out. |


Notes:
- Machine is Ryzen 5950X, Windows 10, compiler Visual Studio 2022 (17.14), Release build.
- This compares both tinyexr and OpenEXR in fully single-threaded mode. Tinyexr has threading
  capabilities, but it spins up and shuts down a whole thread pool for each processed image,
  which is a bit "meh"; and while OpenEXRCore can be threaded (and using full high level
  OpenEXR library does use it that way), the "nanoexr" wrapper I took from USD codebase
  does not do any threading.
- Timing is total time taken to read, downsample (by 2x) and write back 6 EXR files,
  input resolution 3840x2160, input files are ZIP FP16, ZIP FP32, ZIP w/ mips, ZIP tiled,
  PIZ and RLE compressed; output is ZIP compressed.

*That's it!*
