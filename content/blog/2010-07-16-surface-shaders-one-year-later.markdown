---
categories:
- gpu
- rendering
- unity
comments: true
date: 2010-07-16T08:38:43Z
slug: surface-shaders-one-year-later
status: publish
title: Surface Shaders, one year later
url: /blog/2010/07/16/surface-shaders-one-year-later/
wordpress_id: "530"
---

Over a year ago I had a thought that "Shaders must die" ([part 1](/blog/2009/05/05/shaders-must-die/), [part 2](/blog/2009/05/07/shaders-must-die-part-2/), [part 3](/blog/2009/05/10/shaders-must-die-part-3/)).

And what do you know - turns out we're trying to pull this off in upcoming [Unity 3](http://unity3d.com/unity/coming-soon/unity-3). We call this **Surface Shaders** cause I've a suspicion "shaders must die" as a feature name wouldn't have flied very far.



**Idea**

The main idea is that 90% of the time I just want to declare surface properties. This is what I want to say:


> Hey, albedo comes from this texture mixed with this texture, and normal comes from this normal map. Use Blinn-Phong lighting model please, and don't bother me again!



With the above, I don't have to care whether this will be used in a forward or deferred rendering, or how various light types will be handled, or how many lights per pass will be done in a forward renderer, or how some indirect illumination SH probes will come in, etc. I'm not interested in all that! These dirty bits are job of rendering programmers, _just make it work dammit_!

This is not a new idea. Most graphical shader editors _that make sense_ do not have "pixel color" as the final output node; instead they have some node that basically describes surface parameters (diffuse, specularity, normal, ...), and all the lighting code is usually not expressed in the shader graph itself. [OpenShadingLanguage](http://code.google.com/p/openshadinglanguage/) is a similar idea as well (but because it's targeted at offline rendering for movies, it's much richer & more complex).


**Example**

Here's a simple - but full & complete - Unity 3.0 shader that does diffuse lighting with a texture & a normal map.

<pre>
<span class="codedim">Shader "Example/Diffuse Bump" {
  Properties {
    _MainTex ("Texture", 2D) = "white" {}
    _BumpMap ("Bumpmap", 2D) = "bump" {}
  }
  SubShader {
    Tags { "RenderType" = "Opaque" }
    CGPROGRAM</span>
    #pragma surface surf Lambert
    struct Input {
      float2 uv_MainTex;
      float2 uv_BumpMap;
    };
    sampler2D _MainTex;
    sampler2D _BumpMap;
    void surf (Input IN, inout SurfaceOutput o) {
      o.Albedo = tex2D (_MainTex, IN.uv_MainTex).rgb;
      o.Normal = UnpackNormal (tex2D (_BumpMap, IN.uv_BumpMap));
    }
    <span class="codedim">ENDCG
  } 
  Fallback "Diffuse"
}</span>
</pre>

[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2010/07/SurfaceShaderDiffuseBump-150x150.png">}}](http://aras-p.info/blog/wp-content/uploads/2010/07/SurfaceShaderDiffuseBump.png)

Given pretty model & textures, it can produce pretty pictures! How cool is that?

I grayed out bits that are not really interesting (declaration of serialized shader properties & their UI names, shader fallback for older machines etc.). What's left is Cg/HLSL code, which is then augmented by tons of auto-generated code that deals with lighting & whatnot.

This surface shader dissected into pieces:




  * `#pragma surface surf Lambert`: this is a surface shader with main function "surf", and a Lambert lighting model. Lambert is one of predefined lighting models, but you can write your own.


  * `struct Input`: input data for the surface shader. This can have various predefined inputs that will be computed per-vertex & passed into your surface function per-pixel. In this case, it's two texture coordinates.


  * `surf` function: actual surface shader code. It takes Input, and writes into `SurfaceOutput` (a predefined structure). It is possible to write into custom structures, provided you use lighting models that operate on those structures. The actual code just writes Albedo and Normal to the output.




**What is generated**

Unity's "surface shader code generator" would take this, generate _actual_ vertex & pixel shaders, and compile them to various target platforms. With default settings in Unity 3.0, it would make this shader support:




  * Forward renderer and Deferred Lighting (Light Pre-Pass) renderer.


  * Objects with precomputed lightmaps and without.


  * Directional, Point and Spot lights; with projected light cookies or without; with shadowmaps or without. Well ok, this is only for forward renderer because in Light Pre-Pass lighting happens elsewhere.


  * For Forward renderer, it would compile in support for lights computed per-vertex and spherical harmonics lights computed per-object. It would also generate extra additive blended pass if needed for the case when additional per-pixel lights have to be rendered in separate passes.


  * For Light Pre-Pass renderer, it would generate base pass that outputs normals & specular power; and a final pass that combines albedo with lighting, adds in any lightmaps or emissive lighting etc.


  * It can optionally generate a shadow caster rendering pass (needed if custom vertex position modifiers are used for vertex shader based animation; or some complex alpha-test effects are done).



For example, here's code that would be compiled for a forward-rendered base pass with one directional light, 4 per-vertex point lights, 3rd order SH lights; optional lightmaps _(I suggest just scrolling down)_: 

    
    
    #pragma vertex vert_surf
    #pragma fragment frag_surf
    #pragma fragmentoption ARB_fog_exp2
    #pragma fragmentoption ARB_precision_hint_fastest
    #pragma multi_compile_fwdbase
    #include "HLSLSupport.cginc"
    #include "UnityCG.cginc"
    #include "Lighting.cginc"
    #include "AutoLight.cginc"
    struct Input {
    	float2 uv_MainTex : TEXCOORD0;
    };
    sampler2D _MainTex;
    sampler2D _BumpMap;
    void surf (Input IN, inout SurfaceOutput o)
    {
    	o.Albedo = tex2D (_MainTex, IN.uv_MainTex).rgb;
    	o.Normal = UnpackNormal (tex2D (_BumpMap, IN.uv_MainTex));
    }
    struct v2f_surf {
      V2F_POS_FOG;
      float2 hip_pack0 : TEXCOORD0;
      #ifndef LIGHTMAP_OFF
      float2 hip_lmap : TEXCOORD1;
      #else
      float3 lightDir : TEXCOORD1;
      float3 vlight : TEXCOORD2;
      #endif
      LIGHTING_COORDS(3,4)
    };
    #ifndef LIGHTMAP_OFF
    float4 unity_LightmapST;
    #endif
    float4 _MainTex_ST;
    v2f_surf vert_surf (appdata_full v) {
      v2f_surf o;
      PositionFog( v.vertex, o.pos, o.fog );
      o.hip_pack0.xy = TRANSFORM_TEX(v.texcoord, _MainTex);
      #ifndef LIGHTMAP_OFF
      o.hip_lmap.xy = v.texcoord1.xy * unity_LightmapST.xy + unity_LightmapST.zw;
      #endif
      float3 worldN = mul((float3x3)_Object2World, SCALED_NORMAL);
      TANGENT_SPACE_ROTATION;
      #ifdef LIGHTMAP_OFF
      o.lightDir = mul (rotation, ObjSpaceLightDir(v.vertex));
      #endif
      #ifdef LIGHTMAP_OFF
      float3 shlight = ShadeSH9 (float4(worldN,1.0));
      o.vlight = shlight;
      #ifdef VERTEXLIGHT_ON
      float3 worldPos = mul(_Object2World, v.vertex).xyz;
      o.vlight += Shade4PointLights (
        unity_4LightPosX0, unity_4LightPosY0, unity_4LightPosZ0,
        unity_LightColor0, unity_LightColor1, unity_LightColor2, unity_LightColor3,
        unity_4LightAtten0, worldPos, worldN );
      #endif // VERTEXLIGHT_ON
      #endif // LIGHTMAP_OFF
      TRANSFER_VERTEX_TO_FRAGMENT(o);
      return o;
    }
    #ifndef LIGHTMAP_OFF
    sampler2D unity_Lightmap;
    #endif
    half4 frag_surf (v2f_surf IN) : COLOR {
      Input surfIN;
      surfIN.uv_MainTex = IN.hip_pack0.xy;
      SurfaceOutput o;
      o.Albedo = 0.0;
      o.Emission = 0.0;
      o.Specular = 0.0;
      o.Alpha = 0.0;
      o.Gloss = 0.0;
      surf (surfIN, o);
      half atten = LIGHT_ATTENUATION(IN);
      half4 c;
      #ifdef LIGHTMAP_OFF
      c = LightingLambert (o, IN.lightDir, atten);
      c.rgb += o.Albedo * IN.vlight;
      #else // LIGHTMAP_OFF
      half3 lmFull = DecodeLightmap (tex2D(unity_Lightmap, IN.hip_lmap.xy));
      #ifdef SHADOWS_SCREEN
      c.rgb = o.Albedo * min(lmFull, atten*2);
      #else
      c.rgb = o.Albedo * lmFull;
      #endif
      c.a = o.Alpha;
      #endif // LIGHTMAP_OFF
      return c;
    }
    



Of those 90 lines of code, 10 are your original surface shader code; the remaining 80 would have to be pretty much written by hand in Unity 2.x days (well ok, less code would have to be written because 2.x had less rendering features). _But wait_, that was only base pass of the forward renderer! It also generates code for additive pass, for deferred base pass, deferred final pass, optionally for shadow caster pass and so on.

So this should be an easier to write lit shaders (it is for me at least). I hope this will also increase the number of Unity users who can write shaders at least 3 times _(i.e. to 30 up from 10!)_. It _should_ be more future proof to accomodate changes to the lighting pipeline we'll do in Unity next.


**Predefined Input values**

The Input structure can contain texture coordinates and some predefined values, for example view direction, world space position, world space reflection vector and so on. Code to compute them is only generated if they are _actually_ used. For example, if you use world space reflection to do some cubemap reflections (as emissive term) in your surface shader, then in Light Pre-Pass base pass the reflection vector will _not be computed_ (since it does not output emission, so by extension does not need reflection vector).

[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2010/07/SurfaceShaderRim-150x150.png">}}](http://aras-p.info/blog/wp-content/uploads/2010/07/SurfaceShaderRim.png)

As a small example, the shader above extended to do simple rim lighting:

<pre>
<span class="codedim">#pragma surface surf Lambert
struct Input {
    float2 uv_MainTex;
    float2 uv_BumpMap;</span>
    float3 viewDir;
<span class="codedim">};
sampler2D _MainTex;
sampler2D _BumpMap;</span>
float4 _RimColor;
float _RimPower;
<span class="codedim">void surf (Input IN, inout SurfaceOutput o) {
    o.Albedo = tex2D (_MainTex, IN.uv_MainTex).rgb;
    o.Normal = UnpackNormal (tex2D (_BumpMap, IN.uv_BumpMap));</span>
    half rim =
        1.0 - saturate(dot (normalize(IN.viewDir), o.Normal));
    o.Emission = _RimColor.rgb * pow (rim, _RimPower);
<span class="codedim">}</span>
</pre>    


**Vertex shader modifiers**

[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2010/07/SurfaceShaderNormalExtrusion-150x150.png">}}](http://aras-p.info/blog/wp-content/uploads/2010/07/SurfaceShaderNormalExtrusion.png)

It is possible to specify custom "vertex modifier" function that will be called at start of the generated vertex shader, to modify (or generate) per-vertex data. You know, vertex shader based tree wind animation, grass billboard extrusion and so on. It can also fill in any non-predefined values in the Input structure.

My favorite vertex modifier? Moving vertices along their normals.


**Custom Lighting Models**

There are a couple simple lighting models built-in, but it's possible to specify your own. A lighting model is nothing more than a function that will be called with the filled SurfaceOutput structure and per-light parameters (direction, attenuation and so on). Different functions would have to be called in forward and light pre-pass rendering cases; and naturally the light pre-pass one has much less flexibility. So for any fancy effects, it is possible to say "do not compile this shader for light pre-pass", in which case it will be rendered via forward rendering.

[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2010/07/SurfWrapLambert-150x150.png">}}](http://aras-p.info/blog/wp-content/uploads/2010/07/SurfWrapLambert.png)

Example of wrapped-Lambert lighting model:

<pre>
#pragma surface surf WrapLambert
half4 LightingWrapLambert (SurfaceOutput s, half3 dir, half atten) {
    dir = normalize(dir);
    half NdotL = dot (s.Normal, dir);
    half diff = NdotL * 0.5 + 0.5;
    half4 c;
    c.rgb = s.Albedo * _LightColor0.rgb * (diff * atten * 2);
    c.a = s.Alpha;
    return c;
}
<span class="codedim">struct Input {
    float2 uv_MainTex;
};
sampler2D _MainTex;
void surf (Input IN, inout SurfaceOutput o) {
    o.Albedo = tex2D (_MainTex, IN.uv_MainTex).rgb;
}</span>
</pre>


**Behind the scenes**

I'm using HLSL parser from Ryan Gordon's [mojoshader](http://hg.icculus.org/icculus/mojoshader/) to parse the original surface shader code and infer some things from the AST mojoshader produces. This way I can figure out what members are in what structures, go over function prototypes and so on. At this stage some error checking is done to tell the user his surface function is of wrong prototype, or his structures are missing required members - which is much better than failing with dozens of compile errors in the generated code later.

To figure out which surface shader inputs are _actually_ used in the various lighting passes, I'm generating small dummy pixel shaders, compile them with Cg and use Cg's API to query used inputs & outputs. This way I can figure out, for example, that a normal map nor it's texture coordinate is not actually used in Light Pre-Pass' final pass, and save some vertex shader instructions & a texcoord interpolator.

The code that is ultimately generated is compiled with various shader compilers depending on the target platform (Cg for PC/Mac, XDK HLSL for Xbox 360, PS3 Cg for PS3, and my own [fork of HLSL2GLSL](https://github.com/aras-p/hlsl2glslfork) for iPhone, Android and upcoming [NativeClient port of Unity](http://blogs.unity3d.com/2010/05/19/google-android-and-the-future-of-games-on-the-web/)).

So yeah, that's it. We'll see where this goes next, or what happens when Unity 3 will be released.
