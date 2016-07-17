---
layout: page
title: Cloth simulation
comments: true
sharing: true
footer: true
menusection: proj
url: cloth.html
---

<p>This was my BSc thesis in the university. <A href="clothLog.html">Development log (Lithuanian)</A>.</p>

<h3>Files</h3>

<p><strong>TBD</strong> - Demo#4</P>
<p>
Soon - I'll just port some 1.4 pixel shaders to 1.1 (in order to run on GF3/GF4Ti), and upload it.
</p>

<p><strong>2003 01 27</strong> - Demo#3</P>
<p>
Various explicit integrators and constraints, improved normal calculation,
a bit optimized, 40x40 particles cloth patch.
Zip file <A href="files/cloth/clothsim-0126.zip"><strong>here</strong></A> (156kb), requires
DirectX8.1 and a basic 3D accelerator.
</p>


<p><strong>2003 01 10</strong> - Demo#2</P>
<p>
Still no implicit integrator... So here's a demo with various explicit integrators,
80x80 particles cloth patch and a gear-shaped table.
Zip file <A href="files/cloth/clothsim-0110.zip"><strong>here</strong></A> (149kb), requires
DirectX8.1 and a basic 3D accelerator.
</p>



<h3>Images</h3>

<p clear="all"><strong>2003 06 14</strong></P>
<A href="img/cloth/030614-1.jpg"><img src="img/cloth/tn/030614-1.jpg"></A>
<A href="img/cloth/030614-2.jpg"><img src="img/cloth/tn/030614-2.jpg"></A>
<A href="img/cloth/030614-3.jpg"><img src="img/cloth/tn/030614-3.jpg"></A>
<A href="img/cloth/030614-4.jpg"><img src="img/cloth/tn/030614-4.jpg"></A>
<A href="img/cloth/030614-5.jpg"><img src="img/cloth/tn/030614-5.jpg"></A>
<br>
Some shading modes, with 2 directional ligths. Left to right:
<ul>
<li>Per-pixel anisotropic with anisotropy direction map,</li>
<li>Per-pixel diffuse+specular with normal map and gloss map,</li>
<li>- the same,</li>
<li>Homomorphic factorised BRDF (McCool et al.), satin BRDF,</li>
<li>- the same, velvel (red) and satin (yellow) BRDF,</li>
</ul>
All rendered on Radeon 9000Pro with 2xFSAA and 8xAnisotropic; using DX9 (some shaders HLSL, some asm).
<br>


<p clear="all"><strong>2003 01 27</strong></P>
<A href="img/cloth/030124-1.jpg"><img src="img/cloth/tn/030124-1.jpg"></A>
<A href="img/cloth/030124-2.jpg"><img src="img/cloth/tn/030124-2.jpg"></A>
<A href="img/cloth/030124-3.jpg"><img src="img/cloth/tn/030124-3.jpg"></A>
<A href="img/cloth/030124-4.png"><img src="img/cloth/tn/030124-4.jpg"></A>
<A href="img/cloth/030124-5.jpg"><img src="img/cloth/tn/030124-5.jpg"></A>
<A href="img/cloth/030124-6.png"><img src="img/cloth/tn/030124-6.jpg"></A>
<br>
Improved normal calculation, so vertex lighting doesn't look that bad, added
more different constraints and made better texture/material.
<br>


<p clear="all"><strong>2003 01 04</strong></P>
<p>
<div style="float: right">
<A href="img/cloth/030104-1.jpg"><img src="img/cloth/tn/030104-1.jpg"></A>
<A href="img/cloth/030104-2.png"><img src="img/cloth/tn/030104-2.jpg"></A>
</div>
Quantity has a quality on it's own - a patch of 80x80 particles and gear-shaped
table constraints.
</p>


<p clear="all"><strong>2002 12 30</strong></P>
<p>
<div style="float: right">
<A href="img/cloth/021230-1.jpg"><img src="img/cloth/tn/021230-1.jpg"></A>
<A href="img/cloth/021230-2.jpg"><img src="img/cloth/tn/021230-2.jpg"></A>
<A href="img/cloth/021230-3.png"><img src="img/cloth/tn/021230-3.jpg"></A>
</div>
That's a patch of 24x24 cloth with (simply hacked in for now) round-table and
square-table constraints. Ah, really bad vertex tesselation shows up in the round-table
case and really poor per-vertex lighting :)
</p>

<p clear="all"><strong>2002 12 28</strong></P>
<p>
<div style="float: right">
<A href="img/cloth/021228-1.jpg"><img src="img/cloth/tn/021228-1.jpg"></A>
<A href="img/cloth/021228-2.jpg"><img src="img/cloth/tn/021228-2.jpg"></A>
</div>
Explicit Euler for now, no collisions of any kind. This is a patch of 20x20
particles, with three corners constrained. On Duron700/SDR this would run
at nearly 400 FPS (now it's strongly fillrate limited on my TNT2).
</p>
