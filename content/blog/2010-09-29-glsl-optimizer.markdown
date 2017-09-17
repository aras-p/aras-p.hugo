---
tags:
- code
- opengl
- unity
comments: true
date: 2010-09-29T12:39:21Z
slug: glsl-optimizer
status: publish
title: GLSL Optimizer
url: /blog/2010/09/29/glsl-optimizer/
wordpress_id: "561"
---

During development of [Unity 3.0](http://unity3d.com/unity/whats-new/unity-3), I was not-so-pleasantly surprised to see that our [cross-compiled](/blog/2010/05/21/compiling-hlsl-into-glsl-in-2010/) shaders run _slow_ on iPhone 3Gs. And by "slow", I mean **SLOW**; at the speeds of "stop the presses, we can not ship brand new OpenGL ES 2.0 support with THAT performance".


**Back story**

Take this HLSL pixel shader for particles, that does nothing but multiplies texture with per-vertex color:


```
half4 frag (v2f i) : COLOR { return i.color * tex2D (_MainTex, i.texcoord); }
```


This is about as simple as it can get; should be one texture fetch and one multiply for the GPU.

Now _of course_, when HLSL gets cross-compiled into GLSL, it is augmented by some dummy functions/moves to match GLSL's semantics of "a function called main that takes no arguments and returns no value". So you get something like this in GLSL:



```
vec4 frag (in v2f i) { return i.color * texture2D (_MainTex, i.texcoord); }
void main() {
    vec4 xl_retval;
    v2f xlt_i;
    xlt_i.color = gl_Color;
    xlt_i.texcoord = gl_TexCoord[0];
    xl_retval = frag (xlt_i);
    gl_FragData[0] = xl_retval;
}
```



Makes sense. The original function was translated, and main() got added that fills in the input structure, calls the function and writes result to `gl_FragData[0]` (aka `gl_FragColor`).

Lo and behold, the above (with some OpenGL ES 2.0 specific stuff added, like precision qualifiers, definitions of varyings etc.) runs like sh*t on a mobile platform.

Which probably means **mobile platform drivers are quite bad at optimizing GLSL**. I mostly tested iOS, but some tests on Android indicate that situation is the same (maybe even worse, depending on exact kind of Android you have). Which is sad since said platforms also do not have any way to precompile shaders offline, where they could afford good but slow compilers.

Now of course, if you're writing GLSL shaders by hand, you're probably writing close to optimal code, with no redundant data moves or wrapper functions. But if you're cross-compiling them from Cg/HLSL, or generating from some shader fragments, or from visual shader editors, you probably depend on shader compiler being decent at optimizing redundant bits.


**GLSL Optimizer**

Around the same time I accidentally discovered that [Mesa 3D](http://mesa3d.org/) guys are working on new GLSL compiler, dubbed [GLSL2](http://cgit.freedesktop.org/mesa/mesa/log/?h=glsl2). I looked at the code and I liked it a lot; very hackable and "no bullshit" approach. So I took that Mesa's GLSL compiler and made it output GLSL back after it has done all the optimizations.

Here it is: [**http://github.com/aras-p/glsl-optimizer**](http://github.com/aras-p/glsl-optimizer)

It reads GLSL, does some architecture independent optimizations (dead code removal, algebraic simplifications, constant propagation, constant folding, inlining, ...) and spits out "optimized" GLSL back.


**Results**

The above simple particle shader example. GLSL optimizer optimizes it into:




```
void main() {
    gl_FragData[0] =
        (gl_Color.xyzw * texture2D (_MainTex, gl_TexCoord[0].xy)).xyzw;
}
```




Save for redundant swizzle outputs (on my todo list), this is pretty much what you'd be writing by hand. No redundant moves, function call inlined, no extra temporaries, sweet!

How much difference does this make?

[![](/blog/wp-content/uploads/2010/09/glslOptParticlesNo.jpg)](/blog/wp-content/uploads/2010/09/glslOptParticlesNo.png)[![](/blog/wp-content/uploads/2010/09/glslOptParticlesYes.jpg)](/blog/wp-content/uploads/2010/09/glslOptParticlesYes.png)

Lots of particles, non-optimized GLSL on the left; optimized GLSL on the right (click for larger image). **Yep, it's 236 vs. 36 milliseconds/frame** (4 vs. 27 FPS).

This result is for iPhone 3Gs running iOS 4.1. Some Android results: Motorola Droid (some PowerVR GPU): 537 vs. 223 ms; Nexus One (Snapdragon 8250 w/ Adreno GPU): 155 vs. 155 ms (yay! good drivers!); Samsung Galaxy S (some PowerVR GPU): 200 vs. 60 ms. All tests were ran at native device resolutions, so do not take this as performance comparisons between devices.


What about a more complex shader example? Let's try per-pixel lit Diffuse shader (which is quite simple, but will do ok as "complex shader" example for a mobile platform). You can see that the GLSL code below is [mostly auto-generated](/blog/2010/07/16/surface-shaders-one-year-later/); writing it by hand wouldn't produce that many data moves, unused struct members etc. Cg compiles original shader code into 10 ALU and 1 TEX instructions for D3D9 pixel shader 2.0, and is able to optimize away all the redundant stuff.





```
struct SurfaceOutput {
    vec3 Albedo;
    vec3 Normal;
    vec3 Emission;
    float Specular;
    float Gloss;
    float Alpha;
};
struct Input {
    vec2 uv_MainTex;
};
struct v2f_surf {
    vec4 pos;
    vec2 hip_pack0;
    vec3 normal;
    vec3 vlight;
};
uniform vec4 _Color;
uniform vec4 _LightColor0;
uniform sampler2D _MainTex;
uniform vec4 _WorldSpaceLightPos0;
void surf (in Input IN, inout SurfaceOutput o) {
    vec4 c;
    c = texture2D (_MainTex, IN.uv_MainTex) * _Color;
    o.Albedo = c.xyz;
    o.Alpha = c.w;
}
vec4 LightingLambert (in SurfaceOutput s, in vec3 lightDir, in float atten) {
    float diff;
    vec4 c;
    diff = max (0.0, dot (s.Normal, lightDir));
    c.xyz  = (s.Albedo * _LightColor0.xyz) * (diff * atten * 2.0);
    c.w  = s.Alpha;
    return c;
}
vec4 frag_surf (in v2f_surf IN) {
    Input surfIN;
    SurfaceOutput o;
    float atten = 1.0;
    vec4 c;
    surfIN.uv_MainTex = IN.hip_pack0.xy;
    o.Albedo = vec3 (0.0);
    o.Emission = vec3 (0.0);
    o.Specular = 0.0;
    o.Alpha = 0.0;
    o.Gloss = 0.0;
    o.Normal = IN.normal;
    surf (surfIN, o);
    c = LightingLambert (o, _WorldSpaceLightPos0.xyz, atten);
    c.xyz += (o.Albedo * IN.vlight);
    c.w = o.Alpha;
    return c;
}
void main() {
    vec4 xl_retval;
    v2f_surf xlt_IN;
    xlt_IN.hip_pack0 = vec2 (gl_TexCoord[0]);
    xlt_IN.normal = vec3 (gl_TexCoord[1]);
    xlt_IN.vlight = vec3 (gl_TexCoord[2]);
    xl_retval = frag_surf (xlt_IN);
    gl_FragData[0] = xl_retval;
}
```





Running the above through GLSL optimizer produces this:




```
uniform vec4 _Color;
uniform vec4 _LightColor0;
uniform sampler2D _MainTex;
uniform vec4 _WorldSpaceLightPos0;
void main ()
{
    vec4 c;
    vec4 tmpvar_32;
    tmpvar_32 = texture2D (_MainTex, gl_TexCoord[0].xy) * _Color;
    vec3 tmpvar_33;
    tmpvar_33 = tmpvar_32.xyz;
    float tmpvar_34;
    tmpvar_34 = tmpvar_32.w;
    vec4 c_i0_i1;
    c_i0_i1.xyz = ((tmpvar_33 * _LightColor0.xyz) *
    	(max (0.0, dot (gl_TexCoord[1].xyz, _WorldSpaceLightPos0.xyz)) * 2.0)).xyz;
    c_i0_i1.w = (vec4(tmpvar_34)).w;
    c = c_i0_i1;
    c.xyz = (c_i0_i1.xyz + (tmpvar_33 * gl_TexCoord[2].xyz)).xyz;
    c.w = (vec4(tmpvar_34)).w;
    gl_FragData[0] = c.xyzw;
}
```




All functions got inlined, all unused variable assignments got eliminated, and most of redundant moves are gone. There are some redundant moves left though (again, on my todo list), and the variables are assigned cryptic names after inlining. But otherwise, writing the equivalent shader by hand would be pretty close.

Difference between non-optimized and optimized GLSL in this case:

[![](/blog/wp-content/uploads/2010/09/glslOptDiffuseNo.jpg)](/blog/wp-content/uploads/2010/09/glslOptDiffuseNo.png)[![](/blog/wp-content/uploads/2010/09/glslOptDiffuseYes.jpg)](/blog/wp-content/uploads/2010/09/glslOptDiffuseYes.png)

Non-optimized vs. optimized: **350 vs. 267 ms/frame** (2.9 vs. 3.7 FPS). Not bad either!


**Closing thoughts**

Pulling off this GLSL optimizer quite late in [Unity 3.0](http://unity3d.com/unity/whats-new/unity-3) release cycle was a risky move, but it did work.

Hats off to Mesa folks (Eric Anholt, Ian Romanick, Kenneth Graunke et al) for making an awesome codebase of the GLSL compiler! I haven't merged up latest GLSL compiler developments on Mesa tree; they've implemented quite a few new compiler optimizations but I was too busy shipping Unity 3 already. Will try to merge them in soon-ish.

I've tested non-optimized vs. optimized GLSL a bit on a desktop platform (MacBook Pro, GeForce 8600M, OS X 10.6.4) and there is no observable speed difference. Which makes sense, and I _would have expected_ mobile drivers to be good at optimization as well, but apparently that's not the case.

Now of course, mobile drivers will improve over time, and I hope offline "GLSL optimization" step will become obsolete in the future. I still think it makes perfect sense to fully compile shaders offline, so at runtime there's no trace of GLSL at all (just load binary blob of GPU microcode into the driver), but that's a story for another day.

In the meantime, you're welcome to try [GLSL Optimizer](http://github.com/aras-p/glsl-optimizer) out!
