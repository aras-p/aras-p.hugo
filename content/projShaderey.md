---
layout: page
title: Shaderey
comments: true
sharing: true
footer: true
section: proj
url: projShaderey.html
---

<p>
An entry for <a href="http://www.beyond3d.com/articles/shadercomp/results/">ATI/Beyond3D DX9 shader competition</a>.
According to the results, <em>Shaderey</em> is the winner! :)
</p>
<p>
<em>2004 Jun 26</em>: updated the demo itself and this page due to upcoming article in <a href='http://www.shaderx3.com'>ShaderX^3</a>. Shaderey
updates are: GUI instead of command line, made it work on GeForceFX, some minor cleanup/optimizations.
</p>

<H3>The demo</H3>
<p>
Displays outdoors scene rendered in some-NPR (non photorealistic rendering) way. Plays camera animation,
allows manual camera controls and fast-forward benchmarking mode. No music :(
</p>

<H3>Requirements</H3>
<p>
<ul>
<li>DirectX 9.0a (or later) runtime.</li>
<li>Graphics card that supports Pixel Shaders 2.0 and Vertex Shaders 1.1.<br>
	Tested and seems to work on ATI Radeon 9500 and up; nVidia GeForceFX and up.</li>
<li>64MB on graphics card is enough (for normal use). A card that supports DXT1 compressed textures is preferable.</li>
<li>The demo can make use of Multiple Render Targets (2 of them, A8R8G8B8 format) if they are supported. They are disabled by default, see note below.</li>
</ul>
Note that Anisotropic filtering and/or FSAA in graphics card settings don't have much influence here (so better turn them off or to "application preference").
</p>

<H3>Usage</H3>
<p>
Run "shaderey.exe" and set video mode and other parameters in the dialog.
</p>
<p>
For benchmarking mode, set 'Benchmark mode' checkbox in the starting dialog. After completion of whole animation loop, the
"score" is number of frames that were rendered. It doesn't lock backbuffer to ensure full flush, though :(
Note that at startup, some (20) frames are rendered for warming up, and don't affect the score.
</p>
<p>
To enable use of Multiple Render Targets (if they are supported), set 'Use multiple render targets' checkbox in the starting dialog.
By default MRTs are disabled - my observations suggest that they have rather big performance hit in fill-limited cases.
</p>
<p>
When not in benchmarking mode, the keys are:
<ul>
<li>Q - turn camera animation on/off
<li>W - wireframe on/off
<li>Arrows/A/Z/S/X - control camera when not animating
<li>PgUp/PgDown - control sun when not animating
<li>Alt-F4 - exit
</ul>

<H3>Download it!</H3>
<P>
Executable, data files, full source code: <A href="files/shaderey.zip"><strong>shaderey.zip</strong></A> (3.12MB).<br>
Full usage instructions, explanation of techniques, effects, shaders and sources are included.
</P>


<H3>See it</H3>
<P>
Thumbnails, click for a larger shot.
</P>
<A href="img/shaderey01.jpg"><IMG src="img/tn/shaderey01.jpg"></A>
<A href="img/shaderey02.jpg"><IMG src="img/tn/shaderey02.jpg"></A>
<A href="img/shaderey03.jpg"><IMG src="img/tn/shaderey03.jpg"></A>
<A href="img/shaderey04.jpg"><IMG src="img/tn/shaderey04.jpg"></A>
<A href="img/shaderey05.jpg"><IMG src="img/tn/shaderey05.jpg"></A>
<A href="img/shaderey03w.jpg"><IMG src="img/tn/shaderey03w.jpg"></A>
<A href="img/shaderey04w.jpg"><IMG src="img/tn/shaderey04w.jpg"></A>
