---
layout: page
title: D3D9 GPU Hacks
comments: true
sharing: true
footer: true
section: texts
url: /texts/D3D9GPUHacks.html
---

<p>
I've been trying to catch up what hacks GPU vendors have exposed in Direct3D9,
and turns out there's a lot of them!
</p>
<p>
If you know more hacks or more details, please let me know in the comments!
</p>

<p>
Most hacks are exposed as custom ("FOURCC") formats. So to check for
that, you do <code>CheckDeviceFormat</code>. Here's the list (Usage column codes: DS=DepthStencil,
RT=RenderTarget; Resource column codes: tex=texture, surf=surface). More green = more hardware support.
</p>

<table class="table-cells">
<tr><th>Format</th><th>Usage</th><th>Resource</th><th>Description</th><th>NVIDIA GeForce</th><th>AMD Radeon</th><th>Intel</th></tr>

<tr><th colspan="7"><em>Shadow mapping</em></th></tr>

<tr><td>D3DFMT_D16</td>	<td>DS</td><td>tex</td><td rowspan="2"><a href="#shadowmap">Sample depth buffer directly as shadow map</a>.</td>
	<td style="background-color: #70ff70;">3+</td><td style="background-color: #c0ffc0;">HD 2xxx+</td><td style="background-color: #70ff70;">965+</td></tr>
<tr><td>D3DFMT_D24X8</td><td>DS</td><td>tex</td>
	<td style="background-color: #70ff70;">3+</td><td style="background-color: #c0ffc0;">HD 2xxx+</td><td style="background-color: #70ff70;">965+</td></tr>

<tr><th colspan="7"><em>Depth Buffer As Texture</em></th></tr>

<tr><td>DF16</td><td>DS</td><td>tex</td><td rowspan="4"><a href="#depth">Read depth buffer as texture</a>.</td>
	<td style="background-color: #a0a0a0;"></td><td style="background-color: #70ff70;">9500+</td><td style="background-color: #a0ffa0;">G45+</td></tr>
<tr><td>DF24</td><td>DS</td><td>tex</td>
	<td style="background-color: #a0a0a0;"></td><td style="background-color: #a0ffa0;">X1300+</td><td style="background-color: #e0ffe0;">SB+</td></tr>
<tr><td>INTZ</td><td>DS</td><td>tex</td>
	<td style="background-color: #e0ffe0;">8+</td><td style="background-color: #e0ffe0;">HD 4xxx+</td><td style="background-color: #a0ffa0;">G45+</td></tr>
<tr><td>RAWZ</td><td>DS</td><td>tex</td>
	<td style="background-color: #f8fff8;">6 &amp; 7</td><td style="background-color: #a0a0a0;"></td><td style="background-color: #a0a0a0;"></td></tr>

<tr><th colspan="7"><em>Anti-Aliasing related</em></th></tr>

<tr><td>RESZ</td><td>RT</td><td>surf</td><td>Resolve MSAA'd depth stencil surface into non-MSAA'd depth texture.</td>
	<td style="background-color: #a0a0a0;"></td><td style="background-color: #e0ffe0;">HD 4xxx+</td><td style="background-color: #a0ffa0;">G45+</td></tr>
<tr><td>ATOC</td><td>0</td><td>surf</td><td rowspan="3"><a href="#transpaa">Transparency anti-aliasing</a>.</td>
	<td style="background-color: #c0ffc0;">7+</td><td style="background-color: #a0a0a0;"></td><td style="background-color: #e0ffe0;">SB+</td></tr>
<tr><td>SSAA</td><td>0</td><td>surf</td>
	<td style="background-color: #c0ffc0;">7+</td><td style="background-color: #a0a0a0;"></td><td style="background-color: #a0a0a0;"></td></tr>
<tr><td colspan="3"><em>All AMD DX9+ hardware</em></td>
	<td style="background-color: #a0a0a0;"></td><td style="background-color: #70ff70;">9500+</td><td style="background-color: #a0a0a0;"></td></tr>
<tr><td><em>n/a</em></td><td></td><td></td><td>Coverage Sampled Anti-Aliasing<sup><a href="#ref5">[5]</a></sup></td>
	<td style="background-color: #e0ffe0;">8+</td><td style="background-color: #a0a0a0;"></td><td style="background-color: #a0a0a0;"></td></tr>

<tr><th colspan="7"><em>Texturing</em></th></tr>

<tr><td>ATI1</td><td>0</td><td>tex</td><td rowspan="2"><a href="#3dc">ATI1n &amp; ATI2n</a> texture compression formats.</td>
	<td style="background-color: #e0ffe0;">8+</td><td style="background-color: #a0ffa0;">X1300+</td><td style="background-color: #a0ffa0;">G45+</td></tr>
<tr><td>ATI2</td><td>0</td><td>tex</td>
	<td style="background-color: #a0ffa0;">6+</td><td style="background-color: #70ff70;">9500+</td><td style="background-color: #a0ffa0;">G45+</td></tr>
<tr><td>DF24</td><td>DS</td><td>tex</td><td>Fetch 4: when sampling 1 channel texture,
	return four touched texel values<sup><a href="#ref1">[1]</a></sup>. Check for DF24 support.</td>
	<td style="background-color: #a0a0a0;"></td><td style="background-color: #a0ffa0;">X1300+</td><td style="background-color: #e0ffe0;">SB+</td></tr>

<tr><th colspan="7"><em>Misc</em></th></tr>

<tr><td>NULL</td><td>RT</td><td>surf</td><td>Dummy render target surface that does not consume video memory.</td>
	<td style="background-color: #a0ffa0;">6+</td><td style="background-color: #e0ffe0;">HD 4xxx+</td><td style="background-color: #c0ffc0;">HD+</td></tr>
<tr><td>NVDB</td><td>0</td><td>surf</td><td><a href="#dbt">Depth Bounds Test</a>.</td>
	<td style="background-color: #a0ffa0;">6+</td><td style="background-color: #a0a0a0;"></td><td style="background-color: #a0a0a0;"></td></tr>
<tr><td>R2VB</td><td>0</td><td>surf</td><td><a href="#r2vb">Render into vertex buffer</a>.</td>
	<td style="background-color: #f8fff8;">6 &amp; 7</td><td style="background-color: #70ff70;">9500+</td><td style="background-color: #a0a0a0;"></td></tr>
<tr><td>INST</td><td>0</td><td>surf</td><td><a href="#inst">Geometry Instancing on pre-SM3.0 hardware</a>.</td>
	<td style="background-color: #a0a0a0;"></td><td style="background-color: #70ff70;">9500+</td><td style="background-color: #a0a0a0;"></td></tr>
	
</table>

<a name="shadowmap"></a>
<h3>Native Shadow Mapping</h3>
<p>
Native support for shadow map sampling &amp; filtering was introduced ages ago (GeForce 3) by NVIDIA.
Turns out AMD also implemented the same feature for DX10 level cards. Intel also supports it
on Intel 965 (aka GMA X3100, the shader model 3 card) and later (G45/X4500/HD) cards.
</p>
<p>
The usage is quite simple; just create a texture with regular depth/stencil format and render into
it. When reading from the texture, one extra component in texture coordinates will be the depth
to compare with. Compared &amp; filtered result will be returned.
</p>
<p>
Also useful:
<ul>
	<li>Creating NULL color surface to keep D3D runtime happy and save on video memory.</li>
</ul>
</p>


<a name="depth"></a>
<h3>Depth Buffer as Texture</h3>
<p>
For some rendering schemes (anything with "deferred") or some effects (SSAO, depth of field,
volumetric fog, ...) having access to a depth buffer is needed. If native depth buffer
can be read as a texture, this saves both memory and a rendering pass or extra output for MRTs.
</p>
<p>
Depending on hardware, this can be achieved via INTZ, RAWZ, DF16 or DF24 formats:
<ul>
	<li>INTZ is for recent (DX10+) hardware. With recent drivers, all three major IHVs expose this.
		According to AMD <sup><a href="#ref1">[1]</a></sup>,
		it also allows using stencil buffer while rendering. Also allows reading from depth texture
		while it's still being used for depth testing (but not depth writing). Looks like
		this applies to NV &amp; Intel parts as well.</li>
	<li>RAWZ is for GeForce 6 &amp; 7 series <em>only</em>. Depth is specially encoded into
		four channels of returned value.</li>
	<li>DF16 and DF24 is for AMD and Intel cards, including older cards that don't support INTZ.
		Unlike INTZ, this does not allow using depth buffer or using the surface for both
		sampling &amp; depth testing at the same time.</li>
</ul>
Also useful when using depth textures:
<ul>
	<li>Creating NULL color surface to keep D3D runtime happy and save on video memory.</li>
	<li>RESZ allows resolving multisampled depth surfaces into non-multisampled depth textures
		(result will be sample zero for each pixel).</li>
</ul>
Caveats:
<ul>
	<li>Using INTZ for both depth/stencil testing and sampling at the same time
		seems to have performance problems on AMD cards (checked Radeon HD 3xxx to 5xxx,
		Catalyst 9.10 to 10.5). A workaround is to render to INTZ depth/stencil first,
		then use RESZ to "blit" it into another surface. Then do sampling from one surface,
		and depth testing on another.</li>
</ul>
</p>

<a name="dbt"></a>
<h3>Depth Bounds Test</h3>
<p>
Direct equivalent of <a href="http://www.opengl.org/registry/specs/EXT/depth_bounds_test.txt">GL_EXT_depth_bounds_test</a>
OpenGL extension. See <sup><a href="#ref3">[3]</a></sup> for more information.
</p>


<a name="transpaa"></a>
<h3>Transparency Anti-Aliasing</h3>
<p>
NVIDIA exposes two controls: transparency multisampling (ATOC) and transparency supersampling (SSAA) <sup><a href="#ref4">[4]</a></sup>. The whitepaper does not explicitly say it, but in order for ATOC render state
(D3DRS_ADAPTIVETESS_Y set to ATOC) to actually work, D3DRS_ALPHATESTENABLE state must be also set to TRUE.
</p>
<p>
AMD says that all Radeons since 9500 support "alpha to coverage" <sup><a href="#ref1">[1]</a></sup>.
</p>
<p>
Intel supports ATOC (same as NVIDIA) with SandyBridge (GMA HD 2000/3000) GPUs.
</p>


<a name="r2vb"></a>
<h3>Render Into Vertex Buffer</h3>
<p>
Similar to "stream out" or "memexport" in other APIs/platforms. See <sup><a href="#ref2">[2]</a></sup> for
more information. Apparently some NVIDIA GPUs (or drivers?) support this as well.
</p>

<a name="inst"></a>
<h3>Geometry Instancing</h3>
<p>
Instancing is supported on all Shader Model 3.0 hardware by Direct3D 9.0c, so there's no extra hacks
necessary there. AMD has exposed a capability to enable instancing on their Shader Model 2.0 hardware
as well. Check for "INST" support, and do <tt>dev->SetRenderState (D3DRS_POINTSIZE, kFourccINST);</tt>
at startup to enable instancing.
</p>
<p>
I can't find any document on instancing from AMD now. Other references: <sup><a href="#ref6">[6]</a></sup> and <sup><a href="#ref7">[7]</a></sup>.
</p>


<a name="3dc"></a>
<h3>ATI1n &amp; ATI2n Compressed Texture Formats</h3>
<p>
Compressed texture formats. ATI1n is known as BC4 format in DirectX 10 land; ATI2n as BC5 or 3Dc.
Since they are just DX10 formats, support for this is quite widespread, with NVIDIA exposing it
a while ago and Intel exposing it recently (drivers 15.17 or higher).
</p>
<p>
Thing to keep in mind: when DX9 allocates the mip chain, they check if the format is a
known compressed format and allocate the appropriate space for the smallest mip levels. For
example, a 1x1 DXT1 compressed level actually takes up 8 bytes, as the block size is fixed
at 4x4 texels. This is true for all block compressed formats. Now when using the hacked
formats DX9 doesn't know it's a block compression format and will only allocate the number of
bytes the mip would have taken, if it weren't compressed. For example a 1x1 ATI1n format will
only have 1 byte allocated. What you need to do is to stop the mip chain before the size of
the either dimension shrinks below the block dimensions otherwise you risk having memory
corruption.
</p>
<p>
Another thing to keep in mind: on Vista+ (WDDM) driver model, textures in these formats will
still consume application address space. Most regular textures like DXT5 don't take up additional
address space in WDDM (<a href="http://support.microsoft.com/kb/940105/en-us">see here</a>). For
some reason ATI1n and ATI2n textures on D3D9 are deemed lockable.
</p>


<h3>References</h3>

<p>All this information gathered mostly from:
<ol>
<li><a name="ref1"></a><a href="http://amd-dev.wpengine.netdna-cdn.com/wordpress/media/2012/10/Advanced-DX9-Capabilities-for-ATI-Radeon-Cards_v2.pdf">Advanced DX9 Capabilities for ATI Radeon Cards (pdf)</a></li>
<li><a name="ref2"></a><a href="http://developer.amd.com/wordpress/media/2012/10/R2VB_programming.pdf">ATI R2VB Programming (pdf)</a></li>
<li><a name="ref3"></a><a href="http://developer.download.nvidia.com/GPU_Programming_Guide/GPU_Programming_Guide_G80.pdf">NVIDIA GPU Programming Guide (pdf)</a></li>
<li><a name="ref4"></a><a href="http://www.nvidia.com/object/transparency_aa.html">NVIDIA Transparency AA</a></li>
<li><a name="ref5"></a><a href="http://www.nvidia.com/object/coverage-sampled-aa.html">NVIDIA Coverage Sampled AA</a></li>
<li><a name="ref6"></a><a href="http://www.humus.name/index.php?page=3D&ID=52">Humus' Instancing Demo</a></li>
<li><a name="ref7"></a><a href="http://zeuxcg.org/2007/09/22/particle-rendering-revisited/">Arseny's article on particles</a></li>
</ol>
</p>


<a name="changelog"></a>
<h3>Changelog</h3>
<ul>
	<li>2016 01 06: Updated links to NV/AMD docs since they like to move pages around making old links invalid! Renamed ATI to AMD. Clarified ATOC gotcha.</li>
	<li>2013 06 11: One more note on ATI1n/ATI2n format virtual address space issue (thanks JSeb!).</li>
	<li>2013 04 09: Turns out since sometime 2011 Intel has DF24 and Fetch4 for SandyBridge and later.</li>
	<li>2011 01 09: Intel implemented ATOC for SandyBridge, and NULL for GMA HD and later.</li>
	<li>2010 08 25: Intel implemented DF16, INTZ, RESZ for G45+ GPUs!</li>
	<li>2010 08 25: Added note on INTZ performance issue with ATI cards.</li>
	<li>2010 08 19: Intel implemented ATI1n/ATI2n support for G45+ GPUs in the latest drivers!</li>
	<li>2010 07 08: Added note on ATI1n/ATI2n texture formats, with a caveat pointed out by Henning Semler (thanks!)</li>
	<li>2010 01 06: Hey, shadow map hacks are also supported on Intel 965!</li>
	<li>2009 12 09: Shadow map hacks are supported on Intel G45!</li>
	<li>2009 11 21: Added instancing on SM2.0 hardware.</li>
	<li>2009 11 20: Added Fetch-4, CSAA.</li>
	<li>2009 11 20: Initial version.</li>
</ul>
