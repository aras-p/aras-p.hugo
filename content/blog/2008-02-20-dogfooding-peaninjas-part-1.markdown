---
tags:
- code
- games
- unity
comments: true
date: 2008-02-20T21:42:49Z
slug: dogfooding-peaninjas-part-1
status: publish
title: 'Dogfooding: PeaNinjas part 1'
url: /blog/2008/02/20/dogfooding-peaninjas-part-1/
wordpress_id: "158"
---

I decided to make a very small game with Unity. Coincidentally, Danc of [Lost Garden](http://www.lostgarden.com/) fame just announced a small game design challenge called "[Play With Your Peas](http://lostgarden.com/2008/02/play-with-your-peas-game-prototyping.html)". It comes with a set of cute graphics and a ready-to-be-implemented game design. What more could I want?

So it's a <del>small</del> very small 2D game without _any_ next-gen bells and whistles. It can probably be done casually on the side, by allocating an hour here and there. We'll see how it goes. Hey, I never _actually_ done any game in Unity, I only make or break some underlying parts...

[{{<imgleft src="http://aras-p.info/blog/wp-content/uploads/2008/02/peas080211a.thumbnail.png" title="Look! No game there!">}}](http://aras-p.info/blog/wp-content/uploads/2008/02/peas080211a.png)Of course, first I start with no game, just imported graphics. Hey look, I can do sprites!

[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2008/02/peas080216a.thumbnail.png" title="'Level editing'">}}](http://aras-p.info/blog/wp-content/uploads/2008/02/peas080216a.png)Then cook up some base things: define the game grid, throw in some basic user interface on the right hand side, and make it actually do something. This wasn't so hard; that already gets me an almost working level building functionality. It does not have fancy block building delay or block deletion yet; that will come later.

Next come basic physics. Danc's design calls for simple arcade-like physics (things moving at constant speeds, bouncing off at equal angles, and so on), but in Unity I have a fully fledged [physics engine](http://unity3d.com/unity/features/physics) just waiting to be used. Let's use that.

The design has sloped ramp pieces, which are hard to approximate using any primitive colliders, so instead I'll use convex mesh colliders for them. Now, on this machine I only have Blender, which I totally don't know how to use; and I was too lazy to go to PC and use 3ds Max there. What a coder does? Of course, just type in the mesh file in ASCII FBX format. Excerpt:



    ; scaled 2x in Z, by 0.85 in Y
    Vertices: -0.5,-0.425,-1.0, 0.5,-0.425,-1.0, -0.5,-0.425,1.0, 0.5,-0.425,1.0,  -0.5,0.425,-1.0, -0.5,0.425,1.0        
    PolygonVertexIndex: 0,1,-3,2,1,-4,1,0,-5,2,3,-6,0,2,-5,2,5,-5,3,1,-5,5,3,-5



It's a left ramp mesh! So much for fancy [asset auto-importing](http://unity3d.com/unity/features/asset-importing) functionality, when you don't know how to use those 3D apps :)

[{{<imgleft src="http://aras-p.info/blog/wp-content/uploads/2008/02/peas080216b.thumbnail.png" title="Physics!">}}](http://aras-p.info/blog/wp-content/uploads/2008/02/peas080216b.png)
[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2008/02/peas080216c.thumbnail.png" title="'Pea stack'">}}](http://aras-p.info/blog/wp-content/uploads/2008/02/peas080216c.png)After a while I've got peas being controlled by physics, colliding with level and so on. Physics is very bad for productivity, as I ended up just playing around with pea-stacks!

So far there's no _game_ yet... Next up: implement some AI for the peas, so they can wander around, climb the walls, fall down and bounce around. I guess that will be more work and less playing around... We'll see.
