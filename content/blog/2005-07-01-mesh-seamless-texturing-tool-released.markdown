---
categories:
- code
- demos
comments: true
date: 2005-07-01T20:11:00Z
slug: mesh-seamless-texturing-tool-released
status: publish
title: Mesh seamless texturing tool released
url: /blog/2005/07/01/mesh-seamless-texturing-tool-released/
wordpress_id: "51"
---

Finally I've put the [MeshTexer tool online](http://dingus.berlios.de/index.php?n=Main.MeshTexer), with some documentation, the tool itself and some sample data. Basically, it takes a mesh with unique UV mapping, a normalmap and a tileable texture, and "wraps" the texture (nearly) seamlessly onto the given model. Well, the truth is that it just projects the texture onto the model from several sides, weighting by normal.

However, it worked like a charm during [in.out.side](http://www.nesnausk.org/inoutside) demo production. Paulius has written whole texturing workflow [here](http://www.nesnausk.org/inoutside/Technology.php#tech_texturing) - the tool was used to generate base material textures, material blend maps, gloss maps etc. Later these were hand-painted in "strategic" places and combined into final textures.

Enjoy!

