---
tags:
- uncategorized
comments: true
date: 2005-06-02T10:29:00Z
slug: 3d-engine-design
status: publish
title: 3D engine design
url: /blog/2005/06/02/3d-engine-design/
wordpress_id: "42"
---

I'm reading API docs of one "really advanced" modern 3D engine (found them publicly available on their site, don't know if that's an error or not :)) and... it's a strange feeling. Basically, everything's hardcoded!

By 'everything' I mean that, for example, possible shadow algorithms are hardcoded in the very base classes of the engine. Same for physics, lights, scene management, culling etc. Similar like you'd have a class `VeryBaseEngineObject` and that would have information "what shadows are on me?", "am I physics body", "cull me against the camera" and "render me for the water reflection".

Huh? Is that how all engines are designed? You have functionality like "shadow map", "a physics crate", "a fancy reflective water" and "a fullscreen glow with light streaks Masaki Kawase style" in the _core of the engine_? I'd understand if that would be as helpers or some "premade" stuff _on top_ of the core engine... but having that _in the core_? Why?

Is that what constitutes a "flexible" engine?

