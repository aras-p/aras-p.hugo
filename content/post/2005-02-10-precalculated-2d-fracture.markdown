---
categories:
- code
comments: true
date: 2005-02-10T13:21:00Z
slug: precalculated-2d-fracture
status: publish
title: Precalculated 2D fracture
url: /blog/2005/02/10/precalculated-2d-fracture/
wordpress_id: "5"
---

I'm working on [ImagineCup2005](http://imagine.thespoke.net/invitationals/rendering.aspx) realtime rendering demo now, and one thing we are planning to do is fracturing/exploding walls of some room (in realtime of course). I've been thinking how to implement all this, and together with [Paulius Liekis](http://www.nesnausk.org/members.php#2) we came up with half-precomputed, half-cheated solution.



Our 'walls' are perfectly flat, so all fracture process is 2D, just the pieces that fly/fall out turn into 3D simulation. In the demo, some things will hit the walls, and the fracture must start there.



The first cheat we thought is to have some precomputed 'fracture patterns' (bunch of connected lines in 2D). Choose one pattern, 'put' that onto wall and there you go. Now, the problem is that the pattern has to be 'clipped' to the existing patterns, the falling out pieces and the remaining wall has to be triangulated, etc. I think it's not 'slow' process (i.e. suitable for realtime), but pretty tedious to implement.



The next idea was: why not precompute the fracture pattern for the whole wall, and make it pretty detailed? When something hits the wall, you take out some elements from it and let them fly/fall. Now, the fracture pattern is always fixed for the whole wall, so this isn't entirely 'correct' fracture, but I think it's ok for our needs. I coded up some lame 'fracture pattern generator' (tree-like, nodes either branch or not, branches are at some nearly random angles, and terminate when they hit existing branch), and the patterns do look pretty cool.



The only problem with this is when I tried calculating how many fractured pieces our walls will contain. I get half a million or so for the whole room; that's certainly a bit too much.



One idea to cope with this is: have a (sort of) quadtree for the wall, and each cell 'combines' the pieces it contains entirely into one 'super piece' _(what a term!)_. Some of the internal nodes vanish, hence the super-piece contains less triangles, and it gets better when we walk up the quadtree. Now, when a wall is hit somewhere, only a small portion of it 'fractures out', so most of the wall can still be displayed as super-pieces, and it gets detailed only around the fractured area.



So, in the end there's almost no computations performed for the fracture. The fracture pattern and the super-piece hierarchy is precomputed once, and at runtime we just use it. Of course, we still need to simulate the physics of flying/falling pieces, but that's another story.



In one moment, we'll have most of the walls 'exploded' at once, I think for that case we'll just use larger fractured elements, and everything will be ok :)


