---
title: ImagineCup2005 Visual Gaming 3D Engine
comments: true
sharing: true
footer: true
menusection: proj
url: projHoshimi.html
---

<p>
One of the new categories in Microsoft's <a href='http://imagine.thespoke.net'>Imagine Cup 2005</a> is called "Visual Gaming", where participants
are given SDK and have to program AI for a team of nano-robots. This year, the whole "game" behind Visual Gaming is called
<a href="http://www.project-hoshimi.com/">Project Hoshimi</a>, and it features 3D preview part.
</p>
<p>
Now, this "3D previewer" is exactly what I <em>(and another member of <a href="http://www.nesnausk.org">Nesnausk!</a> team, Paulius Liekis)</em> have made.
Why the organizers <em>(Microsoft)</em> had chosen us to do it - I don't really know :)
</p>
<p>
Most of the 3D previewer was done in Aug-Oct 2004; but there will be some additional tweaks all the way to ImagineCup2005 finals.
</p>


<H3>Technical details</H3>
<p>
I did most of programming stuff, and a small amount of artwork. No fancy "effects" are done yet; as the previewer has to run on DX7 (and even some of DX6)
video cards. So, a short list:
<UL>
<li>Subdivision surface for the level mesh, followed by QEM style simplification.</li>
<li>Multiple mesh levels of detail (both level and entities).</li>
<li>All geometry is processed with vertex shaders 1.1; most things use "sort of" rim lighting. Particle effects are also static geometry, with
	all calculations in vertex shaders.</li>
<li>Usual stuff: some GUI, some old D3D devices detection, etc.</li>
</ul>
</P>


<H3>Download</H3>
<p>
The 3D previewer is part of ImagineCup2005 Visual Gaming SDK.<br>
Download it from <a href="http://www.project-hoshimi.com/"><strong>project-hoshimi.com</strong></a> (approx. 3.5MB).
</p>
<p>
Full source code is released <a href="http://dingus.berlios.de/index.php?n=Main.ProjNanobots">here</a>.
</p>

<H3>Screenshots</H3>
<P>
Thumbnails, click for a larger shot.
</P>
<a href="img/ProjHoshimi01.jpg"><img src="img/tn/ProjHoshimi01.jpg"></a>
<a href="img/ProjHoshimi02.jpg"><img src="img/tn/ProjHoshimi02.jpg"></a>
<a href="img/ProjHoshimi03.jpg"><img src="img/tn/ProjHoshimi03.jpg"></a><br>
<a href="img/ProjHoshimi04.jpg"><img src="img/tn/ProjHoshimi04.jpg"></a>
<a href="img/ProjHoshimi05.jpg"><img src="img/tn/ProjHoshimi05.jpg"></a>
<a href="img/ProjHoshimi06.jpg"><img src="img/tn/ProjHoshimi06.jpg"></a>
