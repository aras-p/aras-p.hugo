---
layout: page
title: bzhykt the intro
comments: true
sharing: true
footer: true
section: proj
url: projBzhykt.html
---

<H3>Story</H3>
<P>
I remember ReJ calling me and saying "do you know what's going to happen after
2 weeks?". "No", I said. "Mekka&amp;Symposium'02! And we're going to be there!".
</P>
<P>
So we (I, ReJ, simple, OneHalf) decided to write an intro for it. It was 10 or
11 days left. We've never written an intro before. Had no artists, no musicians,
no idea, no tools, no code. Cool!
</P>
<P>
Everything went well, except that we had no music. We did some weird tracks in
the tracker, but, alas, we're not musicians :( And, as sound playing stuff
was delayed for some reason, we didn't even manage to play our "sound"
normally... (in the end we found that we need to delta-encode sound samples, but
it was too late :)).
</P>
<P>
We ended up with a rather cool-looking-toonish-style renderer, the terrain,
some weird-looking L-system trees and some spiders (boids/flocks stuff). Not much
and no particular idea...
</P>
<P>
It was shown at <A href="http://ms.demo.org/">Mekka&amp;Symposium'2002</A> in
Germany. Ended up somewhere in the middle :(
</P>


<H3>Technical stuff</H3>
<P>
<UL>
<LI><strong>Coding</strong>. We did with straight C++ (not C), with only several lines of
asm code (_ftol, fsin and the like). In the end, even some templates and
virtual functions were used, though the intro only reached 35kb :)</LI>
<LI><strong>Renderer</strong>. Basically: render into small (64x64) texture, read it back
(yes, it's not HW-friendly), and then draw billboards on screen, depending on
the colors of the texture. This way we get "painterly" look. Additionally,
draw several passes of hatching-style stuff (this part requires dot3 on the
card) to get the edges and the shades.</LI>
<LI><strong>Terrain</strong>. The terrain is of quite large resolution (320x320 - that's
~200k triangles), and we needed it to be drawn 3-4 times. So we partitioned
it into fixed "terrain blocks" and used simple frustum culling on them. Terrain
is generated from several simple Perlin noises.</LI>
<LI><strong>Spiders</strong>. OneHalf, the fan of boids, did the spiders during one
sleepless night. The spiders run around using some flocking behaviour, correctly
move their legs, damage each other, etc.</LI>
</LI>
</UL>
</P>


<H3>Get it!</H3>
<P>
You can get it through
<A href="http://www.scene.org/file.php?file=/parties/2002/mekkasymposium02/in64/pekla_bzhykt.zip&dummy=1">
<strong>scene.org</strong></A>.
</P>
<P>
Better turn off your speakers, because the sound is total
crap. Have a decent videocard (GeForce2/Radeon or better) and DirectX8.1.
Some folks reported they can't run it, or it crashes, or it restarts the computer, or it displays garbage - so take care...
</P>

<H3>The look of bzhykt</H3>
<P>
Thumbnails, click for a larger shot. The last one isn't from actual intro,
it's "bzhykt's terrain with flat shading" :)
</P>
<A href="img/bzhykt1.jpg"><IMG src="img/tn/bzhykt1.png"></A>
<A href="img/bzhykt2.jpg"><IMG src="img/tn/bzhykt2.png"></A>
<A href="img/bzhykt3.jpg"><IMG src="img/tn/bzhykt3.png"></A>
<A href="img/bzhykt4.jpg"><IMG src="img/tn/bzhykt4.png"></A>
<A href="img/bzhykt5.jpg"><IMG src="img/tn/bzhykt5.png"></A>
<A href="img/bzhykt6.jpg"><IMG src="img/tn/bzhykt6.png"></A>
