---
title: d01 the silent demo
comments: true
sharing: true
footer: true
menusection: proj
url: projd01.html
---

<H3>Story</H3>
<P>
Somehow the music club "Combo" (the one that's located in the centre of Kaunas)
asked us <em>(nesnausk!)</em> to gather some visualizations (demos, intros, etc.) that
could go along the music. Someone said "let's make a demo for that!" - partially as
a joke, because it was only 5 or 6 days left. However, Cathy quickly made
some "concept art", and one guy named NeARAZ said "I'll do a demo from this".
</P>
<P>
Basically that's all. In 3 days, I've made this demo. See it yourself.
</P>
<P>
There's some problem with it on older nVidia drivers (3x.xx) - the
screen blends to white or black. Hopefully I'll fix that soon. For now, just
use the latest drivers :)
</p>


<H3>Technical stuff</H3>
<P>
In case you're interested, I give the source of this, see below.
<UL>
<LI><strong>Code</strong>. Some parts I took from
<A href="http://jammy.sourceforge.net">LTGameJam'02</A>. The rest is pretty
much hardcoded. This demo could be very easily turned into an intro, but
I've gone the easier way and used the fancy stuff from D3DX (effect files,
texture/mesh loading, etc.), hence the executable is fairly large.</LI>
<LI><strong>Rendering</strong>. The standard cheap glow effect on everything: render to
screen and to some (4) different resolution textures (so in total everything
is rendered 5 times). Then additive-blend these textures over whole screen. Because
the pixels in each successive texture get bigger and bigger the bright spots look
like glowing.</LI>
</UL>
</P>


<H3>Download</H3>
<P>
Binary release (executable, data files)
<A href="files/d01.zip"><strong>here</strong></A> (243 KB). <em>Note: no music!
<strong>Use the latest drivers for nVidia cards!</strong></em>
</P>
<P>
Source release (sources, data files, MSVC6 project and workspace)
<A href="files/d01-src.zip"><strong>here</strong></A> (103 KB).
</P>


<H3>d01 in pixels</H3>
<P>
Thumbnails, click for a larger shot.
</P>
<A href="img/d011.jpg"><IMG src="img/tn/d011.jpg"></A>
<A href="img/d012.jpg"><IMG src="img/tn/d012.jpg"></A>
<A href="img/d013.jpg"><IMG src="img/tn/d013.jpg"></A>
<A href="img/d014.jpg"><IMG src="img/tn/d014.jpg"></A>
<A href="img/d015.jpg"><IMG src="img/tn/d015.jpg"></A>
<A href="img/d016.jpg"><IMG src="img/tn/d016.jpg"></A>
