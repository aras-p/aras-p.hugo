---
title: "SPIR-V Compression: SMOL vs MARK"
date: 2018-10-31T19:10:10+03:00
tags: ['code', 'vulkan', 'rendering', 'rant', 'compression']
comments: true
---

Two years ago I did a small utility to help with Vulkan (SPIR-V) shader compression: SMOL-V
(see [blog post](/blog/2016/09/01/SPIR-V-Compression/) or [github repo](https://github.com/aras-p/smol-v)).

It is used by Unity, and looks like also used by some non-Unity projects as well *(if you use it, [let me know](mailto:aras@nesnausk.org)!
always interesting to see where it ends up at)*.

Then I remembered the [github issue](https://github.com/KhronosGroup/SPIRV-Tools/issues/382) where SPIR-V compression was discussed at. 
It mentioned that SPIRV-Tools was getting some sort of "compression codec"
([see comments](https://github.com/KhronosGroup/SPIRV-Tools/issues/382#issuecomment-349681127)) and got closed as "done",
so I decided to check it out.


### SPIRV-Tools compression: MARK-V

[SPIRV-Tools repository](https://github.com/KhronosGroup/SPIRV-Tools), which is a collection of libraries and tools for
processing SPIR-V shaders (validation, stripping, optimization, etc.) has a compressor/decompressor in there too, but it's
not advertised much. It's *not* built by default; and requires passing a `SPIRV_BUILD_COMPRESSION=ON` option to CMake build.

The sources related to it are under `source/comp` and `tools/comp` folders; and compression is not part of the main interfaces under
`include/spirv-tools` headers; you'd have to manually include `source/comp/markv.h`. The build also produces a command line executable
`spirv-markv` that can do encoding or decoding.

The code is well commented in terms of "here's what this small function does", but I didn't find any high level description of
"the algorithm" or properties of the compression. I see that it does *something* with shader instructions; there's *some* Huffman related things
in there, and large tables that are seemingly auto-generated somehow.

*Let's give it a go!*


#### Getting MARK-V to compile

In SMOL-V repository I have a little test application (see [testmain.cpp](https://github.com/aras-p/smol-v/blob/677d5fedf/testing/testmain.cpp))
that has on a bunch of shaders, runs either SMOL-V or Spirv-Remapper on them, additionally
compresses result with zlib/lz4/zstd and so on. *"Let's add MARK-V in there too"* sounded like a natural thing to do.
And since I refuse to deal with CMake in my hobby projects :), I thought I'd just add relevant MARK-V source files...

First *"uh oh"* sign: while the number of files under compression related folders (`source/comp`, `tools/comp`) is not high, that is
500 kilobytes of source code. *Half a meg of source, Carl!*

And then *of course* it needs a whole bunch of surrounding code from SPIRV-Tools to compile. So I copied everything that it needed
to work. In total, 1.8MB of source code across 146 files.

After finding all the source files and setting up include paths for them, it compiled easily on both Windows (VS2017) and Mac (Xcode 9.4).

> Pet peeve: I never understood why people don't use file-relative include paths (like `#include "../foo/bar/baz.h"`), instead
> requiring the **users** of your library to setup additional include path compiler flags. As far as I can tell, relative include paths have no downsides,
> and require way less fiddling to both compile your library and use it.


#### Side issue: STL vector for input data

The main entry point for MARK-V *decoding* (this is what would happen *on the device* when loading shaders -- so this *is*
the performance critical part) is:

```c++
spv_result_t MarkvToSpirv(
    spv_const_context context, const std::vector<uint8_t>& markv,
    const MarkvCodecOptions& options, const MarkvModel& markv_model,
    MessageConsumer message_consumer, MarkvLogConsumer log_consumer,
    MarkvDebugConsumer debug_consumer, std::vector<uint32_t>* spirv);
```

Ok, I kind of get the need (or at least convenience) of using `std::vector` for *output* data; after all you are decompressing and writing out
an expanding array. Not ideal, but at least there is *some* explanation.

But for *input* data -- why?! One of `const uint8_t* markv, size_t markv_size` or a `const uint8_t* markv_begin, const uint8_t* markv_end`
is just as convenient, and allows *way* more flexibility for the user at where the data is coming from. I might have loaded my data
as memory-mapped files, which then literally is just a pointer to memory. Why would I have to copy that data into an additional STL
vector just to use your library?


#### Side issue: found bugs in "Max" compression

MARK-V has three compression models - "Lite", "Mid" and "Max". On some test shaders I had the "Max" one could not decompress successfully
after compression, so I guess "some bugs are there somewhere". Filed a [bug report](https://github.com/KhronosGroup/SPIRV-Tools/issues/2015)
and excluded the "Max" model from further comparison :( 


### MARK-V vs SMOL-V

#### Size evaluation

<table class="table-cells">
<tr><th rowspan="2" width="10%">Compression</th><th colspan="2">No filter</th><th colspan="2">SMOL-V</th><th colspan="2">MARK-V Lite</th><th colspan="2">MARK-V Mid</th></tr>
<tr><th width="9%">Size KB</th><th width="9%">Ratio</th><th width="9%">Size KB</th><th width="9%">Ratio</th><th width="9%">Size KB</th><th width="9%">Ratio</th><th width="9%">Size KB</th><th width="9%">Ratio</th></tr>
<tr><td>Uncompressed</td>	<td class="ar">4870</td><td class="ar">100.0%</td>		<td class="ar">1630</td><td class="ar">33.5%</td>		<td class="ar">1369</td><td class="ar">28.1%</td>		<td class="ar">1085</td><td class="ar">22.3%</td>		</tr>
<tr><td>zlib default</td>	<td class="ar">1213</td><td class="ar">24.9%</td>		<td class="ar">602</td><td class="ar good3">12.4%</td>	<td class="ar">411</td><td class="ar good2">8.5%</td>	<td class="ar">336</td><td class="ar good1">6.9%</td>	</tr>
<tr><td>LZ4HC default</td>	<td class="ar">1343</td><td class="ar">27.6%</td>		<td class="ar">606</td><td class="ar good3">12.5%</td>	<td class="ar">410</td><td class="ar good2">8.4%</td>	<td class="ar">334</td><td class="ar good1">6.9%</td>	</tr>
<tr><td>Zstd default</td>	<td class="ar">899</td><td class="ar">18.5%</td>		<td class="ar">446</td><td class="ar good2">9.1%</td>	<td class="ar">394</td><td class="ar good2">8.1%</td>	<td class="ar">329</td><td class="ar good1">6.8%</td>	</tr>
<tr><td>Zstd level 20</td>	<td class="ar">590</td><td class="ar good3">12.1%</td>	<td class="ar">348</td><td class="ar good2">7.1%</td>	<td class="ar">293</td><td class="ar good1">6.0%</td>	<td class="ar">257</td><td class="ar good1">5.3%</td>	</tr>
</table>

Two learnings from this:

* MARK-V without additional compression on top ("Uncompressed" row) is not really competitive (~25%);
  just compressing shader data with Zstandard produces smaller result; or running through SMOL-V coupled with any
  other compression.
* This suggests that MARK-V acts more like a "filter" (similar to SMOL-V or spirv-remap), that makes
  the data smaller, but *also* makes it more compressible. Coupled with **additional compression, MARK-V
  produces pretty good results**, e.g. the "Mid" model ends up compressing data to ~7% of original size. Nice!


#### Decompression performance

I checked how much time it takes to decode/decompress shaders (4870KB uncompressed size):

<table class="table-cells">
<tr><th width="30%"></th><th width="30%" colspan="2">Windows<br/>AMD TR 1950X<br/>3.4GHz</th><th width="30%" colspan="2">Mac<br/>i9-8950HK<br/>2.9GHz</th></tr>
<tr><td>MARK-V Lite</td><td class="ar bad3">536.7ms</td><td class="ar bad3">9.1MB/s</td>	<td class="ar bad3">492.7ms</td><td class="ar bad3">9.9MB/s</td>	</tr>
<tr><td>MARK-V Mid</td>	<td class="ar bad2">759.1ms</td><td class="ar bad2">6.4MB/s</td>	<td class="ar bad2">691.1ms</td><td class="ar bad2">7.0MB/s</td>	</tr>
<tr><td>SMOL-V</td>		<td class="ar good2">8.8ms</td>	<td class="ar good2">553.4MB/s</td>	<td class="ar good2">11.1ms</td><td class="ar good2">438.7MB/s</td>		</tr>
</table>

Now, I haven't seriously looked at my SMOL-V decompression performance (e.g. Zstandard general
decompression algorithm does ~1GB/s), but at ~500MB/s it's perhaps "not terrible".

I can't quite say the same about MARK-V though; it gets **under 10MB/s of decompression** performance.
That, I think, is "pretty bad". I don't know *what* it does there, but this low decompression speed
is within a "maybe I wouldn't want to use this" territory.


#### Decompressor size

There is only one case where the decompressor code size does not matter: it's if it comes pre-installed
on the end hardware *(as part of OS, runtimes, drivers, etc.)*. In all other cases, you have to ship decompressor
inside your own application, i.e. statically or dynamically link to that code -- so that, well, you can
decompress the data you have compressed.

I evaluated decompressor code size by making a dynamic/shared library on a Mac (`.dylib`) with a single exported
function that does a *"decode these bytes please"* work. I used
`-O2 -fvisibility=hidden -std=c++11 -fno-exceptions -fno-rtti` compiler flags, and
`-shared -fPIC -lstdc++ -dead_strip -fvisibility=hidden` linker flags.

* SMOL-V decompressor .dylib size: <span class="good3">**8.2 kilobytes**</span>.
* MARK-V decompressor .dylib size (only with "Mid" model): <span class="bad3">**1853.2 kilobytes**</span>.

That's right. 1.8 megabytes! At first I thought I did something wrong!

I looked at the size report via [Bloaty](https://github.com/google/bloaty), and yeah, in MARK-V decompressor
it's like: 570KB `GetIdDescriptorHuffmanCodecs`, 137KB `GetOpcodeAndNumOperandsMarkovHuffmanCodec`,
64KB `GetNonIdWordHuffmanCodecs`, 44KB `kOpcodeTableEntries` and then piles and piles of template
instantiations that are smaller, but there's *lots* of them.

In SMOL-V by comparison, it's 2KB `smolv::Decode`, 1.3KB `kSpirvOpData` and the rest is misc stuff and/or
dylib overhead.


#### Library compilation time

While this is not *that* important aspect, it's relevant to my current work role as a build engineer :)

Compiling MARK-V libraries with optimizations on (`-O2`) takes 102 seconds on my Mac (single threaded;
obviously multi-threaded would be faster). It is close to two megabytes of source code after all; and there is one
file (`tools/comp/markv_model_shader.cpp`) that takes 16 seconds to compile alone. I *think* that got CI agents into
timeouts in SPIRV-Tools project, and that was the reason why MARK-V is not enabled by default in the builds :)

Compiling SMOL-V library takes 0.4 seconds in comparison.


### Conclusion

While looking at compression ratio in isolation, MARK-V coupled with additional lossless compression looks good,
I don't think I would recommend it due to other issues.

The decompressor executable size alone (almost 2MB!) means that in order for MARK-V to start to "make sense"
compared to say SMOL-V, your total shader data size needs to be over 100 megabytes; *only then* additional compression
from MARK-V offsets the massive decompressor size.

Sure, there are games with shaders that large, but then MARK-V is also *quite slow* at decompression -- it would take
over 10 seconds to decompress 100MB worth of shader data :(

All my evaluation code is on [`mark-v` branch](https://github.com/aras-p/smol-v/commits/mark-v) in SMOL-V
repository. At this point I'm not sure I'll merge it to the main branch.

*This is all*.