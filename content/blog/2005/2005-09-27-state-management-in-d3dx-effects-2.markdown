---
tags:
- d3d
- papers
comments: true
date: 2005-09-27T18:46:00Z
slug: state-management-in-d3dx-effects-2
status: publish
title: 'State management in D3DX Effects #2'
url: /blog/2005/09/27/state-management-in-d3dx-effects-2/
wordpress_id: "70"
---

I've written down the [basic idea here](/blog/2005/09/24/state-management-in-d3dx-effects). Done some tests and it really seems to work!

That required tiny 700 lines of hacky C++ code in the engine; but in exchange there's no longer a need to write state restoring passes by hand. Maybe such effect usage scheme would even be useable in RealWorld!

Too bad I didn't think it up a couple of months ago. My ShaderX4 article about this subject would have been much better...

Ok, still got to test this stuff on real world data (i.e. trying it on our demos)
