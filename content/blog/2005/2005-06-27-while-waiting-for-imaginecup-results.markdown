---
tags:
- games
- random
comments: true
date: 2005-06-27T16:09:00Z
slug: while-waiting-for-imaginecup-results
status: publish
title: While waiting for ImagineCup results...
url: /blog/2005/06/27/while-waiting-for-imaginecup-results/
wordpress_id: "48"
---

Aargh, the ImagineCup crew postponed the results announcement again (that is, 3rd time already)! First it had to be on June 13th, now it's scheduled for _Real Soon Now, Really_!

Meanwhile, I'm doing a game that _surely_ will revolutionize the whole gaming industry, blah blah etc. That is, a letter learning game for my daughter; in Lithuanian. There are lots of these ([example](http://www.fisher-price.com/us/fun/games/abc/default.asp)), but the problem is that they're in all languages except the one you need.

Now, the most obvious implementation would be in Flash. Alas, I don't have Flash authoring tools nor do I want to learn them and it's ActionScript. I thought about going Lua+SDL all the way, without C++ at all. Guess what - SDL doesn't have any high level audio playing stuff; I'm too lazy to bind it's sdl_mixer to lua; there's no nice IDE and there were lots of other obstacles. So I just went and am doing this in C++, abusing our [demo engine](http://dingus.berlios.de/). Oh well, I'm just lazy.

While implementing it I found out that I have no good tool to pack lots of arbitrary images into nice power-of-2 texture atlases. nVidia's [Texture Atlas Tools](http://developer.nvidia.com/object/texture_atlas_tools.html) do almost that, but they rescale textures to be power-of-2 (which is usually ok, but not for my case). So I just had to [fork the AtlasCreationTool](http://svn.berlios.de/wsvn/dingus/trunk/dingus/tools/AtlasCreationToolFork/?rev=0&sc=0) _(just SVN link for now)_ to do what I want. While at it, I removed it's dependency on D3D sample framework _(hey, removing code is always good!)_, forced it to support non-pow-2 textures and to pack them better. It's probably not that good for the usual case (where you want to place textures at block boundaries in the atlas to minimize mipmap artifacts), but packs arbitrary sized images pretty nicely. Yay!

