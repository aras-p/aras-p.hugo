---
categories:
- d3d
- opengl
comments: true
date: 2007-09-23T01:50:08Z
slug: is-opengl-really-faster-than-d3d9
status: publish
title: Is OpenGL really faster than D3D9?
url: /blog/2007/09/23/is-opengl-really-faster-than-d3d9/
wordpress_id: "136"
---

The common knowledge is that drawing stuff in OpenGL is much more faster than in D3D9. I wonder - is this actually true, or just an urban legend? I could very well imagine that setting everything up to draw a single model and then issuing 1000 draw calls for it is faster in OpenGL... but come on, that's not a very life-like scenario!

At [work](http://unity3d.com) we now have a D3D9 and an OpenGL renderers on Windows. The original codebase was very much designed for OpenGL, so I had to jump through a lot of hoops to get it fully working on D3D... small differences that add up, like: there's no object space texgen on D3D, shaders don't track built-in state (world, modelview matrices, light positions, ...), textures in GL vs. textures + sampler state in D3D, and so on. Anyway, the codebase was definitely not designed to exploit D3D strengths and OpenGL weaknesses, more likely the other way around.

But wait! I look at our benchmark tests, and D3D9 is consistently faster than OpenGL. Some examples:



	
  * Real world scene with lots of shadow casting lights (different objects, different shaders, different lights, different shadow types in one scene):
	
		
    * Core Duo with Radeon X1600: 23 FPS D3D9, 13 FPS GL.

		
    * P4 with GeForce 6800GT: 16 FPS D3D9, 9 FPS GL.

		
    * Core2 Duo with Radeon HD 2600: 41 FPS D3D9, 35 FPS GL.

	
	

	
  * High object count test (1000 objects, multiple lights, 5 passes per object total):
	
		
    * Core Duo with Radeon X1600: 18.3 FPS D3D9, 12.5 FPS GL.

		
    * P4 with GeForce 6800GT: 13.2 FPS D3D9, 9.4 FPS GL.

		
    * Core2 Duo with Radeon HD 2600: 34.8 FPS D3D9, 29.3 FPS GL.

	
	

	
  * Dynamic geometry (lots of particle systems) test (this is limited by vertex buffer writing speed and CPU calculating the particles, not draw by calls):
	
		
    * Core Duo with Radeon X1600: 170 FPS D3D9, 102 FPS GL.

		
    * P4 with GeForce 6800GT: 108 FPS D3D9, 74 FPS GL.

		
    * Core2 Duo with Radeon HD 2600: 325 FPS D3D9, 242 FPS GL.

	
	

	
  * ...and so on.



To be fair, there are a couple of tests where on some hardware OpenGL has a slight edge. But in 95% of the cases, D3D9 is faster. Not to mention that we have about 10x less broken hardware/driver workarounds for D3D9 than we have for OpenGL...

What gives? Either our OpenGL code is horribly suboptimal, or _"OpenGL is faster!!!!11oneoneeleven"_ is a myth. I have trouble figuring out in which places our code would be horribly suboptimal, I think we follow all advice given by hardware vendors on how to make OpenGL efficient (not that there is much advice out there though...).

There isn't much software that can run the same content on both D3D and OpenGL and is suitable for benchmarking. I tried [Ogre 3D](http://ogre3d.org) demos on one machine (GeForce 6800GT card) and guess what? D3D9 is faster in tests that specifically stress draw count (like the instancing demo... D3D9 is faster both in instanced and non-instanced modes).

Am I crazy?
