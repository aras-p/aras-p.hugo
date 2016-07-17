---
layout: page
title: Water Boiler
comments: true
sharing: true
footer: true
menusection: proj
url: projFlip0206.html
---

<P>
It was around 23rd June 2002... I suddenly remembered -
<A href="http://www.flipcode.com">Flipcode's</A> June coding contest! It was
about one week left. "Liquid effects". Aiste suggested "A Semolina Simulator"
- that clearly required a fluid/semolina solver. In my pile of papers I quickly
found "Deep Water Animation and Rendering" - that was not very suitable. But
in the references was mentioned "Stable Fluids" by Jos Stam - that was it!
</P>
<P>
So, I quickly typed in the basic "framework", then the fluid solver (used
FFT for solving - that's absolutely incorrect because of pot boundaries, but
who cares?). As semolina turned out to be too hard, I decided to make
just the water boiler (with lots o' bubbles and rippling surface). So I modeled
the pot, drew the textures, made lots of tuning, added the bubbles... And here
it is: <em>(tadadam!) </em><strong>The Water Boiler</strong>.
</P>
<P>
Requirements for running: Windows OS; DirectX 8.1; (relatively) fast CPU
(700MHz or so); known to work on GPUs: TNT2 M64, TNT2 Vanta, GeForce2MX200,
GeForce2Ti (so something along lines of those).
</P>


<H3>Download</H3>
<P>
Binary release (executable, data files)
<A href="files/waterboiler.zip"><strong>here</strong></A> (401 KB). <em>(Excuse me for programmer's art)</em>
</P>
<P>
Source release (only sources, MSVC 6.0 project and workspace)
<A href="files/waterboiler-src.zip"><strong>here</strong></A> (527 KB). <em>Warning: by no means it should be
taken as an example of good design, coding style or efficiency. These things
can't be found here :)</em>
</P>

<H3>Things I found</H3>
<UL>
<LI>Jos Stam's paper rocks!
<LI>For the first time I have faced the "instability" of the calculations.
    That occured before I implemented Stam's solver - I quickly coded a naive
    solver. Fluid velocities sometimes went crazy (or even to infinity).
    While instable calculations are acceptable in many cases, they are not
    suitable in the others...
<LI><em>(again) </em> Direct3D 8 rocks! Especially when you're writing small
    demos/techdemos - just take "common" classes from D3D examples and type
    in your stuff. And with D3DX it's really "the best rendering engine
    ever" :)
<LI><em>Semolina</em> is a very funny word. I didn't know it before I looked up
    in a dictionary. Moreover, the thing semolina is made of is called
    <em>farina</em>! English language is so funny sometimes :)
</UL>


<H3>Things that are missing</H3>
<UL>
<LI>Correct heating. Now the heating only heats one circle of bottom water
    elements. It should heat whole pot bottom almost uniformly, and also heat
    pot sides.
<LI>Solver with correct boundaries. Currently the velocities that go out of
    the pot appear on it's other side (that's because of FFT). Conjugate
    Gradient or similar solver should be used, with correct (cylinder)
    boundaries. That would be also slightly incorrect because the water surface
    is (in reality) not flat, but that's fairly negligible.
<LI>The bubbles movement/spawning was implemented "out of the top of my head",
	it's not even close to the correct movement.
<LI>Water refraction. I planned to do this (sort of: render "from-above"
    view into texture; then draw water surface as non-transparent grid,
    using that texture). But there were no time left, and this already exists
    in DX examples, right?
<LI>Water caustics <em>(same as above, especially the "this already exists" part).</em>
</UL>


<H3>Boiler in action</H3>
<P>
These show the initial pot, nearly-boiling pot, and wireframe/velocities/temperatures
view. Everything is on Duron700 / nVidia TNT2 M64, so don't pay big attention
to the FPS number :) But the real fun of watching the bubbles can't be
expressed in the shots...
</P>
<IMG src="img/Waterboiler1.jpg">
<IMG src="img/Waterboiler2.jpg">
<IMG src="img/Waterboiler3.jpg">
