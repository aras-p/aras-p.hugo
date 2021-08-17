---
title: Compact Normal Storage for small G-Buffers
comments: true
sharing: true
footer: true
menusection: texts
url: /texts/CompactNormalStorage.html
---

<ul style="font-size: 80%">
<li><a href="#intro">Intro</a></li>
<li><a href="#method00xyz">Baseline: store X&amp;Y&amp;Z</a></li>
<li><a href="#method01xy">Method 1: X&amp;Y</a></li>
<li><a href="#method03spherical">Method 3: Spherical Coordinates</a></li>
<li><a href="#method04spheremap">Method 4: Spheremap Transform</a></li>
<li><a href="#method07stereo">Method 7: Stereographic projection</a></li>
<li><a href="#method08ppview">Method 8: Per-pixel View Space</a></li>
<li><a href="#perf-comparison">Performance Comparison</a></li>
<li><a href="#q-comparison">Quality Comparison</a></li>
<li><a href="#changelog">Changelog</a></li>
</ul>


<h3>Heads up!</h3>

I wrote this article in year 2009. Since then a lot of things have happened. A decade later, it looks like
everyone settled onto something like semi-octahedral normal encoding. Which was not described here since, well,
it was not quite invented yet :) See:

* "[Octahedron normal vector encoding](https://knarkowicz.wordpress.com/2014/04/16/octahedron-normal-vector-encoding/)" (Narkowicz), 2014.
* "[Survey of Efficient Representations for Independent Unit Vectors](http://jcgt.org/published/0003/02/01/)" (Cigolle, Donow, Evangelakos, Mara, McGuire, Meyer), 2014
* "[Signed Octahedron Normal Encoding](https://johnwhite3d.blogspot.com/2017/10/signed-octahedron-normal-encoding.html)" (White), 2017

So my advice is just to use that and ignore everything below :)


<a name="intro"></a>
<h3>Intro</h3>

<p>
Various deferred shading/lighting approaches or image postprocessing effects need to store
normals as part of their G-buffer. Let's figure out a compact storage method for view space normals.
In my case, main target is minimalist G-buffer, where depth and normals are packed into a single
32 bit (8 bits/channel) render texture. I try to minimize error and shader cycles to encode/decode.
</p>

<p>
Now of course, 8 bits/channel storage for normals <em>can</em> be not enough for shading,
especially if you want specular (low precision &amp; quantization leads to specular "wobble" when camera or objects move).
However, everything below should <em>Just Work (tm)</em> for 10 or 16 bits/channel integer formats. For 16 bits/channel
half-float formats, some of the computations are not necessary (e.g. bringing normal values into 0..1 range).
</p>

<p>
If you know other ways to store/encode normals, please let me know in the comments!
</p>

<p>
Various normal encoding methods and their comparison below. Notes:
<ul>
<li>Error images are: <tt>1-pow(dot(n1,n2),1024)</tt> and <tt>abs(n1-n2)*30</tt></tt>, where
<tt>n1</tt> is actual normal, and <tt>n2</tt> is normal encoded into a texture, read back &amp; decoded. MSE and PSNR
is computed on the difference (<tt>abs(n1-n2)</tt>) image.</li>
<li>Shader code is HLSL. Compiled into ps_3_0 by d3dx9_42.dll (February 2010 SDK).</li>
<li>Radeon GPU performance numbers from AMD's GPU ShaderAnalyzer 1.53, using Catalyst 9.12 driver.</li>
<li>GeForce GPU performance numbers from NVIDIA's NVShaderPerf 2.0, using 174.74 driver.</li>
</ul>
</p>

<h4>Note: there was an error!</h4>
<p>
<a href="CompactNormalStorageOldWrong.html">Original version</a> of my article had <em>some stupidity</em>: encoding shaders did not normalize
the incoming per-vertex normal. This resulted in quality evaluation results being <em>somewhat wrong</em>.
Also, if normal is assumed to be normalized, then three methods in original article
(Sphere Map, Cry Engine 3 and Lambert Azimuthal) are in fact <em>completely equivalent</em>.
The old version is <a href="CompactNormalStorageOldWrong.html">still available</a> for the sake of 
integrity of the internets.
</p>

<h4>Test Playground Application</h4>
<p>
Here is a small Windows application I used to test everything below:
<a href="files/NormalEncodingPlayground.zip">NormalEncodingPlayground.zip</a> (4.8MB, source
included).
</p>
<p>
It requires GPU with Shader Model 3.0 support. When it writes fancy shader reports, it expects
AMD's GPUShaderAnalyzer and NVIDIA's NVShaderPerf to be installed. Source code should build with
Visual C++ 2008.
</p>


<a name="method00xyz"></a>
<h3>Baseline: store X&amp;Y&amp;Z</h3>
<p>
Just to set the basis, store all three components of the normal. It's not suitable for our quest,
but I include it here to evaluate "base" encoding error (which happens here only because of
quantization to 8 bits per component).
</p>

<p>
Encoding, Error to Power, Error * 30 images below. MSE: 0.000008; PSNR: 51.081 dB.<br/>
<a href='img/normals2/Normals00xyz.png'><img src='img/normals2/tn-Normals00xyz.png'></a>
<a href='img/normals2/Normals00xyz-pow.png'><img src='img/normals2/tn-Normals00xyz-pow.png'></a>
<a href='img/normals2/Normals00xyz-error30.png'><img src='img/normals2/tn-Normals00xyz-error30.png'></a>
</p>

<table border='0'>
<tr><th>Encoding</th><th>Decoding</th></tr>
<tr><td><pre style="font-size: 60%">
half4 encode (half3 n, float3 view)
{
    return half4(n.xyz*0.5+0.5,0);
}
</pre></td>
<td><pre style="font-size: 60%">
half3 decode (half4 enc, float3 view)
{
    return enc.xyz*2-1;
}
</pre></td></tr>
<tr><td><pre style="font-size: 60%">
ps_3_0
def c0, 0.5, 0, 0, 0
dcl_texcoord_pp v0.xyz
mad_pp oC0, v0.xyzx, c0.xxxy, c0.xxxy</pre></td>
<td><pre style="font-size: 60%">
ps_3_0
def c0, 2, -1, 0, 0
dcl_texcoord2 v0.xy
dcl_2d s0
texld_pp r0, v0, s0
mad_pp oC0.xyz, r0, c0.x, c0.y
mov_pp oC0.w, c0.z</pre></td></tr>
<tr><td><pre style="font-size: 60%">
1 ALU
Radeon HD 2400: 1 GPR, 1.00 clk
Radeon HD 3870: 1 GPR, 1.00 clk
Radeon HD 5870: 1 GPR, 0.50 clk
GeForce 6200: 1 GPR, 1.00 clk
GeForce 7800GT: 1 GPR, 1.00 clk
GeForce 8800GTX: 6 GPR, 8.00 clk
</pre></td>
<td><pre style="font-size: 60%">
2 ALU, 1 TEX
Radeon HD 2400: 1 GPR, 1.00 clk
Radeon HD 3870: 1 GPR, 1.00 clk
Radeon HD 5870: 1 GPR, 0.50 clk
GeForce 6200: 1 GPR, 1.00 clk
GeForce 7800GT: 1 GPR, 1.00 clk
GeForce 8800GTX: 6 GPR, 10.00 clk
</pre></td></tr>
</table>


<a name='method01xy'></a>
<h3>Method #1: store X&amp;Y, reconstruct Z</h3>
<p>
Used by Killzone 2 among others (<a href="http://www.guerrilla-games.com/publications/dr_kz2_rsx_dev07.pdf">PDF link</a>).
</p>

<p>
Encoding, Error to Power, Error * 30 images below. MSE: 0.013514; PSNR: 18.692 dB.<br/>
<a href='img/normals2/Normals01xy.png'><img src='img/normals2/tn-Normals01xy.png'></a>
<a href='img/normals2/Normals01xy-pow.png'><img src='img/normals2/tn-Normals01xy-pow.png'></a>
<a href='img/normals2/Normals01xy-error30.png'><img src='img/normals2/tn-Normals01xy-error30.png'></a>
</p>


<table border="0"><tr><td>
Pros:
<ul>
<li>Very simple to encode/decode</li>
</ul>
</td><td>
Cons:
<ul>
<li>Normal <em>can</em> point away from the camera. My test scene setup actually has that. See
Resistance 2 Prelighting paper (<a href="http://www.insomniacgames.com/tech/articles/0409/files/GDC09_Lee_Prelighting.pdf">PDF link</a>) for explanation.</li>
</ul>
</td></tr></table>


<table border='0'>
<tr><th>Encoding</th><th>Decoding</th></tr>
<tr><td><pre style="font-size: 60%">
half4 encode (half3 n, float3 view)
{
    return half4(n.xy*0.5+0.5,0,0);
}
</pre></td>
<td><pre style="font-size: 60%">
half3 decode (half2 enc, float3 view)
{
    half3 n;
    n.xy = enc*2-1;
    n.z = sqrt(1-dot(n.xy, n.xy));
    return n;
}
</pre></td></tr>
<tr><td><pre style="font-size: 60%">
ps_3_0
def c0, 0.5, 0, 0, 0
dcl_texcoord_pp v0.xy
mad_pp oC0, v0.xyxx, c0.xxyy, c0.xxyy</pre></td>
<td><pre style="font-size: 60%">
ps_3_0
def c0, 2, -1, 1, 0
dcl_texcoord2 v0.xy
dcl_2d s0
texld_pp r0, v0, s0
mad_pp r0.xy, r0, c0.x, c0.y
dp2add_pp r0.z, r0, -r0, c0.z
mov_pp oC0.xy, r0
rsq_pp r0.x, r0.z
rcp_pp oC0.z, r0.x
mov_pp oC0.w, c0.w</pre></td></tr>
<tr><td><pre style="font-size: 60%">
1 ALU
Radeon HD 2400: 1 GPR, 1.00 clk
Radeon HD 3870: 1 GPR, 1.00 clk
Radeon HD 5870: 1 GPR, 0.50 clk
GeForce 6200: 1 GPR, 1.00 clk
GeForce 7800GT: 1 GPR, 1.00 clk
GeForce 8800GTX: 5 GPR, 7.00 clk
</pre></td>
<td><pre style="font-size: 60%">
7 ALU, 1 TEX
Radeon HD 2400: 1 GPR, 1.00 clk
Radeon HD 3870: 1 GPR, 1.00 clk
Radeon HD 5870: 1 GPR, 0.50 clk
GeForce 6200: 1 GPR, 4.00 clk
GeForce 7800GT: 1 GPR, 3.00 clk
GeForce 8800GTX: 5 GPR, 15.00 clk
</pre></td></tr>
</table>




<a name='method03spherical'></a>
<h3>Method #3: Spherical Coordinates</h3>

<p>It is possible to use spherical coordinates to encode the normal. Since we know it's unit
length, we can just store the two angles.</p>
<p>Suggested by Pat Wilson of Garage Games: <a href="http://www.garagegames.com/community/blogs/view/15340">GG blog post</a>.
Other mentions: <a href="http://mynameismjp.wordpress.com/2009/06/17/storing-normals-using-spherical-coordinates/">MJP's blog</a>,
<a href="http://www.garagegames.com/community/forums/viewthread/78938/1#comment-555096">GarageGames thread</a>,
<a href="http://diaryofagraphicsprogrammer.blogspot.com/2008/09/calculating-screen-space-texture.html?showComment=1221085980000#c3416027416379202146">Wolf Engel's blog</a>,
<a href="http://www.gamedev.net/community/forums/topic.asp?topic_id=514536&whichpage=1&#3349819">gamedev.net forum thread</a>.
</p>

<p>
Encoding, Error to Power, Error * 30 images below. MSE: 0.000062; PSNR: 42.042 dB.<br/>
<a href='img/normals2/Normals03spherical.png'><img src='img/normals2/tn-Normals03spherical.png'></a>
<a href='img/normals2/Normals03spherical-pow.png'><img src='img/normals2/tn-Normals03spherical-pow.png'></a>
<a href='img/normals2/Normals03spherical-error30.png'><img src='img/normals2/tn-Normals03spherical-error30.png'></a>
</p>


<table border="0"><tr><td>
Pros:
<ul>
<li>Suitable for normals in general (not necessarily view space)</li>
</ul>
</td><td>
Cons:
<ul>
<li>Uses trig instructions (quite heavy on ALU). Possible to replace some of that with texture lookups though.</li>
</ul>
</td></tr></table>


<table border='0'>
<tr><th>Encoding</th><th>Decoding</th></tr>
<tr><td><pre style="font-size: 60%">
#define kPI 3.1415926536f
half4 encode (half3 n, float3 view)
{
    return half4(
      (half2(atan2(n.y,n.x)/kPI, n.z)+1.0)*0.5,
      0,0);
}
</pre></td>
<td><pre style="font-size: 60%">
half3 decode (half2 enc, float3 view)
{
    half2 ang = enc*2-1;
    half2 scth;
    sincos(ang.x * kPI, scth.x, scth.y);
    half2 scphi = half2(sqrt(1.0 - ang.y*ang.y), ang.y);
    return half3(scth.y*scphi.x, scth.x*scphi.x, scphi.y);
}
</pre></td></tr>
<tr><td><pre style="font-size: 60%">
ps_3_0
def c0, 0.999866009, 0, 1, 3.14159274
def c1, 0.0208350997, -0.0851330012,
    0.180141002, -0.330299497
def c2, -2, 1.57079637, 0.318309873, 0.5
dcl_texcoord_pp v0.xyz
add_pp r0.xy, -v0_abs, v0_abs.yxzw
cmp_pp r0.xz, r0.x, v0_abs.xyyw, v0_abs.yyxw
cmp_pp r0.y, r0.y, c0.y, c0.z
rcp_pp r0.z, r0.z
mul_pp r0.x, r0.x, r0.z
mul_pp r0.z, r0.x, r0.x
mad_pp r0.w, r0.z, c1.x, c1.y
mad_pp r0.w, r0.z, r0.w, c1.z
mad_pp r0.w, r0.z, r0.w, c1.w
mad_pp r0.z, r0.z, r0.w, c0.x
mul_pp r0.x, r0.x, r0.z
mad_pp r0.z, r0.x, c2.x, c2.y
mad_pp r0.x, r0.z, r0.y, r0.x
cmp_pp r0.y, v0.x, -c0.y, -c0.w
add_pp r0.x, r0.x, r0.y
add_pp r0.y, r0.x, r0.x
add_pp r0.z, -v0.x, v0.y
cmp_pp r0.zw, r0.z, v0.xyxy, v0.xyyx
cmp_pp r0.zw, r0, c0.xyyz, c0.xyzy
mul_pp r0.z, r0.w, r0.z
mad_pp r0.x, r0.z, -r0.y, r0.x
mul_pp r0.x, r0.x, c2.z
mov_pp r0.y, v0.z
add_pp r0.xy, r0, c0.z
mul_pp oC0.xy, r0, c2.w
mov_pp oC0.zw, c0.y</pre></td>
<td><pre style="font-size: 60%">
ps_3_0
def c0, 2, -1, 0.5, 1
def c1, 6.28318548, -3.14159274, 1, 0
dcl_texcoord2 v0.xy
dcl_2d s0
texld_pp r0, v0, s0
mad_pp r0.xy, r0, c0.x, c0.y
mad r0.x, r0.x, c0.z, c0.z
frc r0.x, r0.x
mad r0.x, r0.x, c1.x, c1.y
sincos_pp r1.xy, r0.x
mad_pp r0.x, r0.y, -r0.y, c0.w
mul_pp oC0.zw, r0.y, c1
rsq_pp r0.x, r0.x
rcp_pp r0.x, r0.x
mul_pp oC0.xy, r1, r0.x</pre></td></tr>
<tr><td><pre style="font-size: 60%">
26 ALU
Radeon HD 2400: 1 GPR, 17.00 clk
Radeon HD 3870: 1 GPR, 4.25 clk
Radeon HD 5870: 2 GPR, 0.95 clk
GeForce 6200: 2 GPR, 12.00 clk
GeForce 7800GT: 2 GPR, 9.00 clk
GeForce 8800GTX: 9 GPR, 43.00 clk
</pre></td>
<td><pre style="font-size: 60%">
17 ALU, 1 TEX
Radeon HD 2400: 1 GPR, 17.00 clk
Radeon HD 3870: 1 GPR, 4.25 clk
Radeon HD 5870: 2 GPR, 0.95 clk
GeForce 6200: 2 GPR, 7.00 clk
GeForce 7800GT: 1 GPR, 5.00 clk
GeForce 8800GTX: 6 GPR, 23.00 clk
</pre></td></tr>
</table>



<a name='method04spheremap'></a>
<h3>Method #4: Spheremap Transform</h3>

<p>Spherical environment mapping (indirectly) maps reflection vector to a texture coordinate
in [0..1] range. The reflection vector can point away from the camera, just like our view space
normals. Bingo! See <a href="http://www.opengl.org/resources/code/samples/sig99/advanced99/notes/node177.html">Siggraph 99 notes</a>
for sphere map math. Normal we want to encode is R, resulting values are (s,t).
</p>

<p>
If we assume that incoming normal is normalized, then there are methods derived from elsewhere
that end up being <em>exactly equivalent</em>:
<ul>
<li>Used in Cry Engine 3, presented by Martin Mittring in "A bit more Deferred" presentation
    (<a href="http://www.crytek.com/sites/default/files/A_bit_more_deferred_-_CryEngine3.ppt">PPT link</a>,
    slide 13). For Unity, I had to negate Z component of view space normal to produce good results,
    I guess Unity's and Cry Engine's coordinate systems are different. The code would be:
<pre style="font-size: 60%">
half2 encode (half3 n, float3 view)
{
    half2 enc = normalize(n.xy) * (sqrt(-n.z*0.5+0.5));
    enc = enc*0.5+0.5;
    return enc;
}
half3 decode (half4 enc, float3 view)
{
    half4 nn = enc*half4(2,2,0,0) + half4(-1,-1,1,-1);
    half l = dot(nn.xyz,-nn.xyw);
    nn.z = l;
    nn.xy *= sqrt(l);
    return nn.xyz * 2 + half3(0,0,-1);
}
</pre></li>
<li>Lambert Azimuthal Equal-Area projection
    (<a href="http://en.wikipedia.org/wiki/Lambert_azimuthal_equal-area_projection">Wikipedia link</a>).
    Suggested by Sean Barrett in <a href="/blog/2009/08/04/compact-normal-storage-for-small-g-buffers/#comment-20223">comments</a> for this article. The code would be:
<pre style="font-size: 60%">
half2 encode (half3 n, float3 view)
{
    half f = sqrt(8*n.z+8);
    return n.xy / f + 0.5;
}
half3 decode (half4 enc, float3 view)
{
    half2 fenc = enc*4-2;
    half f = dot(fenc,fenc);
    half g = sqrt(1-f/4);
    half3 n;
    n.xy = fenc*g;
    n.z = 1-f/2;
    return n;
}
</pre></li>
</ul>
</p>

<p>
Encoding, Error to Power, Error * 30 images below. MSE: 0.000016; PSNR: 48.071 dB.<br/>
<a href='img/normals2/Normals04spheremap.png'><img src='img/normals2/tn-Normals04spheremap.png'></a>
<a href='img/normals2/Normals04spheremap-pow.png'><img src='img/normals2/tn-Normals04spheremap-pow.png'></a>
<a href='img/normals2/Normals04spheremap-error30.png'><img src='img/normals2/tn-Normals04spheremap-error30.png'></a>
</p>


<table border="0"><tr><td>
Pros:
<ul>
<li>Quality pretty good!</li>
<li>Quite cheap to encode/decode.</li>
<li>Similar derivation used by Cry Engine 3, so it must be good :)</li>
</ul>
</td><td>
Cons:
<ul>
<li>???</li>
</ul>
</td></tr></table>


<table border='0'>
<tr><th>Encoding</th><th>Decoding</th></tr>
<tr><td><pre style="font-size: 60%">
half4 encode (half3 n, float3 view)
{
    half p = sqrt(n.z*8+8);
    return half4(n.xy/p + 0.5,0,0);
}
</pre></td>
<td><pre style="font-size: 60%">
half3 decode (half2 enc, float3 view)
{
    half2 fenc = enc*4-2;
    half f = dot(fenc,fenc);
    half g = sqrt(1-f/4);
    half3 n;
    n.xy = fenc*g;
    n.z = 1-f/2;
    return n;
}
</pre></td></tr>
<tr><td><pre style="font-size: 60%">
ps_3_0
def c0, 8, 0.5, 0, 0
dcl_texcoord_pp v0.xyz
mad_pp r0.x, v0.z, c0.x, c0.x
rsq_pp r0.x, r0.x
mad_pp oC0.xy, v0, r0.x, c0.y
mov_pp oC0.zw, c0.z</pre></td>
<td><pre style="font-size: 60%">
ps_3_0
def c0, 4, -2, 0, 1
def c1, 0.25, 0.5, 1, 0
dcl_texcoord2 v0.xy
dcl_2d s0
texld_pp r0, v0, s0
mad_pp r0.xy, r0, c0.x, c0.y
dp2add_pp r0.z, r0, r0, c0.z
mad_pp r0.zw, r0.z, -c1.xyxy, c1.z
rsq_pp r0.z, r0.z
mul_pp oC0.zw, r0.w, c0.xywz
rcp_pp r0.z, r0.z
mul_pp oC0.xy, r0, r0.z</pre></td></tr>
<tr><td><pre style="font-size: 60%">
4 ALU
Radeon HD 2400: 2 GPR, 3.00 clk
Radeon HD 3870: 2 GPR, 1.00 clk
Radeon HD 5870: 2 GPR, 0.50 clk
GeForce 6200: 1 GPR, 4.00 clk
GeForce 7800GT: 1 GPR, 2.00 clk
GeForce 8800GTX: 5 GPR, 12.00 clk
</pre></td>
<td><pre style="font-size: 60%">
8 ALU, 1 TEX
Radeon HD 2400: 2 GPR, 3.00 clk
Radeon HD 3870: 2 GPR, 1.00 clk
Radeon HD 5870: 2 GPR, 0.50 clk
GeForce 6200: 1 GPR, 6.00 clk
GeForce 7800GT: 1 GPR, 3.00 clk
GeForce 8800GTX: 6 GPR, 15.00 clk
</pre></td></tr>
</table>



<a name='method07stereo'></a>
<h3>Method #7: Stereographic Projection</h3>

<p>What the title says: use Stereographic Projection
(<a href="http://en.wikipedia.org/wiki/Stereographic_projection">Wikipedia link</a>), plus rescaling
so that "practically visible" range of normals maps into unit circle (regular stereographic projection
maps sphere to circle of infinite size). In my tests, scaling factor of 1.7777 produced best results;
in practice it depends on FOV used and how much do you care about normals that point away from the camera.</p>

<p>Suggested by
Sean Barrett and Ignacio Castano in <a href="/blog/2009/08/04/compact-normal-storage-for-small-g-buffers/#comment-20312">comments</a> for this article.</p>

<p>
Encoding, Error to Power, Error * 30 images below. MSE: 0.000038; PSNR: 44.147 dB.<br/>
<a href='img/normals2/Normals07stereo.png'><img src='img/normals2/tn-Normals07stereo.png'></a>
<a href='img/normals2/Normals07stereo-pow.png'><img src='img/normals2/tn-Normals07stereo-pow.png'></a>
<a href='img/normals2/Normals07stereo-error30.png'><img src='img/normals2/tn-Normals07stereo-error30.png'></a>
</p>


<table border="0"><tr><td>
Pros:
<ul>
<li>Quality pretty good!</li>
<li>Quite cheap to encode/decode.</li>
</ul>
</td><td>
Cons:
<ul>
<li>???</li>
</ul>
</td></tr></table>


<table border='0'>
<tr><th>Encoding</th><th>Decoding</th></tr>
<tr><td><pre style="font-size: 60%">
half4 encode (half3 n, float3 view)
{
    half scale = 1.7777;
    half2 enc = n.xy / (n.z+1);
    enc /= scale;
    enc = enc*0.5+0.5;
    return half4(enc,0,0);
}
</pre></td>
<td><pre style="font-size: 60%">
half3 decode (half4 enc, float3 view)
{
    half scale = 1.7777;
    half3 nn =
        enc.xyz*half3(2*scale,2*scale,0) +
        half3(-scale,-scale,1);
    half g = 2.0 / dot(nn.xyz,nn.xyz);
    half3 n;
    n.xy = g*nn.xy;
    n.z = g-1;
    return n;
}
</pre></td></tr>
<tr><td><pre style="font-size: 60%">
ps_3_0
def c0, 1, 0.281262308, 0.5, 0
dcl_texcoord_pp v0.xyz
add_pp r0.x, c0.x, v0.z
rcp r0.x, r0.x
mul_pp r0.xy, r0.x, v0
mad_pp oC0.xy, r0, c0.y, c0.z
mov_pp oC0.zw, c0.w</pre></td>
<td><pre style="font-size: 60%">
ps_3_0
def c0, 3.55539989, 0, -1.77769995, 1
def c1, 2, -1, 0, 0
dcl_texcoord2 v0.xy
dcl_2d s0
texld_pp r0, v0, s0
mad_pp r0.xyz, r0, c0.xxyw, c0.zzww
dp3_pp r0.z, r0, r0
rcp r0.z, r0.z
add_pp r0.w, r0.z, r0.z
mad_pp oC0.z, r0.z, c1.x, c1.y
mul_pp oC0.xy, r0, r0.w
mov_pp oC0.w, c0.y</pre></td></tr>
<tr><td><pre style="font-size: 60%">
5 ALU
Radeon HD 2400: 2 GPR, 4.00 clk
Radeon HD 3870: 2 GPR, 1.00 clk
Radeon HD 5870: 2 GPR, 0.50 clk
GeForce 6200: 1 GPR, 2.00 clk
GeForce 7800GT: 1 GPR, 2.00 clk
GeForce 8800GTX: 5 GPR, 12.00 clk
</pre></td>
<td><pre style="font-size: 60%">
7 ALU, 1 TEX
Radeon HD 2400: 2 GPR, 4.00 clk
Radeon HD 3870: 2 GPR, 1.00 clk
Radeon HD 5870: 2 GPR, 0.50 clk
GeForce 6200: 1 GPR, 4.00 clk
GeForce 7800GT: 1 GPR, 4.00 clk
GeForce 8800GTX: 6 GPR, 12.00 clk
</pre></td></tr>
</table>


<a name='method08ppview'></a>
<h3>Method #8: Per-pixel View Space</h3>

<p>If we compute view space per-pixel, then Z component of a normal can never be negative.
Then just store X&amp;Y, and compute Z.</p>

<p>Suggested by Yuriy O'Donnell on <a href="http://twitter.com/kayru/status/10649422204">Twitter</a>.</p>

<p>
Encoding, Error to Power, Error * 30 images below. MSE: 0.000134; PSNR: 38.730 dB.<br/>
<a href='img/normals2/Normals08ppview.png'><img src='img/normals2/tn-Normals08ppview.png'></a>
<a href='img/normals2/Normals08ppview-pow.png'><img src='img/normals2/tn-Normals08ppview-pow.png'></a>
<a href='img/normals2/Normals08ppview-error30.png'><img src='img/normals2/tn-Normals08ppview-error30.png'></a>
</p>


<table border="0"><tr><td>
Pros:
<ul>
<li>???</li>
</ul>
</td><td>
Cons:
<ul>
<li>Quite heavy on ALU</li>
</ul>
</td></tr></table>


<table border='0'>
<tr><th>Encoding</th><th>Decoding</th></tr>
<tr><td colspan='2'><pre style="font-size: 60%">
float3x3 make_view_mat (float3 view)
{
    view = normalize(view);
    float3 x,y,z;
    z = -view;
    x = normalize (float3(z.z, 0, -z.x));
    y = cross (z,x);
    return float3x3 (x,y,z);
}
half4 encode (half3 n, float3 view)
{
    return half4(mul (make_view_mat(view), n).xy*0.5+0.5,0,0);
}
half3 decode (half4 enc, float3 view)
{
    half3 n;
    n.xy = enc*2-1;
    n.z = sqrt(1+dot(n.xy,-n.xy));
    n = mul(n, make_view_mat(view));
    return n;
}
</pre></td></tr>
<tr><td><pre style="font-size: 60%">
ps_3_0
def c0, 1, -1, 0, 0.5
dcl_texcoord_pp v0.xyz
dcl_texcoord1 v1.xyz
mov r0.x, c0.z
nrm r1.xyz, v1
mov r1.w, -r1.z
mul r0.yz, r1.xxzw, c0.xxyw
dp2add r0.w, r1.wxzw, r0.zyzw, c0.z
rsq r0.w, r0.w
mul r0.xyz, r0, r0.w
mul r2.xyz, -r1.zxyw, r0
mad r1.xyz, -r1.yzxw, r0.yzxw, -r2
dp2add r0.x, r0.zyzw, v0.xzzw, c0.z
dp3 r0.y, r1, v0
mad_pp oC0.xy, r0, c0.w, c0.w
mov_pp oC0.zw, c0.z</pre></td>
<td><pre style="font-size: 60%">
ps_3_0
def c0, 2, -1, 1, 0
dcl_texcoord1 v0.xyz
dcl_texcoord2 v1.xy
dcl_2d s0
mov r0.y, c0.w
nrm r1.xyz, v0
mov r1.w, -r1.z
mul r0.xz, r1.zyxw, c0.yyzw
dp2add r0.w, r1.wxzw, r0.xzzw, c0.w
rsq r0.w, r0.w
mul r0.xyz, r0, r0.w
mul r2.xyz, -r1.zxyw, r0.yzxw
mad r2.xyz, -r1.yzxw, r0.zxyw, -r2
texld_pp r3, v1, s0
mad_pp r3.xy, r3, c0.x, c0.y
mul r2.xyz, r2, r3.y
mad r0.xyz, r3.x, r0, r2
dp2add_pp r0.w, r3, -r3, c0.z
rsq_pp r0.w, r0.w
rcp_pp r0.w, r0.w
mad_pp oC0.xyz, r0.w, -r1, r0
mov_pp oC0.w, c0.w</pre></td></tr>
<tr><td><pre style="font-size: 60%">
17 ALU
Radeon HD 2400: 3 GPR, 11.00 clk
Radeon HD 3870: 3 GPR, 2.75 clk
Radeon HD 5870: 2 GPR, 0.80 clk
GeForce 6200: 4 GPR, 12.00 clk
GeForce 7800GT: 4 GPR, 8.00 clk
GeForce 8800GTX: 8 GPR, 24.00 clk
</pre></td>
<td><pre style="font-size: 60%">
21 ALU, 1 TEX
Radeon HD 2400: 3 GPR, 11.00 clk
Radeon HD 3870: 3 GPR, 2.75 clk
Radeon HD 5870: 2 GPR, 0.80 clk
GeForce 6200: 3 GPR, 12.00 clk
GeForce 7800GT: 3 GPR, 9.00 clk
GeForce 8800GTX: 12 GPR, 29.00 clk
</pre></td></tr>
</table>



<a name="perf-comparison"></a>
<h3>Performance Comparison</h3>

<p>
GPU performance comparison in a single table:
<table class="table-cells">
<tr><td></td>               <th>#1: X &amp; Y</th>              <th>#3: Spherical</th>          <th>#4: Spheremap</th>              <th>#7: Stereo</th>                     <th>#8: PPView</th>   </tr>
<tr><th colspan='8'>Encoding, GPU cycles</th></tr>
<tr><td>Radeon HD2400</td>  <td class='number good1'>1.00</td>  <td class='number'>17.00</td>   <td class='number good2'>3.00</td>  <td class='number good3'>4.00</td>       <td class='number      '>11.00</td>               </tr>
<tr><td>Radeon HD5870</td>  <td class='number good1'>0.50</td>  <td class='number'>0.95</td>    <td class='number good1'>0.50</td>  <td class='number good1'>0.50</td>       <td class='number      '>0.80</td>               </tr>
<tr><td>GeForce 6200</td>   <td class='number good1'>1.00</td>  <td class='number'>12.00</td>   <td class='number good3'>4.00</td>  <td class='number good2'>2.00</td>       <td class='number      '>12.00</td>               </tr>
<tr><td>GeForce 8800</td>   <td class='number good1'>7.00</td>  <td class='number'>43.00</td>   <td class='number good2'>12.00</td> <td class='number good2'>12.00</td>      <td class='number      '>24.00</td>               </tr>
<tr><th colspan='8'>Decoding, GPU cycles</th></tr>
<tr><td>Radeon HD2400</td>  <td class='number good1'>1.00</td>  <td class='number'>17.00</td>   <td class='number good2'>3.00</td>  <td class='number good3'>4.00</td>       <td class='number      '>11.00</td>               </tr>
<tr><td>Radeon HD5870</td>  <td class='number good1'>0.50</td>  <td class='number'>0.95</td>    <td class='number good1'>0.50</td>  <td class='number'>1.00</td>             <td class='number good3'>0.80</td>       </tr>
<tr><td>GeForce 6200</td>   <td class='number good1'>4.00</td>  <td class='number'>7.00</td>    <td class='number good3'>6.00</td>  <td class='number good1'>4.00</td>       <td class='number      '>12.00</td>      </tr>
<tr><td>GeForce 8800</td>   <td class='number good3'>15.00</td> <td class='number'>23.00</td>   <td class='number good3'>15.00</td> <td class='number good1'>12.00</td>      <td class='number      '>29.00</td>      </tr>
<tr><th colspan='8'>Encoding, D3D ALU+TEX instruction slots</th></tr>
<tr><td>SM3.0</td>          <td class='number good1'>1</td>     <td class='number'>26</td>      <td class='number good2'>4</td>     <td class='number good3'>5</td>          <td class='number'>17</td>      </tr>
<tr><th colspan='8'>Decoding, D3D ALU+TEX instruction slots</th></tr>
<tr><td>SM3.0</td>          <td class='number good1'>8</td>     <td class='number'>18</td>      <td class='number good3'>9</td>     <td class='number good1'>8</td>          <td class='number'>22</td>      </tr>
</table>
</p>

<a name="q-comparison"></a>
<h3>Quality Comparison</h3>

<p>
Quality comparison in a single table. PSNR based, higher numbers are better.
<table class="table-cells">
<tr><th>Method</th><th>PSNR, dB</th></tr>
<tr><td>#1: X &amp; Y</td>      <td class='number      '>18.629</td></tr>
<tr><td>#3: Spherical</td>      <td class='number good3'>42.042</td></tr>
<tr><td>#4: Spheremap</td>      <td class='number good1'>48.071</td></tr>
<tr><td>#7: Stereographic</td>  <td class='number good2'>44.147</td></tr>
<tr><td>#8: Per pixel view</td> <td class='number      '>38.730</td></tr>
</table>
</p>


<a name="changelog"></a>
<h3>Changelog</h3>
<ul>
    <li>2021 08 17: Added a note about octahedral encodings at the top of the post.</li>
    <li>2010 03 25: Added Method #8: Per-pixel View Space. Suggested by <a href="http://twitter.com/kayru">Yuriy O'Donnell</a>.</li>
    <li>2010 03 24: <em>Stop! Everything before was wrong! Old article <a href="CompactNormalStorageOldWrong.html">moved here</a>.</em></li>
    <li>2009 08 12: Added Method #7: Stereographic projection. Suggested by <a href="http://www.nothings.org/">Sean Barrett</a> and <a href="http://castano.ludicon.com/blog/">Ignacio Castano</a>.</li>
    <li>2009 08 12: Optimized Method #5, suggested by Steve Hill.</li>
    <li>2009 08 08: Added power difference images.</li>
    <li>2009 08 07: Optimized Method #4: Sphere map. Suggested by Irenee Caroulle.</li>
    <li>2009 08 07: Added Method #6: Lambert Azimuthal Equal Area. Suggested by <a href="http://www.nothings.org/">Sean Barrett</a>.</li>
    <li>2009 08 05: Added Method #5: Cry Engine 3. Suggested by Steve Hill.</li>
    <li>2009 08 05: Improved quality of Method #3a: round values in texture LUT.</li>
    <li>2009 08 05: Added MSE and PSNR values for all methods.</li>
    <li>2009 08 04: Added Method #3a: Spherical Coordinates w/ texture LUT.</li>
    <li>2009 08 04: Method #1: <tt>1-dot(n.xy,n.xy)</tt> is slightly better than <tt>1-n.x*n.x-n.y*n.y</tt> (better pipelining on NV and ATI). Suggested by <a href="http://zeuxcg.blogspot.com/">Arseny "zeux" Kapoulkine</a>.</li>
</ul>
