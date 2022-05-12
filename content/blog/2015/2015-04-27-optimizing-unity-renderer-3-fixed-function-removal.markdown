---
tags:
- code
- unity
- work
- performance
comments: true
date: 2015-04-27T00:00:00Z
title: 'Optimizing Unity Renderer Part 3: Fixed Function Removal'
url: /blog/2015/04/27/optimizing-unity-renderer-3-fixed-function-removal/
---

*[Last time](/blog/2015/04/04/optimizing-unity-renderer-2-cleanups/) I wrote about some cleanups and optimizations. Since then,
I got sidetracked into doing some Unity 5.1 work, removing Fixed Function Shaders and other unrelated things. So not
much blogging about optimization per se.*

### Fixed Function What?

Once upon a time, GPUs did not have these fancy things called "programmable shaders"; instead they could be
configured in more or less *(mostly less)* flexible ways, by enabling and disabling certain features.
For example, you could tell them to calculate some lighting per-vertex; or to add two textures together per-pixel.

Unity started out a long time ago, back when *fixed function GPUs* were still a thing; so naturally it supports writing
shaders in this fixed function style ("shaderlab" in Unity lingo). The syntax for them is quite easy, and actually
they are much faster to write than vertex/fragment shader pairs if all you need is some simple shader.

For example, a Unity [shader pass](http://docs.unity3d.com/Manual/SL-Pass.html) that turns on regular alpha blending,
and outputs texture multiplied by material color, is like this:
 
``` c++
Pass
{
	Blend SrcAlpha OneMinusSrcAlpha
	SetTexture [_MainTex] { constantColor[_Color] combine texture * contant }
}
```

compare that with a vertex+fragment shader that does exactly the same:

``` c++
Pass
{
	Blend SrcAlpha OneMinusSrcAlpha
	CGPROGRAM
	#pragma vertex vert
	#pragma fragment frag
	#include "UnityCG.cginc"
	struct v2f
	{
		float2 uv : TEXCOORD0;
		float4 pos : SV_POSITION; 
	};
	float4 _MainTex_ST;
	v2f vert (float4 pos : POSITION, float2 uv : TEXCOORD0)
	{
		v2f o;
		o.pos = mul(UNITY_MATRIX_MVP, pos);
		o.uv = TRANSFORM_TEX(uv, _MainTex);
		return o;
	}
	sampler2D _MainTex;
	fixed4 _Color;
	fixed4 frag (v2f i) : SV_Target
	{
		return tex2D(_MainTex, i.uv) * _Color;
	}
	ENDCG
}
```

Exactly the same result, a lot more boilerplate typing.

Now, we have removed support for *actually fixed function* GPUs and platforms (in practice: OpenGL ES 1.1 on
mobile and Direct3D 7 GPUs on Windows) in Unity 4.3 *(that was in late 2013)*. So there's no big technical reason
to keep on writing shaders in this "fixed function style"... except that 1) a lot of existing projects and packages
already have them and 2) for simple things it's less typing.

That said, fixed function shaders in Unity have downsides too:

* They do not work on consoles (like PS4, XboxOne, Vita etc.), primarily because generating shaders at runtime is very
  hard on these platforms.
* They do not work with [MaterialPropertyBlocks](http://docs.unity3d.com/ScriptReference/MaterialPropertyBlock.html), and
  as a byproduct, do not work with Unity's Sprite Renderer nor materials animated via animation window.
* By their nature they are suitable for only very simple things. Often you could start with a simple fixed function shader, only
  to find that you need to add more functionality that can't be expressed in fixed function vertex lighting /
  texture combiners.

### How are fixed function shaders implemented in Unity? And why?

Majority of platforms we support do not have the concept of "fixed function rendering pipeline" anymore, so these shaders
are internally converted into "actual shaders" and these are used for rendering. The only exceptions where fixed function
still exists are: legacy desktop OpenGL (GL 1.x-2.x) and Direct3D 9.

Truth to be told, even on Direct3D 9 we've been creating actual shaders to emulate fixed function since Unity 2.6;
[see this old article](/texts/VertexShaderTnL.html). So D3D9 was the first platform we got that implemented this
"lazily create actual shaders for each fixed function shader variant" thing.

Then more platforms came along; for OpenGL ES 2.0 we implemented a very similar thing as for D3D9, just instead of
concatenating bits of D3D9 shader assembly we'd concatenate GLSL snippets. And then even more platforms came
(D3D11, Flash, Metal); each of them implemented this "fixed function generation" code. The code is not terribly
complicated; the problem was pretty well understood and we had enough graphics tests verify it works.

[{{<imgright src="/img/blog/2015-04/Opt3-Why.png">}}](/img/blog/2015-04/Opt3-Why.png)

Each step along the way, somehow no one *really* questioned why we keep doing all this. ***Why do all that
at runtime, instead of converting fixed-function-style shaders into "actual shaders" offline, at shader import time?***
(well ok, plenty of people asked that question; the answer has been *"yeah that would make sense; just requires
someone to do it"* for a while...)

A long time ago generating "actual shaders" for for fixed function style ones offline was not very practical, due to sheer number
of possible variants that need to be supported. The trickiest ones to support were texture coordinates *(routing
of UVs into texture stages; optional texture transformation matrices; optional projected texturing; and optional
[texture coordinate generation](http://docs.unity3d.com/Manual/SL-ImplementingTexGen.html))*. But hey, we've removed quite some
of that in Unity 5.0 anyway. Maybe now it's easier? Turns out, it is.


### Converting fixed function shaders into regular shaders, at import time

[{{<imgright src="/img/blog/2015-04/Opt3-WikiDescriptionSmall.png">}}](/img/blog/2015-04/Opt3-WikiDescription.png)

So I set out to do just that. Remove all the runtime code related to "fixed function shaders"; instead just turn them into
"regular shaders" when importing the shader file in Unity editor. Created an outline of idea & planned work on our wiki, and started
coding. I thought the end result would be "I'll add 1000 lines and remove 4000 lines of existing code". *I was wrong!*

Once I got the basics of shader import side working (turned out, about 1000 lines of code indeed), I started removing all
the fixed function bits. That was a day of *pure joy*:

[{{<img src="/img/blog/2015-04/Opt3-RemoveAllTheCode.png">}}](/img/blog/2015-04/Opt3-RemoveAllTheCode.png)

Almost twelve thousand lines of code, gone. This is amazing!

> I never realized all the fixed function code was that large. You write it for one platform, and then it basically works;
> then some new platform comes and the code is written for that, and then it basically works. By the time you get N platforms,
> all that code is **massive**, but it never came in one sudden lump so no one realized it.
>
> **Takeaway**: once in a while, look at a whole subsystem. You migth be surprised at how much it grew over the years.
> Maybe some of the conditions for why it is like that do not apply anymore?


### Sidenote: per-vertex lighting in a vertex shader

If there was one thing that was easy with fixed function pipeline, is that many features were easily *composable*.
You could enable any number of lights (well, up to 8); and each of them could be a directional, point or a spot light;
toggling specular on or off was just a flag away; same with fog etc.

It feels like "easy composition of features" is a **big thing we lost when we all moved to shaders**. Shaders as we know them
(i.e. vertex/fragment/... stages) aren't composable at all! Want to add some optional feature -- that pretty much means
either "double the amount of shaders", or branching in shaders, or generating shaders at runtime. Each of these has
their own tradeoffs.

For example, how do you write a vertex shader that can do up to 8 arbitrary lights? There are many ways of doing it; what I have done right now is:

Separate vertex shader variants for "any spot lights present?", "any point lights present?" and "directional lights only" cases.
My guess is that spot lights are *very rarely* used with per-vertex fixed function lighting; they just look really bad.
So in many cases, the cost of "compute spot lights" won't be paid.

Number of lights is passed into the shader as an integer, and the shader loops over them. *Complication*: OpenGL ES 2.0
/ WebGL, where loops can only have constant number of iterations :( In practice many
OpenGL ES 2.0 implementations do not enforce that limitation; however WebGL implementations do. At this very moment
I don't have a good answer; on ES2/WebGL I just always loop over all 8 possible lights (the unused lights have black
colors set). For a real solution, instead of a regular loop like this:

```
uniform int lightCount;
// ...
for (int i = 0; i < lightCount; ++i)
{
	// compute light #i
}
```

I'd like to emit a shader like this when compiling for ES2.0/WebGL:

```
uniform int lightCount;
// ...
for (int i = 0; i < 8; ++i)
{
	if (i == lightCount)
		break;
	// compute light #i
}
```

Which would be valid according to the spec; it's just annoying to deal with seemingly arbitrary limitations like this
(I heard that WebGL 2 does not have this limitation, so that's good).


### What do we have now

So the current situation is that, by removing a lot of code, I achieved the following upsides:

* "Fixed function style" shaders work on all platforms now (consoles! dx12!).
* They work more consistenly across platforms (e.g. specular highlights and attenuation were subtly different
  between PC & mobile before).
* MaterialPropertyBlocks work with them, which means sprites etc. all work.
* Fixed function shaders aren't rasterized at a weird half-pixel offset on Windows Phone anymore.
* It's easier to go from fixed functon shader to an actual shader now; I've added a button in shader inspector that just shows
  all the generated code; you can paste that back and start extending it.
* Code is smaller; that translates to executable size too. For example Windows 64 bit player got smaller by 300 kilobytes.
* Rendering is slightly faster (even when fixed function shaders aren't used)!

That last point was not the primary objective, but is a nice bonus. No particular big place was affected, but quite a few
branches and data members were removed from platform graphics abstraction (that only were there to support fixed function
runtime). Depending on the projects I've tried, I've seen up to 5% CPU time saved on the rendering thread
(e.g. 10.2ms -> 9.6ms), which is pretty good.

Are there any downsides? Well, yes, a couple:

* You can not create fixed function shaders *at runtime* anymore. Before, you could do something like a
  `var mat = new Material("<fixed function shader string>")` and it would all work. Well, except for consoles, where
  these shaders were never working. For this reason I've made the `Material(string)` constructor be obsolete with a warning
  for Unity 5.1; but it will actually stop working later on.
* It's a web player backwards compatibility breaking change, i.e. if/once this code lands to production (for example, Unity 5.2)
  that would mean 5.2 runtime can not playback Unity 5.0/5.1 data files anymore. Not a big deal, we just have to decide
  if with (for example) 5.2 we'll switch to a different web player
  [release channel](http://blogs.unity3d.com/2012/11/29/testing-your-web-player-content-against-the-latest-unity-runtime-versions/).
* Several corner cases might not work. For example, a fixed function shader that uses a globally-set texture that is
  *not a 2D texture*. Nothing about that texture is specified in the shader source itself; so while I'm generating
  actual shader at import time I don't know if it's a 2D or a Cubemap texture. So for global textures I just assume they
  are going to be 2D ones.
* *Probably that's it!*

Removing all that fixed function runtime support also revealed more potential optimizations. Internally we
were passing things like "texture type" (2D, Cubemap etc.) for all texture changes -- but it seems that it was only the fixed
function pipeline that was using it. Likewise, we are passing a vertex-declaration-like structure for each and every draw call;
but now *I think* that's not really needed anymore. Gotta look further into it.

Until next time!
