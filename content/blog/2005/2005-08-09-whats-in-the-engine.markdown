---
tags:
- demos
comments: true
date: 2005-08-09T15:53:00Z
slug: whats-in-the-engine
status: publish
title: What's in the engine?
url: /blog/2005/08/09/whats-in-the-engine/
wordpress_id: "59"
---

As always, here's a thought that's more about demo-engines than about game-engines; and mostly dealing with graphics stuff.

Some folks add whatever gfx effects/techniques they use to "the engine". Quote from [Plastic](http://www.plastic-demo.org/):


> We've also added a couple of new features like <...> wireframe/particle shader.

Now, I don't have a problem with that, but what strikes me is that for whatever reason [our engine](http://dingus.berlios.de/) doesn't have any effect/technique in it. Zero shaders, no shadowmaps/PRT/refractions/whatever.

The obvious disadvantage of our approach is that for each demo I basically write the shaders and "effects" from scratch (ok, copying them is also fine). There's no central place where, for example, a Gaussian blur postprocessing filter or shadowmaps rendering code is stored.

On the other hand, that means (nearly) complete freedom for each demo. In each prod I can tweak whatever I want and implement completely different rendering techniques. For example, [in.out.side demo](http://nesnausk.org/inoutside) is quite different from [Visual Gaming viewer](http://dingus.berlios.de/index.php?n=Main.ProjNanobots) or [Xplodar FEM demo](/projXplodar.html), yet they all share the same underlying "engine" (read: bunch of code).

Still, what I'd like to do is have "somewhat stable" stuff gathered in one place. I don't tweak my basic lighting functions, standard postprocessing effects or shadowmap sampling patterns that often :)
