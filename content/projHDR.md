---
title: HDR with MSAA demo (2005)
comments: true
sharing: true
footer: true
menusection: proj
url: projHDR.html
---

<p>
An attempt to use old 32 bit RGBA rendertarget for HDR, using RGBE8 encoding.
Multisampling should Just Work on all DX9 hardware. See <a href="#desc">description</a> below.
</p>

<p>Requirements: DX9 video card: pixel shaders 2.0 and floating point textures required. Good performance is also a plus!</p>
<p>Usage: Run. Press arrow keys and A/Z to control the camera; the rest is via UI sliders. Press F2 for device selection dialog,
try out anti aliasing.</p>
<p><em>
Note: It seems that anti aliasing does not work on some ATI hardware at the moment. I'm still figuring out whether this is my bug or
HW feature (e.g. <a href="http://www.beyond3d.com/forum/showthread.php?p=611933#post611933">MSAA does not resolve alpha channel</a>,
or something like that).</em>
</p>

<h3>Features</h3>
<ul>
<li>HDR rendering: mostly copied from DX SDK sample HDRLighting (tone mapping,
  luminance adaptation, blue shift, bloom).</li>
<li>Diffuse lighting from environment (9 coeff. SH) and direct sunlight with
  shadow map; using per-vertex ambient occlusion term. Shadow map uses DST
  if available, else fallbacks to R32F.</li>
<li>Model is still a WIP version of <a href="http://www.google.com/search?q=St+Anne's+Church+Vilnius">St. Anne's Church</a> in Vilnius, Lithuania.
  Modeling is done by <em>Siger</em>, see thread at <a href="http://cgtalk.lt/viewtopic.php?t=2505">cgtalk.lt</a> <em>(Lithuanian)</em>. The model is not authored with realtime
  rendering in mind, that's why it has approx. 500 thousand vertices at the moment.</li>
<li>The light probe is courtesy of <a href="http://www.debevec.org">Paul Debevec</a>. You can swap in your own probe in <code>data/tex/HdrEnv.dds</code>
  in native DDS cubemap or vertical cross HDR format (just rename to .dds).</li>
</ul>


<H3>Download</H3>
<p>
7-zip file <a href="files/TestHDR.7z"><strong>here</strong></a> (4.4MB, <em>get 7-zip <a href="http://www.7-zip.org">here</a></em>).
</p>
<p>
Source code: get it from <a href="http://dingus.berlios.de/index.php?n=Main.ProjHDR">dingus.berlios.de</a> or just browse the main source file
<a href="http://svn.berlios.de/viewcvs/dingus/trunk/testHDR/src/demo/Demo.cpp?view=markup">online</a> (the rest is effect files, they are
included in the binary release).
</p>

<H3>Screenshots</H3>
<P>
Thumbnails, click for a larger shot.
</P>
<a href="img/TestHDR01.jpg"><img src="img/tn/TestHDR01.jpg"></a>
<a href="img/TestHDR02.jpg"><img src="img/tn/TestHDR02.jpg"></a>
<a href="img/TestHDR03.jpg"><img src="img/tn/TestHDR03.jpg"></a>
<a href="img/TestHDR04.jpg"><img src="img/tn/TestHDR04.jpg"></a>
<a href="img/TestHDR05.jpg"><img src="img/tn/TestHDR05.jpg"></a>

<a name="desc"></a>
<h3>Description</h3>
<p>
The big problem with HDR renderers is that multisampling does not work with floating point rendertargets on most DX9 hardware right now <em>(2005 Nov)</em>. Doing
manual supersampling is often not an option either because of performance loss. This demo is an attempt to do HDR rendering without floating point rendertargets -
good old A8R8G8B8 format is used, with RGBE8 encoding when rendering and decoding in the tone mapping process.
</p>
<p>
Of course, multisampling RGBE does not produce "correct" results when all components are simply interpolated/averaged (as is the case with MSAA). However,
multisampling is a hack (in a sense "not fully correct") anyway - it just increases sampling frequency in an attempt to better represent the underlying signal. In RGBE case,
we're increasing sampling frequency, but the samples will not be simply averaged (RGB and E will be averaged separately). The result should be fairly good,
as averaging RGB and E separately will still produce final value that is "between" the sample values, which is good.
</p>
<p>
By the way, ATI has a whitepaper <em>HDR Texturing</em> on a similar subject in <a href="http://www.ati.com/developer/radeonSDK.html">Radeon SDK</a>
<em>(October 05)</em> - though they focus on interpolation, not MSAA.
</p>
<p>
Note: for RGBE and MSAA, you really do not want to use full -128..127 range for the exponent. Because MSAA will interpolate the exponent, you will want it to
have intermediate (non-integer) values as well. What I did was: use -64..63 range, thus leaving one bit for exponent interpolation - this is often enough.
</p>
