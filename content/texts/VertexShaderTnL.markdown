---
layout: page
title: Implementing fixed function T&L in vertex shaders
comments: true
sharing: true
footer: true
menusection: texts
url: /texts/VertexShaderTnL.html
---

<ul style="font-size: 80%">
<li><a href="#why">Why?</a></li>
<li><a href="#constraints">Constraints and Targets</a></li>
<li><a href="#previous">Previous Work</a></li>
<li><a href="#approach">Approach</a></li>
<li><a href="#highlevel">High Level Overview</a></li>
<li><a href="#generator">Shader Generator</a></li>
<li><a href="#regalloc">Register Allocation</a></li>
<li><a href="#pipeconfig">Vertex Pipe Configuration</a></li>
<li><a href="#pipedata">Vertex Pipe Data</a></li>
<li><a href="#fragments">Shader Fragments</a></li>
<li><a href="#results">Results</a></li>
<li><a href="#conclusions">Conclusions and Future Work</a></li>
<li><a href="#appendixA">Appendix A</a></li>
</ul>


<a name="why"></a>
<h3>Why?</h3>

In <a href="http://unity3d.com">Unity</a> we often need to render some objects in multiple passes, where some passes use fixed
function T&L (Transform and Lighting), and some use vertex shaders. For example, when rendering
first couple lights using per-pixel lighting and the remaining ones in one base pass using standard
T&L. The problem is - precision of vertex transformations is not guaranteed to match when using
fixed function and vertex shaders, leading to z fighting artifacts.

Here's an example (click for larger version):<br/>
<a href="img/tnl/Image-zfight.png"><img src="img/tnl/tn-Image-zfight.jpg"></a><br/>
The model here uses fixed function for ambient rendering pass, and shaders for per-pixel lighting
pass.

What we used to do in Unity to "solve" this: we would "pull" the later rendering passes slightly
towards the camera, so there would be no z fighting (technically it's done using depth bias):<br/>
<img src="img/tnl/Diagram.png"><br/>
However, this creates "double lighting" artifacts on very close or self-intersecting surfaces:<br/>
<a href="img/tnl/Image-doublelight.png"><img src="img/tnl/tn-Image-doublelight.jpg"></a><br/>
In most cases this is not a serious problem or can be worked around, but some folks can't afford that.
For example, one customer was creating a medical application with models of bones, tissues and whatnot;
it is <em>full</em> of surfaces that are very close to each other. Here's a picture again to emphasize
where the approach fails:<br/>
<a href="img/tnl/Image-fail.png"><img src="img/tnl/tn-Image-fail.jpg"></a><br/>

Even more, depth bias did not solve the Z fighting problem in some cases. For example, very large
clipped polygons would still produce horrible Z fighting on Radeon 9500-9800 cards, no matter what
was the depth bias (I didn't ever figure out why; I just blamed it on "something funky" in the polygon
clipper on that hardware).

So we decided to solve this issue in Unity. This required re-implementing whole fixed function
T&L pipeline using vertex shaders.


<a name="constraints"></a>
<h3>Constraints and Targets</h3>

<ul>
<li>Users can create completely arbitrary fixed function setups - we need to implement everything that is possible in fixed function.</li>
<li>We can't determine the set of T&L configurations needed offline (because of perhaps too much flexibility on Unity side...).</li>
<li>Can't use HLSL or other high level compilers at runtime (the compiler has to be shipped with the engine, so that would increase engine size by 2MB or so).</li>
<li>Target D3D9, vertex shader 2.0 and up. Older configurations don't use per-pixel lighting very often.</li>
</ul>


<a name="previous"></a>
<h3>Previous work</h3>

In no particular order:
<ul>
<li><a href="http://developer.nvidia.com/object/nvlink_2_1.html">NVLink</a> by NVIDIA - precompiled binary only, about 300 kilobytes in size, sample code did not work, probably targeted D3D8. Not much use.</li>
<li><a href="http://developer.amd.com/samples/gpusamples/FixedFuncShader/Pages/default.aspx">FixedFuncShader</a> by ATI - a decent starting point. Does not implement
everything (leaves out routing texture coordinate inputs to texture interpolators, texture matrices etc.). Single ubershader approach with constant based branching. Code could be optimized more.</li>
<li><a href="http://zeuxcg.blogspot.com/2007/10/my-own-lighting-shader-with-blackjack.html">My own lighting shader with blackjack and hookers</a> by Arseny Kapoulkine - not directly related, but a useful lighting
shader optimization article.</li>
</ul>


<a name="approach"></a>
<h3>Approach</h3>

I started by extending FixedFuncShader to handle the remaining functionality, and quickly found out
that supporting all modes of texture routing and transformations would result in insane number
of static branches (there are up to 8 output texture interpolators, where each is either passed directly
or transformed with matrices of various dimensions, and the source is one of input texture coordinates
or one of texture generation modes - a huge number of possible combinations here). So ubershader
approach had to go.

As using a high level shading language was not an option either (see <a href="#constraints">constraints</a>), the only
choice was <strong>combining shaders</strong> from assembly or shader bytecode fragments.


<a name="highlevel"></a>
<h3>High Level Overview</h3>

From the engine side, all vertex transformation related data is separated into two pieces:
<ol>
<li>Configuration - <tt>VertexPipeConfig</tt>. Different configurations cause different
vertex shaders to be generated to handle this configuration. For example, lighting being on or off
goes into configuration.</li>
<li>Data - <tt>VertexPipeData</tt>. Data goes into vertex shader constants. For example,
world*view*projection matrix, light colors etc.</li>
</ol>
So once the configuration and data is known, the engine does roughly this (pseudocode):

```c
vs = GetShaderForConfig (config); // caches created shaders for each config
SetVertexShader (vs);
SetupShaderData (data);
// can render now!
```

This means that all vertex pipeline related state has to be gathered into "config" and
"data" structures, because complete "config" needs to be known to look up proper shader.


<a name="generator"></a>
<h3>Shader Generator</h3>

Shaders are generated from assembly fragments. The fragments use "symbolic" register names for
temporary registers, and later on the shader generator assigns actual register names. Whole
shader is just a bunch of fragments thrown together, and temporary register allocation is the only
processing done on them. Possible redundant computations are managed manually - any computation
that is used by more than one fragment is split into a separate fragment.

So each shader fragment:
<ul>
<li>Is an assembly snippet with symbolic register names.</li>
<li>Can require some values computed by previous fragments.</li>
<li>Can compute compute some values for next fragments.</li>
<li>Can use some temporary registers internally.</li>
<li>Can require some vertex inputs (position, normal, ...).</li>
<li>Can depend on some other shader fragments being present (e.g. camera space reflection vector depends on camera space normal and view vector).</li>
</ul>

Now, given <tt>VertexPipeConfig</tt>, I just throw the needed fragments and
say "generate a shader for this". When fragments are added for shader generation, they are
only added once. So if both environment mapping texgen and specular lighting needs
"view vector" fragment, it will only be added once. This takes care of the possible redundant
calculations.


<a name="regalloc"></a>
<h3>Register Allocation</h3>

A value produced by some fragment has to be put into some temporary register. From that point on,
this register has to be preserved until the last fragment that uses this value as input. Temporary
registers needed internally by shader fragments are allocated from the unused registers. This is the
basis of my register allocation scheme.

Here's an example. It's a shader that:
<ol>
    <li>Transforms vertex position (<tt>Position</tt> fragment).</li>
    <li>Computes lighting from directional lights, without specular. This initializes diffuse
        to black (<tt>DiffuseInit</tt>), accumulates lighting for each light (<tt>DiffuseDir</tt>)
        and outputs to the color interpolator (<tt>DiffuseOutput</tt>). Lighting uses <tt>DIFF</tt>
        as a symbolic value name. As lighting needs camera space normal, that fragment is
        automatically inserted as well (which uses <tt>CNOR</tt> name for it's value).</li>
    <li>Outputs camera space reflection vector as the first texture coordinate (perhaps for
        cube environment mapping). This value, <tt>REFL</tt>, is computed by <tt>CamSpaceRefl</tt>
        fragment and output by <tt>Tex0Out</tt> fragment. Reflection depends on
        <tt>ViewVector</tt> fragment, which in turn depends on <tt>CamSpacePos</tt> fragment, so
        those fragments are inserted automatically. Reflection also depends on camera
        space normal fragment, but that was already inserted by lighting fragment.</li>
</ol>

In a table below I color coded symbolic name lifetimes. Each row is the actual register that
was assigned. Temporary registers used internally by fragment (in this case, only <tt>DiffuseDir</tt>
fragment needs one temporary register) get assigned from the free ones during that fragment.

<table class="table-cells" style="font-size: 70%">
<tr>
    <th></th>
    <th>Position</th>
    <th>CamSpaceNormal</th>
    <th>DiffuseInit</th>
    <th>DiffuseDir</th>
    <th>DiffuseOutput</th>
    <th>CamSpacePos</th>
    <th>ViewVector</th>
    <th>CamSpaceRefl</th>
    <th>Tex0Out</th>
</tr>
<tr>
    <th>r0</th>
    <td></td>
    <td style="background-color:#ffd0d0;">out:CNOR</td>
    <td style="background-color:#ffd0d0;"></td>
    <td style="background-color:#ffd0d0;">in:CNOR</td>
    <td style="background-color:#ffd0d0;"></td>
    <td style="background-color:#ffd0d0;"></td>
    <td style="background-color:#ffd0d0;"></td>
    <td style="background-color:#ffd0d0;">in:CNOR</td>
    <td></td>
</tr>
<tr>
    <th>r1</th>
    <td></td>
    <td></td>
    <td style="background-color:#d0ffd0;">out:DIFF</td>
    <td style="background-color:#d0ffd0;">inout:DIFF</td>
    <td style="background-color:#d0ffd0;">inout:DIFF</td>
    <td></td>
    <td style="background-color:#ffffd0;">out:VIEW</td>
    <td style="background-color:#ffffd0;">in:VIEW</td>
    <td></td>
</tr>
<tr>
    <th>r2</th>
    <td></td>
    <td></td>
    <td></td>
    <td>tmp:TMP0</td>
    <td></td>
    <td style="background-color:#d0d0ff;">out:CPOS</td>
    <td style="background-color:#d0d0ff;">in:CPOS</td>
    <td style="background-color:#ffd0ff;">out:REFL</td>
    <td style="background-color:#ffd0ff;">in:REFL</td>
</tr>
</table>



<a name="pipeconfig"></a>
<h3>Vertex Pipe Configuration</h3>

Vertex pipe configuration is packed into <tt>VertexPipeConfig</tt> structure like mentioned
above. Each configuration results in a new shader generated to handle this configuration.
In my case, packed configuration takes 53 bits; most of that space (44 bits) describing
how to route and generate texture coordinates.

The number of shaders possibly generated is much less than 2<sup>53</sup>, because not all bit
combinations are used. However, the possible combination count is still enormous; in the number of
tens of millions. The good news is that real actual Unity games do not use that many vertex
pipeline configurations, usually in the number of 10 to 100.

Here's my <tt>VertexPipeConfig</tt> structure:

```c
struct VertexPipeConfig {
    // 2 bits for each output texture (none, 2D matrix, 3D matrix)
    UInt64 textureMatrixModes : 16;
    // 3 bits for each output texture (UV0,UV1,SphereMap,ObjectSpace,CameraSpace,CubeReflect,CubeNormal)
    UInt64 textureSources : 24;
    // number of output texture coordinates
    UInt64 texCoordCount : 4;
    // color material mode (none,ambient,diffuse,spec,emission,ambient&diffuse)
    UInt64 colorMaterial : 3;
    UInt64 hasVertexColor : 1;  // does mesh have per-vertex color?
    UInt64 hasLighting : 1;     // lighting on?
    UInt64 hasSpecular : 1;     // specular on?
    UInt64 hasLightType : 3;    // has light of given type? (bit per type)
    // 11 bits left
```

Function <tt>GetShaderForConfig()</tt> is roughly this:

```c
Shader* GetShaderForConfig (VertexPipeConfig config)
{
    if (cache has config)
        return shader from cache for this config;
        
    create shader generator;
    add fragment to transform position;
    add fragments to compute lighting & apply color material mode;
    add fragments to route, generate & transform texture coordinates;
    
    generate shader;
    insert shader into cache for this config;
    return shader;
}
```
See <a href="#appendixA">Appendix A</a> for an almost complete <tt>GetShaderForConfig</tt> implementation.



<a name="pipedata"></a>
<h3>Vertex Pipe Data</h3>

Vertex pipeline data (matrices, light colors, ...) goes into predefined vertex shader constant
registers. The shader fragments explicitly reference them, so no constant register allocation is
needed by shader generator.

Inside the shader, lighting computations are performed in camera space (this would be just like in
OpenGL specification). All light positions & directions are transformed into camera space
on the CPU before setting up shader constants. This saves some matrix multiplications in the shader.

Some common expressions are precomputed on the CPU as well, for example
<tt>MaterialEmissive+Ambient*MaterialAmbient</tt> does not need to be computed for each vertex.

Some parts of T&L are fixed and can't be changed in Unity; for example: constant light
attenuation is always 1.0, linear attenuation is always zero, spotlight falloff is always one,
spotlight Theta is always half of Phi etc. I took all of that into account when writing shader
fragments and packing vertex pipe data. Each vertex light packs into 4 constant registers:

```c
struct VSLightData {
    SimpleVec4 pos;
    SimpleVec4 dir;
    SimpleVec4 color;
    // 1.0/(cos(theta/2)-cos(phi/2), cos(phi/2), range^2, d^2 attenuation
    SimpleVec4 params;
};
```
This still leaves <tt>w</tt> components of first three vectors unused.

The other data as required by vertex shaders:

```c
float4x4 MVP - model*view*projection matrix
float4x4 MV - model*view matrix
float4x4 MV_IT - inverse transpose of model*view matrix
float4x4 Texture - texure matrices, need up to 8 of them
float4 Ambient - ambient color
float4 ColorMatAmbient - some color material modes need second ambient color
float4 Misc - various constants (0, 1, 0.5, 4)
float4 Diffuse - material diffuse color
float4 Specular - material specular color, specular power in w
float4 LightIndexes - light start indices * 4, a component for each light type
int LightCount - light counts; need three of them (directional, point, spot)
```
The above data could be optimized more; for example model*view matrix could only take 3
constant registers and so on.


<a name="fragments"></a>
<h3>Shader Fragments</h3>

Most of shader fragments are very straightforward, just linear code flow; take some
symbolic names as input and produce some symbolic names as output. For example, transforming
vertex position could be:

```c
const ShaderFragment kVS_Pos = {
    (1<<kInputPosition), // vertex input: position
    0, // no dependencies
    0, // no temporary registers
    NULL, // no inputs
    NULL, // no outputs
    // IPOS = position from vertex data
    // c0-c3 = MVP matrix
    "dp4 oPos.x, $IPOS, c0\n"
    "dp4 oPos.y, $IPOS, c1\n"
    "dp4 oPos.z, $IPOS, c2\n"
    "dp4 oPos.w, $IPOS, c3\n"
};
```

A more complex example, fragment to compute camera space reflection vector:

```c
// REFL = camera space reflection vector: 2*dot(V,N)*N-V
const ShaderFragment kVS_Temp_CamSpaceRefl = {
    0, // no vertex input
    (1<<kDep_CamSpaceN) | (1<<kDep_ViewVector), // depends on
    0, // no temporary registers
    "CNOR VIEW", // inputs
    "REFL", // outputs
    "mov $O_REFL.xyz, $O_VIEW\n"
    "dp3 $O_REFL.w, $O_REFL, $O_CNOR\n"
    "add $O_REFL.w, $O_REFL.w, $O_REFL.w\n"
    "mad $O_REFL.xyz, $O_REFL.w, $O_CNOR, -$O_REFL\n"
};
```

This fragment depends on "camera space normal" and "view vector" fragments. Here's camera space normal:

```c
// CNOR = camera space normal of the vertex
const ShaderFragment kVS_Temp_CamSpaceN = {
    (1<<kInputNormal), // vertex input: normal
    0, // no dependencies
    0, // no temporary registers
    NULL, // no inputs
    "CNOR", // outputs
    
    // INOR = normal from vertex data
    // c8-c10 = MV_IT matrix
    "mul $O_CNOR, $INOR.y, c9\n"
    "mad $O_CNOR, c8, $INOR.x, $O_CNOR\n"
    "mad $O_CNOR, c10, $INOR.z, $O_CNOR\n",
};
```
And so on, no rocket science here.</p>

The lighting fragments are a bit different - each fragment loops over the lights of
particular type (using vertex shader 2.0 static loop instructions). Inserting a new fragment for
each light present would run into shader instruction count limits, that's why static looping
was chosen.

There is a fragment for "initialize lighting" (see example in <a href="#regalloc">register
allocation</a>) that initializes lighting accumulator value(s) to zero, then a fragment that
loops over directional lights, a fragment that loops over point lights etc. Which types of
lights are active is part of the <a href="#pipeconfig">VertexPipeConfig</a>
structure, so when there are no spot lights, the spot light fragment does not get added. Light data
is set up into shader constants grouped by light type, so each fragment needs to know the light start
index and count.

Here's fragment that computes directional lights without specular:

```c
const ShaderFragment kVS_Light_Diffuse_Dir = {
    0, // no vertex input
    (1<<kDep_CamSpaceN), // depends on camera space normal
    1, // uses 1 temporary register
    "CNOR", // inputs
    "DIFF", // outputs
    "mov $O_CNOR.w, c48.y\n"                        // CNOR.w is reused as light data index
    "rep i1\n"                                      // i1 = light count
    "  mova a0.x, $O_CNOR.w\n"
    "  dp3 $TMP0.x, $O_CNOR, c61[a0.x]\n"           // NdotL
    "  slt $TMP0.w, c45.x, $TMP0.x\n"               // clamp = NdotL > 0
    "  mul $TMP0.xyz, $TMP0.x, c62[a0.x]\n"         // diff = NdotL * lightColor
    "  mad $O_DIFF.xyz, $TMP0.w, $TMP0, $O_DIFF\n"  // diffuse += diff * clamp
    "  add $O_CNOR.w, $O_CNOR.w, c45.y\n"           // index += 4
    "endrep\n"
};
```

Other light fragments are longer (e.g. spot light with specular), but the idea is the same.



<a name="results"></a>
<h3>Results</h3>

The main result is that <em>it works</em> and the Z fighting / depth bias issues are solved:<br/>
<a href="img/tnl/Image-ok.png"><img src="img/tnl/tn-Image-ok.jpg"></a><br/>
In Unity 2.6, all shaders that use T&L will be transparently using vertex shaders to emulate
it (when using D3D9).

Performance: I tested various scenarios on Radeon HD 3xxx, GeForce 8xxx and Intel GMA 950.
In most cases, performance of vertex shaders is the same as using T&L. In some cases, vertex
shaders are slightly slower (I would expect that, driver developers probably spent way more time
optimizing their T&L implementation than I did).

Time spent to create the shaders distributes like this: 70% inside the driver, 25% in
D3DXAssembleShader, 5% in my shader fragment generator. My generator could be optimized
(right now does too many string operations), but it's only spending 5% there, so not much gain.

Shader generator, shader fragments and engine changes to support this added about 5 kilobytes to
the engine size (after compression).

Implementation took 2-3 weeks in total (hard to measure exactly because I spent
time doing other things as well). Most of the time was spent experimenting and rejecting
alternative approaches, adding more functional tests, more benchmark scenarios, implementing
an application that tests rendering correctness of hundreds of thousands of vertex pipe
configurations, profiling etc.



<a name="conclusions"></a>
<h3>Conclusions and Future Work</h3>

I enjoyed working on this a lot. I'm probably late by 7 years at realizing this, but combining
shader fragments is awesome. The rest of the world has already moved onto high level shader language
compilers, but we can't use them at runtime yet because of size issues.

I'd like to experiment with combining fragments at runtime in order to avoid generating all
possible shader permutations offline in Unity.

The approach of "move redundant computations into separate fragments" and a very simple
register allocation scheme seems to work just fine and shaders that are produced
match the ones produced by HLSL compiler (in assembly instruction/register counts and in
actual hardware cycles/registers as shown by NVShaderPerf or GPUShaderAnalyzer).

Creation of shaders could be sped up by not using shader assembly, but by operating on shader
bytecode level. The fragments would be precompiled bytecode, and register allocation would
directly change the bytecode. This could save 25-30% of the shader creation time (everything in
D3DXAssembleShader).


<a name="appendixA"></a>
<h3>Appendix A - GetShaderForConfig implementation</h3>

Almost directly from the source code (error handling and other bits removed):

```c
Shader* GetShaderForConfig (VertexPipeConfig config)
{
    // have this shader already?
    Shader* shader = g_ShaderCache.find (config);
    if (shader)
        return shader;
        
    // create for this config
    ShaderGenerator gen;
    
    // transform position
    gen.AddFragment( &kVS_Pos );
    
    // lighting
    if (config.hasLighting)
    {
        UInt32 hasLightType = config.hasLightType;
        if (config.hasSpecular)
        {
            // with specular
            gen.AddFragment (&kVS_Light_Specular_Pre);
            if (hasLightType & (1<<kLightDirectional))
                gen.AddFragment (&kVS_Light_Specular_Dir);
            if (hasLightType & (1<<kLightPoint))
                gen.AddFragment (&kVS_Light_Specular_Point);
            if( hasLightType & (1<<kLightSpot))
                gen.AddFragment (&kVS_Light_Specular_Spot);
        }
        else
        {
            // without specular
            gen.AddFragment (&kVS_Light_Diffuse_Pre);
            if( hasLightType & (1<<kLightDirectional) )
                gen.AddFragment (&kVS_Light_Diffuse_Dir);
            if( hasLightType & (1<<kLightPoint) )
                gen.AddFragment (&kVS_Light_Diffuse_Point);
            if( hasLightType & (1<<kLightSpot) )
                gen.AddFragment (&kVS_Light_Diffuse_Spot);
        }

        // color material
        const ShaderFragment* frag = NULL;
        if( config.hasVertexColor )
        {
            switch( config.colorMaterial ) {
            case kColorMatDiffuse:
                frag = &kVS_Out_Diffuse_Lighting_ColorDiffuse; break;
            case kColorMatAmbientAndDiffuse:
                frag = &kVS_Out_Diffuse_Lighting_ColorDiffuseAmbient; break;
            case kColorMatAmbient:
                frag = &kVS_Out_Diffuse_Lighting_ColorAmbient; break;
            case kColorMatEmission:
                frag = &kVS_Out_Diffuse_Lighting_ColorEmission; break;
            default:
                frag = &kVS_Out_Diffuse_Lighting; break;
            }
        } else {
            frag = &kVS_Out_Diffuse_Lighting;
        }
        gen.AddFragment( frag );

        // output specular color
        if( config.hasSpecular ) {
            if( config.colorMaterial == kColorMatSpecular )
                gen.AddFragment (&kVS_Out_Specular_Lighting_ColorSpecular);
            else
                gen.AddFragment (&kVS_Out_Specular_Lighting);
        }

    }
    else
    {
        // no lighting
        if( config.hasVertexColor )
            gen.AddFragment (&kVS_Out_Diffuse_VertexColor);
        else
            gen.AddFragment (&kVS_Out_Diffuse_White);
    }
    
    // route, generate and transform texture coordinates
    static const ShaderFragment* kFragSources[kTexSourceTypeCount] = {
        &kVS_Load_UV0,
        &kVS_Load_UV1,
        &kVS_Temp_SphereMap,
        &kVS_Temp_ObjSpacePos,
        &kVS_Temp_CamSpacePos,
        &kVS_Temp_CamSpaceRefl,
        &kVS_Temp_CamSpaceN,
    };
    static const char* kFragSourceNames[kTexSourceTypeCount] = {
        "UV0",
        "UV1",
        "SPHR",
        "OPOS",
        "CPOS",
        "REFL",
        "CNOR",
    };
    static const ShaderFragment* kFragMatrices[kTexMatrixTypeCount] = {
        &kVS_Out_TexCoord,
        &kVS_Out_Matrix2,
        &kVS_Out_Matrix3
    };
    // load or generate texture coordinates
    for( int i = 0; i < config.texCoordCount; ++i )
    {
        unsigned src = (config.textureSources >> (i*3)) & 7;
        gen.AddFragment( kFragSources[src] );
    }
    // output or transform&output them
    for( int i = 0; i < config.texCoordCount; ++i )
    {
        unsigned src = (config.textureSources >> (i*3)) & 7;
        unsigned matmode = (config.textureMatrixModes >> (i*2)) & 3;
        gen.AddFragment (kFragMatrices[matmode], kFragSourceNames[src], i);
    }
    
    // generate shader!
    shader = gen.GenerateShader (..);
    
    // insert into cache
    g_ShaderCache.insert (config, shader);
    return shader;
}
```

