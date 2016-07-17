---
tags:
- gpu
- rendering
- unity
comments: true
date: 2009-05-10T17:24:17Z
slug: shaders-must-die-part-3
status: publish
title: Shaders must die, part 3
url: /blog/2009/05/10/shaders-must-die-part-3/
wordpress_id: "350"
---

Continuing the series (see [Part 1](/blog/2009/05/05/shaders-must-die/), [Part 2](/blog/2009/05/07/shaders-must-die-part-2/))...

Got different lighting models (BRDFs) working. Without further ado, code snippets that produce real actual working shaders that work with lights & shadows and whatnot:

Simple Lambert (single color):


     Properties
         Color _Color
     EndProperties
     Surface
         o.Albedo = _Color;
     EndSurface
     Lighting Lambert
     



Let's add a texture:


     Properties
         2D _MainTex
         Color _Color
     EndProperties
     Surface
         o.Albedo = SAMPLE(_MainTex) * _Color;
     EndSurface
     Lighting Lambert



Change light model to Half-Lambert (a.k.a. wrapped diffuse):


     // ...everything the same
     Lighting HalfLambert



Blinn-Phong, with constant exponent & constant specular color, modulated by gloss map in main texture's alpha:


     Properties
         2D _MainTex
         Color _Color
         Color _SpecColor
         Float _Exponent
     EndProperties
     Surface
         half4 col = SAMPLE(_MainTex);
         o.Albedo = col * _Color;
         o.Specular = _SpecColor.rgb * col.a;
         o.Exponent = _Exponent;
     EndSurface
     Lighting BlinnPhong



The same Blinn-Phong, with added normal map:



     Properties
         2D _MainTex
         2D _BumpMap
         Color _Color
         Color _SpecColor
         Float _Exponent
     EndProperties
     Surface
         half4 col = SAMPLE(_MainTex);
         o.Albedo = col * _Color;
         o.Specular = _SpecColor.rgb * col.a;
         o.Exponent = _Exponent;
         o.Normal = SAMPLE_NORMAL(_BumpMap);
     EndSurface
     Lighting BlinnPhong



I also made an illustrative-style BRDF (see [Illustrative Rendering in Team Fortress 2](http://www.valvesoftware.com/publications.html)), but that only requires above sample to have "Lighting TF2" at the end.

Another thing I tried is surface that has Albedo dependent on a viewing angle, similar to [Layered Car Paint Shader](http://developer.amd.com/media/gpu_assets/ShaderX2_LayeredCarPaintShader.pdf). It works:


     Properties
         2D _MainTex
         2D _BumpMap
         2D _SparkleTex
         Float _Sparkle
         Color _PrimaryColor
         Color _HighlightColor
     EndProperties
     Surface
         half4 main = SAMPLE(_MainTex);
         half3 normal  = SAMPLE_NORMAL(_BumpMap);
         half3 normalN = normalize(SAMPLE_NORMAL(_SparkleTex));
         half3 ns = normalize (normal + normalN * _Sparkle);
         half3 nss = normalize (normal + normalN);
         i.viewDir = normalize(i.viewDir);
         half nsv = max(0,dot(ns, i.viewDir));
         half3 c0 = _PrimaryColor.rgb;
         half3 c2 = _HighlightColor.rgb;
         half3 c1 = c2 * 0.5;
         half3 cs = c2 * 0.4;    
         half3 tone =
             c0 * nsv +
             c1 * (nsv*nsv) +
             c2 * (nsv*nsv*nsv*nsv) +
             cs * pow(saturate(dot(nss,i.viewDir)), 32);
         main.rgb *= tone;
         o.Albedo = main;
         o.Normal = normal;
     EndSurface
     Lighting Lambert



Up next:




  * How and where emissive terms should be placed. I cautiously omitted all emissive terms from the above examples (so my layered car shader is without reflections right now).


  * Where should things like rim lighting go? I'm not sure if it's a surface property (increasing albedo/emission with angle) or a lighting property (a back light).



My impressions so far:


  * I like that I don't have to write down vertex-to-fragment structures or the vertex shader. In most cases all the vertex shader does is transform stuff and pass it down to later stages, plus occasional computations that are linear over the triangle. No good reason to write it by hand.


  * I like that the above shaders do _not_ deal with _how_ the rendering is actually done. For Unity's case, I'm compiling them into single pass per light forward renderer, but they _should_ just work with multiple lights per pass, deferred etc. _Of course, that still has to be proven!_



So far so good.

Series index: Shaders must die, [Part 1](/blog/2009/05/05/shaders-must-die/), [Part 2](/blog/2009/05/07/shaders-must-die-part-2/), [**Part 3**](/blog/2009/05/10/shaders-must-die-part-3/).

