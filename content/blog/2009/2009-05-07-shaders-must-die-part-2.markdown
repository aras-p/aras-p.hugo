---
tags:
- gpu
- rendering
- unity
comments: true
date: 2009-05-07T23:35:28Z
slug: shaders-must-die-part-2
status: publish
title: Shaders must die, part 2
url: /blog/2009/05/07/shaders-must-die-part-2/
wordpress_id: "339"
---

I started playing around with the idea of "[shaders must die](/blog/2009/05/05/shaders-must-die/)". I'm experimenting with extracting "surface shaders" for now.

Right now my experimental pipeline is:

  1. Write a surface shader file
  2. Perl script transforms it into Unity 2.x shader file
  3. Which in turn is compiled by Unity into all lighting/shadows permutations, for D3D9 and OpenGL backends. Cg is used for actual shader compilation.


I have _very_ simple cases working. For example: 


     Properties
         2D _MainTex
     EndProperties
     Surface
         o.Albedo = SAMPLE(_MainTex);
     EndSurface


This is a "no bullshit" source code for a simple Diffuse (Lambertian) shader, 87 bytes of text.

The Perl script produces a Unity 2.x shader. This will be long, but bear with me - I'm trying to show how much stuff has to be written right now, when we're operating on vertex/pixel shader level. See [Attenuation and Shadows for Pixel Lights](http://unity3d.com/support/documentation/Components/SL-Attenuation.html) in Unity docs for how this system works.


     Shader "ShaderNinja/Diffuse" {
     Properties {
       _MainTex ("_MainTex", 2D) = "" {}
     }
     SubShader {
       Tags { "RenderType"="Opaque" }
       LOD 200
       Blend AppSrcAdd AppDstAdd
       Fog { Color [_AddFog] }
       Pass {
         Tags { "LightMode"="PixelOrNone" }
     CGPROGRAM
     #pragma fragment frag
     #pragma fragmentoption ARB_fog_exp2
     #pragma fragmentoption ARB_precision_hint_fastest
     #include "UnityCG.cginc"
     uniform sampler2D _MainTex;
     struct v2f {
         float2 uv_MainTex : TEXCOORD0;
     };
     struct f2l {
         half4 Albedo;
     };
     half4 frag (v2f i) : COLOR0 {
         f2l o;
         o.Albedo = tex2D(_MainTex,i.uv_MainTex);
         return o.Albedo * _PPLAmbient * 2.0;
     }
     ENDCG
       }
       Pass {
         Tags { "LightMode"="Pixel" }
     CGPROGRAM
     #pragma vertex vert
     #pragma fragment frag
     #pragma multi_compile_builtin
     #pragma fragmentoption ARB_fog_exp2
     #pragma fragmentoption ARB_precision_hint_fastest
     #include "UnityCG.cginc"
     #include "AutoLight.cginc"
     struct v2f {
         V2F_POS_FOG;
         LIGHTING_COORDS
         float2 uv_MainTex;
         float3 normal;
         float3 lightDir;
     };
     uniform float4 _MainTex_ST;
     v2f vert (appdata_tan v) {
         v2f o;
         PositionFog( v.vertex, o.pos, o.fog );
         o.uv_MainTex = TRANSFORM_TEX(v.texcoord, _MainTex);
         o.normal = v.normal;
         o.lightDir = ObjSpaceLightDir(v.vertex);
         TRANSFER_VERTEX_TO_FRAGMENT(o);
         return o;
     }
     uniform sampler2D _MainTex;
     struct f2l {
         half4 Albedo;
         half3 Normal;
     };
     half4 frag (v2f i) : COLOR0 {
         f2l o;
         o.Normal = i.normal;
         o.Albedo = tex2D(_MainTex,i.uv_MainTex);
         return DiffuseLight (i.lightDir, o.Normal, o.Albedo, LIGHT_ATTENUATION(i));
     }
     ENDCG
       }
     }
     Fallback "VertexLit"
     }



Phew, that is quite some typing to get simple diffuse shader (1607 bytes)! Well, at least all the lighting/shadow combinations are handled by Unity macros here. When Unity takes this shader and compiles into all permutations, it results in 58 kilobytes of shader assembly (D3D9 + OpenGL, 17 light/shadow combinations).

Let's try something slightly different: bumpmapped, with a detail texture:


     Properties
         2D _MainTex
         2D _Detail
         2D _BumpMap
     EndProperties
     Surface
         o.Albedo = SAMPLE(_MainTex) * SAMPLE(_Detail) * 2.0;
         o.Normal = SAMPLE_NORMAL(_BumpMap);
     EndSurface
     


This is 173 bytes of text. Generated Unity shader is 2098 bytes, which compiles into 74 kilobytes of shader assembly.

In this case, the processing script detects that surface shader modifies normal per pixel, and does the necessary tangent space light transformations. It all just works!

So this is where I am now. Next up: detect which lighting model to use based on surface parameters (right now it always uses Lambertian). Fun!
