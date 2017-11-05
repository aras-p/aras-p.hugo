---
tags:
- gpu
- rant
- rendering
comments: true
date: 2009-05-05T14:59:48Z
slug: shaders-must-die
status: publish
title: Shaders must die
url: /blog/2009/05/05/shaders-must-die/
wordpress_id: "324"
---

It came in as a simple [thought](http://twitter.com/aras_p/status/1651784380), and now I can't shake it off. So I say:

[![Shaders Must Die](/blog/wp-content/uploads/2009/05/shadersmustdie.jpg)](/blog/wp-content/uploads/2009/05/shadersmustdie.jpg)

Ok, now that the controversial bits are done, let's continue.


Most of this can be (and probably is) wrong, and I haven't given it enough thought yet. But here's my thinking about shaders of "regular scene objects". All of below is about things that need to interact with lighting; I'm not talking about shaders for postprocessing, one-off uses, special effects, GPGPU or kitchen sinks.

**Operating on vertex/pixel shader level is a wrong abstraction level**

Instead, it should be separated out into "_surface shader_" (albedo, normal, specularity, ...), "_lighting model_" (Lambertian, Blinn Phong, ...) and "_light shader_" (attenuation, cookies, shadows).





  * Probably 90% of the cases would only touch the surface shader (mostly mix textures/colors in various ways), and choose from some precooked lighting models.


  * 9% of the cases would tweak the lighting model. Most of the things would settle for "standard" (Blinn-Phong or similar), with some stuff using skin or anisotropic or ...


  * The "light shader" only needs to be touched once in a blue moon by ninjas. Once the shadowing and attenuation systems are implemented, there's almost no reason for shader authors to see all the dirty bits.



Yes, current hardware operates on vertex/geometry/pixel shaders, which is a logical thing to do for hardware. After all, these are the primitives it works on when rendering. But those primitives are _not_ the things you work on when authoring how a surface should look or how it should react to a light.

**Simple code; no redundant info; sensible defaults**

In the ideal world, here's a simple surface shader (the syntax is deliberately stupid):


    Haz Texture;
    Albedo = sample Texture;


Or with bump mapping added:


    Haz Texture;
    Haz NormalMap;
    Albedo = sample Texture;
    Normal = sample_normal NormalMap;


And this should be _all_ the info you have to provide. This would choose the lighting model based on used things (in this case, Lambertian). It would _somehow_ just work with all kinds of lights, shadows, ambient occlusion and whatnot.

Compare to how much has to be written to implement a simple surface in your current shader technology, so that it would work "with everything".

From the above shader, proper hardware shaders can be generated for DX9, DX11, DX1337, OpenGL, next-gen and next-next-gen consoles, mobile platforms with capable hardware, etc.

It can be used in accumulative forward rendering, forward rendering with multiple lights per pass, hybrid (light pre-pass / prelight) rendering, deferred rendering etc. Heck, even for a raytracer if you have one at hand.

I want!

Now of course, it won't be as nice as more complex materials have to be expressed. Some might not even be possible. But shader text complexity should grow with material complexity; and all information that is redundant, implied, inferred or useless should be eliminated. _There's no good reason to stick to conventions and limits of current hardware just because it operates like that_.

Shaders must die!
