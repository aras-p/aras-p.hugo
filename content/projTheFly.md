---
layout: page
title: The Fly
comments: true
sharing: true
footer: true
menusection: proj
url: projTheFly.html
---

<p>
The Fly is a <em>demo</em> (non-interactive program that shows realtime graphics and plays some music). I and my fellow Paulius Liekis
made it for Microsoft's <a href='http://www.imaginecup.com'>Imagine Cup 2004</a> and it took <strong>second place in the worldwide finals</strong>!
The official PR is <a href='http://www.microsoft.com/presspass/press/2004/jul04/07-06Champions04PR.asp'>here</a>.
Of course, we're really happy about that <em>(but watch out for Imagine Cup 2005 :))</em>.
</p>
<p>
 The demo was made during Mar-Apr 2004, reusing lots of our previously written code.
 </p>

<H3>Technical details</H3>
<p>
I did most of programming stuff, and a small amount of artwork (setting up lighting for lightmaps, some bumpmaps). The demo features:
<UL>
<li><strong>Graphics effects</strong>, with several fallback paths (for pixel shader 2.0, 1.1 and fixed function hardware):
	<ul>
	<li>tangent space normal mapping, using over-the-edge diffuse lighting,</li>
	<li>a mix of above and precomputed lightmaps, using ad-hoc method (some discussion <a href='http://sourceforge.net/mailarchive/message.php?msg_id=8542701'>here</a>),</li>
	<li>depth-of-field using two blurred versions of the framebuffer,
		<div class="small">(M.Kawase, "Frame Buffer Postprocessing Effects in Double-Steal", GDC2003).</div></li>
	<li>fur rendering using extruded shells, (L.T,V.T) texture lookup for anisotropic lighting and some faking of self-shadowing,</li>
	<li>simple projected shadows,</li>
	<li>multi-layered terrain, with three detailed textures, "texture mix map" and precomputed lightmap,</li>
	<li>water rendering with several scrolling bump-maps for water waves,</li>
	<li>translucent and iridescent wings
		<div class="small">(N. Tatarchuk, C. Brennan in "ShaderX^2" and also a RenderMonkey example).</div></li>
	</ul>
</li>
<li><strong>In code</strong> I made heavy use of DirectX9 and some of it's cool features (notably D3DX Effects Framework).</li>
</ul>
</P>


<H3>Download</H3>
<p>
Zip file <a href="files/Nesnausk!-TheFly.zip"><strong>here</strong></a> (18.6MB). Requires DirectX9.0b runtime.<br>
<em>Looks best on a pixel shader 2.0 capable video card; looks worse on a pixel shader 1.1 card and looks like crap on no-pixel-shader card :)</em>
</P>

<H3>The Fly in the fly</H3>
<P>
Thumbnails, click for a larger shot.
</P>
<a href="img/thefly_00.jpg"><img src="img/tn/thefly_00.jpg"></a>
<a href="img/thefly_01.jpg"><img src="img/tn/thefly_01.jpg"></a>
<a href="img/thefly_02.jpg"><img src="img/tn/thefly_02.jpg"></a>
<a href="img/thefly_03.jpg"><img src="img/tn/thefly_03.jpg"></a>
<a href="img/thefly_04.jpg"><img src="img/tn/thefly_04.jpg"></a>
<a href="img/thefly_05.jpg"><img src="img/tn/thefly_05.jpg"></a>
<a href="img/thefly_06.jpg"><img src="img/tn/thefly_06.jpg"></a>
<a href="img/thefly_07.jpg"><img src="img/tn/thefly_07.jpg"></a>
<a href="img/thefly_08.jpg"><img src="img/tn/thefly_08.jpg"></a>
<a href="img/thefly_09.jpg"><img src="img/tn/thefly_09.jpg"></a>

<H3>More info</H3>
<p>
Comments at <a href="http://www.pouet.net/prod.php?which=12271">pouet.net</a><br>
Review at <a href="http://nesnausk.org/demoscene/Apzvalgos.php?id=70">demo.scene.lt</a> (in Lithuanian)<br>
Project page at <a href="http://www.nesnausk.org/project.php?project=12">nesnausk.org</a>
</p>
