+++
comments = true
date = "2016-09-13T13:36:09+03:00"
tags = ['rendering']
title = "Shader Compression: Some Data"
+++

One common question I had about [SPIR-V Compression](/blog/2016/09/01/SPIR-V-Compression/) is "why compress shaders
at all?", coupled with question on how SPIR-V shaders compare with shader sizes on other platforms.

Here's some data _(insert usual caveats: might be not representative, etc. etc.)_.


#### Unity Standard shader, synthetic test

Took Unity's [Standard shader](https://docs.unity3d.com/Manual/shader-StandardShader.html), made some content
to make it expand into 482 actual shader variants _(some variants to handle different options in the UI,
some to handle different lighting setups, lightmaps, shadows and whatnot etc.)_. This is purely a synthetic
test, and not an indicator of any "likely to match real game data size" scenario.

These 482 shaders, when compiled into various graphics API shader representations with our current (Unity 5.5)
toolchain, result in sizes like this:

<table class="table-cells">
<tr><th>API</th><th>Uncompressed MB</th><th><a href="https://cyan4973.github.io/lz4/">LZ4HC</a> Compressed MB</th></tr>
<tr><td>D3D9</td>	<td class="ar good2">1.04</td>	<td class="ar good3">0.14</td></tr>
<tr><td>D3D11</td>	<td class="ar">1.39</td>	<td class="ar good2">0.12</td></tr>
<tr><td>Metal</td>	<td class="ar bad3">2.55</td>	<td class="ar">0.20</td></tr>
<tr><td>OpenGL</td>	<td class="ar">2.04</td>	<td class="ar">0.15</td></tr>
<tr><td>Vulkan</td>	<td class="ar bad1">6.84</td>	<td class="ar bad1">1.63</td></tr>
<tr><td>Vulkan + <a href="https://github.com/aras-p/smol-v">SMOL-V</a></td>	<td class="ar">1.94</td>	<td class="ar bad3">0.43</td></tr>
</table>

Basically, **SPIR-V shaders are 5x larger than D3D11 shaders**, and 3x larger than GL/Metal shaders. When
compressed (LZ4HC used since that's what we use to compress shader code), they are 12x larger
than D3D11 shaders, and 8x larger than GL/Metal shaders.

Adding [SMOL-V](https://github.com/aras-p/smol-v) encoding gets SPIR-V shaders to "only" be 3x larger than
shaders of other APIs when compressed.


#### Game, full set of shaders


I also got numbers from one game developer trying out SMOL-V on their game. The game is inhouse engine, size of
full game shader build:

<table class="table-cells">
<tr><th>API</th><th>Uncompressed MB</th><th>Zipped MB</th></tr>
<tr><td>D3D11</td>	<td class="ar good3">44</td>	<td class="ar good3">14</td></tr>
<tr><td>Vulkan + <a href="https://github.com/KhronosGroup/glslang/blob/master/README-spirv-remap.txt">remap</a></td>	<td class="ar bad1">178</td>	<td class="ar bad1">62</td></tr>
<tr><td>Vulkan + <a href="https://github.com/KhronosGroup/glslang/blob/master/README-spirv-remap.txt">remap</a> + <a href="https://github.com/aras-p/smol-v">SMOL-V</a></td>	<td class="ar bad3">83</td>	<td class="ar bad3">54</td></tr>
</table>

Here again, SPIR-V shaders are several times larger (than for example DX11), even after remapping + compression.
Adding SMOL-V makes them a bit smaller (I suspect _without_ remapping it might be even smoller).


#### In context

However, in the bigger picture shader compression might indeed not be a thing worth looking into. As always,
"it depends"...

Adding smol-v encoding on this game's data saved 15 megabytes. On one hand, it's ten floppy disks,
which is almost as much as entire
[Windows 95 took](https://blogs.msdn.microsoft.com/oldnewthing/20050819-10/?p=34513)! On the other hand, it's about
the size of one 4kx4k texture, when DXT5/BC7 compressed.

So yeah. YMMV.

