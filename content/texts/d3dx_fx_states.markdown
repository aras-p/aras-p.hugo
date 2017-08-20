---
title: D3DX Effects state management
comments: true
sharing: true
footer: true
menusection: texts
url: /texts/d3dx_fx_states.html
---

<p>
This article describes how to make D3DX Effects run fast - efficiently manage device states, etc. A more extended article that also talks about renderer organization, feeding effects
with parameters and other aspects are in <a href="http://www.amazon.com/gp/product/1584504250/104-3659308-1299952">ShaderX<sup>4</sup></a> book.
This one just outlines the basic ideas related to performance and describes <em>some improvements</em> (everything starting at "improved solution" section) that didn't make into the book (I thought about them too late).
</p>


<H3>Effects and Performance</H3>
<p>
D3DX effects do lots of work behind the scenes to make programmer's life easier. D3D guys are making serious improvements in reducing Effect system
overhead (optimizing internals), but some things just can't be optimized away. I'm mostly talking about the fact that by default, effects save and restore all
modified D3D device state - this obviously has some overhead (probably mostly in D3D runtime and the driver, not in Effects themselves). This behavior is great
for quick prototyping, but what if we could reduce the state management overhead?
</p>
<p>
One common advice is "don't save/restore state in Effects" (via <code>D3DXFX_DONOTSAVE*</code> flags). Ok, fine, but what's then? After one effect, another one will
be messed up because previous one might set some exotic states! Setting <em>all</em> states in each effect is also not a viable option: if just one of them wanted to set
some rarely used state (e.g. <code>ShadeMode=Flat</code>), then all other effects would need to set it back to <code>Gouraud</code>.
</p>

<H3>The Initial Solution</H3>
<p>
My first observation is that essentially there are three groups of device states:
</p>
<ol>
<li>States that need to be in "standard" values when beginning any effect. These need to be restored after any effect that modifies them.
	<br/><em>Example: all effects expect AlphaBlendEnable to be False initially.</em></li>
<li>States that do not need to be restored because every effect will set them anyway.
	<br/><em>Example: every effect will setup the right shader constants,
	so there's no point in restoring them.</em></li>
<li>States that do not need to be restored because they do not affect rendering in "standard" situations (i.e. states from the first group, while in "standard" values,
	turn off render states in this group). When used, these render states must be set; this usually coincides with setting first group states to non-default values. I
	call them "dependent states".
	<br/><em>Example: if each effect that turns on alpha blending will also set correct SrcBlend and DestBlend, there's no point in restoring them.</em></li>
</ol>
<p>
Which states fall into which groups is completely up to the engine. More on that <a href="#groups">later</a>.
</p>
<p>
The initial solution could be: in each effect, write an extra "restore pass". This pass sets 1st group states back to standard values, if they are modified by
the effect. In the renderer, turn off state saving/restoring; render all passes except last normally; and just begin/end the last pass (but don't actually render anything
in it).
</p>
<p>An example effect excerpt could look like this:</p>
<pre>
technique tec20 {
    pass POpaque {
        VertexShader = compile vs_1_1 vsMainOpaque();
        PixelShader = compile ps_1_1 psMainOpaque();
        AlphaTestEnable = True;
        AlphaFunc = Greater;
        AlphaRef = 250;
    }
    pass PAlpha {
        VertexShader = compile vs_1_1 vsMainAlpha();
        PixelShader = compile ps_1_1 psMainAlpha();
        AlphaTestEnable = False;
        ZWriteEnable = False;
        AlphaBlendEnable = True;
        SrcBlend = SrcAlpha;
        DestBlend = InvSrcAlpha;
    }
    <em>// restore pass (nothing will be actually rendered here)</em>
    <strong>pass PRestore</strong> {
        AlphaBlendEnable = False; <em>// restore alpha blend to standard value</em>
        ZWriteEnable = True; <em>// restore z write to standard value</em>
    }
}
</pre>
<p>
Ok, now we've got a solution that does not require saving/restoring all changed state. Only the <em>needed</em> (as defined by the engine) state is manually restored
via explicitly written "restore pass". This achieves the primary objective - performance (see <a href="#perf">measurements below</a>), but has a serious problem...
</p>
<p>
<em>It is very error prone</em>. What if you forget to restore some needed state? The following effects will be messed up, and the cause is really hard to find. What if
you don't assign some dependent state when you should? The current effect will be messed up. More - this system requires remembering which states belong to which
groups... So while the solution <em>can work</em> (hey, it has worked for me for 2 years!), most likely it <em>will not</em> or at least will cause some serious headache.
Especially if there are many people authoring effect files.
</p>


<H3>Enter Improved Solution</H3>

<p>In short: <strong>automagically generate the state restore pass</strong>. In more detail:</p>
<ol>
<li>Know which states fall into which groups. This is easy: just load group descriptions from some file.</li>
<li>Examine each effect and find out which states does it change.</li>
<li>Generate the state restore pass.</li>
<li>Finally, use the effect that consists of original one plus just generated state restore pass. In the renderer it is the same as the initial solution - render all
    passes except last and just begin/end the last pass.</li>
</ol>
<p>
Points 2-4 are not trivial, but doable. As a bonus, once the effect is examined (step 2), we can check whether it assigns all needed dependent states (third group), etc.
In the end, we should have a fairly robust system (no manual work necessary to make it work), with all the speed benefits (no need to save/restore all touched state).
Read on.
</p>


<H3>Implementation</H3>

<a name="groups"></a>
<p>Knowing which states fall into which groups (step 1 above) is easy - have some file that describes each group (see <a href="#exgroups">example below</a>):
<ol>
<li>Restored states: for each state have its "standard" value in text form. This will even handle such cases as: <code>CullMode</code> needs to be restored
	to <code>iCull</code> shared variable value - just list "<code>(iCull)</code>" as its standard value.</li>
<li>Need-to-be-set states: list of states that must be assigned by every effect. Shader constants are dealt with by Effect framework automatically
    (i.e. each effect will set them anyway). The same is often true for samplers (when using HLSL). So the list of "required" states that must be known to
    the engine is quite small; for example, just "each effect has to set <code>VertexShader</code> and <code>PixelShader</code>, even if to NULL".
    So just list these states in configuration file.</li>
<li>Dependent states: when "master state" is set to some value, all dependent states must also be set in the same pass. Example: if <code>AlphaBlendEnable</code>
	is set to True, <code>SrcBlend</code> and <code>DestBlend</code> must also be set. In the file, for each master state and its value, list all dependent states.</li>
</ol>
</p>

<p>
Examining the effect is harder. Hardcore approaches, like writing a custom effect file parser, are easily out of question (re-implementing preprocessor anyone?).
A sensible way to examine the effect is: load it; set custom <code>ID3DXEffectStateManager</code> that "remembers" all state assignments; begin all effect passes. After
that, the custom state manager will have all state assignments recorded (we're mostly interested in the states, not their values).
</p>

<p>
Now, generate the state restore pass: for each 1st group state that is assigned by the effect, issue its assignment back to "standard" value (standard values
come from configuration file). Here, we can also check whether effect's first pass assigns all needed states (2nd group); and whether all dependent states
(3rd group) are assigned in the same pass when the master state is set to its value. <em>Note: at least in 2004Oct SDK, the macro values can't span multiple
lines. So generate the state restore pass as one long line.</em>
</p>

<p>
The only thing left is: how do we get the generated state restore pass back into the effect? Currently there's no way to "inject" new passes programatically...
A possible solution is: require all effects to call macro <code>RESTORE_PASS</code>, like this:
</p>
<pre>
technique Foo {
    pass P1 { ... }
    pass P2 { ... }
    <strong>RESTORE_PASS</strong>
}
</pre>
<p>
When first loading the effect (for examining), set the macro to empty string (macro substitutions can be supplied programatically). After the effect is examined
and restore pass is generated, set the macro to the contents of restore pass, e.g. <code>PRestore{AlphaBlendEnable=False;}</code> and load the effect
again. This time, the effect should have the (generated) restore pass. After loading, check whether restore pass exists and complain loudly if it does not
(<em>"maybe you forgot to write RESTORE_PASS at the end?"</em>).
</p>


<H3>Summing it up</H3>

<p>We've got a system with some good properties:</p>
<ul>
<li>No need to save/restore all modified effect states.</li>
<li>Instead, only the <em>needed</em> states are restored. This restoring happens automagically via generated state restore pass. No manual work from effect author
	needed to make it work, just writing <code>RESTORE_PASS</code> near the end of technique (and the system will let you know if you forget that).</li>
<li>Because no states are ever saved, and only some are restored back; it should be faster!</li>
</ul>
<p>The system also has some drawbacks:</p>
<ul>
<li>Each effect has to be loaded twice (once for examining it; once for loading with injected restore pass), thus longer effect loading times. This
    could disappear if API would expose a way to add effect passes at runtime. Or, whole this restore pass generation could happen at build time:
    load each effect, generate restore pass, compile it, write out compiled binary version. Later on, just load compiled versions.</li>
<li>Someone needs to implement this system. Well, that must be done only once :)</li>
</ul>


<h2>Addendum</h2>

<a name="perf"></a>
<h3>Performance Measurements</h3>

<p>
I've made a test application that is heavy on effect switching, but the rendered objects and shaders are simple - hopefully that's a way to stress-test effect
switching performance.
</p>
<p>
The application renders 2000 objects each frame (2000 DIPs) and does <strong>1361</strong> effect switches (the actual number of effects is 8, but I intentionally didn't sort
by effects). Test system was P4@3GHz, GeForce6800GT (78.01 drivers), with DX SDK 2004 October (compiled with VC6) and 2005 June (compiled with VC7.1).
No observable speed difference was found in both SDK versions, suggesting that the bottleneck is all state save/restore stuff (runtime+driver) and not the
Effects internals (which presumably changed somewhat between SDK versions).
</p>
<table class="table-cells">
<tr style="font-weight: bold">
	<td width="65%">Approach</td>
	<td width="15%">FPS</td>
	<td width="15%">ms/frame</td>
	<td width="15%">ms/frame improvement</td>
</tr>
<tr>
	<td>Effects default: save/restore all state</td>
	<td>34.29</td>
	<td>29.16</td>
	<td>-</td>
</tr>
<tr>
	<td>Described solution ("needed state" restore pass)</td>
	<td>44.43</td>
	<td>22.51</td>
	<td>30%</td>
</tr>
<tr>
	<td>Described solution; plus redundant state filtering via ID3DXEffectStateManager</td>
	<td>46.51</td>
	<td>21.50</td>
	<td>36%</td>
</tr>
<tr>
	<td>Described solution; plus redundant state filtering; plus other reduntant sets filtering (vertex/index buffers, declarations etc.)</td>
	<td>47.45</td>
	<td>21.07</td>
	<td>38%</td>
</tr>
</table>
<p>
Of course, doing 1361 effect switches each frame is pretty surreal... Extrapolating the results towards 150 effect switches/frame (which is pretty realistic)
we get that proposed system alone can save 0.73 ms/frame; coupled with redundant states filtering the gain is 0.88 ms/frame. So while it won't improve renderer
beyond the speed of light, it is at least something :)
</p>


<a name="exgroups"></a>
<h3>State Groups</h3>

<p>
Over the course of several my own projects, I've settled upon quite stable state grouping. The following is Lua script that defines state
groups; it is directly read by the engine. The script should be pretty clear, even if you don't know Lua :)
</p>
<pre>
-- If these states are modified, they are restored to the given default values
restored = {
    -- render states
    { 'AlphaBlendEnable', 'False' },
    { 'SeparateAlphaBlendEnable', 'False' },
    { 'AlphaTestEnable', 'False' },
    { 'ClipPlaneEnable', '0' },
    { 'ColorWriteEnable', 'Red | Green | Blue | Alpha' },
    { 'FogEnable', 'False' },
    { 'PointSpriteEnable', 'False' },
    { 'StencilEnable', 'False' },
    { 'ZEnable', 'True' },
    { 'ZWriteEnable', 'True' },
    { 'BlendOp', 'Add' },
    { 'BlendOpAlpha', 'Add' },
    { 'Clipping', 'True' },
    { 'CullMode', '(iCull)' }, -- standard cull mode is iCull shared variable
    { 'DepthBias', '0' },
    { 'DitherEnable', 'False' },
    { 'FillMode', 'Solid' },
    { 'LastPixel', 'True' },
    { 'MultiSampleAntiAlias', 'True' },
    { 'MultiSampleMask', '0xFFFFFFFF' },
    { 'PatchSegments', '0' },
    { 'ShadeMode', 'Gouraud' },
    { 'SlopeScaleDepthBias', '0' },
    { 'ZFunc', 'Less' },
    { 'Wrap0', '0' },
    { 'Wrap1', '0' },
    { 'Wrap2', '0' },
    { 'Wrap3', '0' },
    { 'Wrap4', '0' },
    { 'Wrap5', '0' },
    { 'Wrap6', '0' },
    { 'Wrap7', '0' },
    { 'Wrap8', '0' },
    { 'Wrap9', '0' },
    { 'Wrap10', '0' },
    { 'Wrap11', '0' },
    { 'Wrap12', '0' },
    { 'Wrap13', '0' },
    { 'Wrap14', '0' },
    { 'Wrap15', '0' },
    -- exotic texture stage states
    { 'TexCoordIndex', '@stage@' }, -- value is the stage index
    { 'TextureTransformFlags', 'Disable' },
    -- exotic sampler states
    { 'MipMapLodBias', '0' },
    { 'MaxMipLevel', '0' },
    { 'SRGBTexture', '0' },
}

-- These states are required in first pass of each effect
required = {
    'VertexShader', 'PixelShader',
}

-- If the given master state is set to the given value, all listed
-- dependent states must also be set in the same pass.
dependent = {
    { 'AlphaBlendEnable', 1,
        { 'SrcBlend', 'DestBlend', }, },
    { 'SeparateAlphaBlendEnable', 1,
        { 'SrcBlendAlpha', 'DestBlendAlpha', }, },
    { 'AlphaTestEnable', 1,
        { 'AlphaFunc', 'AlphaRef', }, },
    { 'StencilEnable', 1,
        { 'StencilFail', 'StencilFunc', 'StencilMask', 'StencilPass', 'StencilWriteMask', 'StencilZFail', }, },
}
</pre>
<p>
As you can see, it's missing almost all fixed function T&amp;L related states. That's because I never found a good way to use T&amp;L
with Effects; and I don't really use it anymore either :)
</p>

<a name="source"></a>
<h3>My Implementation</h3>

<p>
I have this system implemented in my freetime demo/game engine <a href="http://dingus.berlios.de">dingus</a>. Whole implementation is more or less isolated in
<code>dingus/kernel/EffectLoader.[cpp|h]</code> files. Here are web links to them in Subversion <em>(best viewed with tab size 4, yes I do use tabs)</em>:
</p>
<ul>
<li>EffectLoader.h <a href="http://svn.berlios.de/viewcvs/dingus/trunk/dingus/dingus/kernel/EffectLoader.h?view=markup">head revision</a>
    (or <a href="http://svn.berlios.de/viewcvs/dingus/trunk/dingus/dingus/kernel/EffectLoader.h?rev=328&view=markup">revision</a> at the time of writing).</li>
<li>EffectLoader.cpp <a href="http://svn.berlios.de/viewcvs/dingus/trunk/dingus/dingus/kernel/EffectLoader.cpp?view=markup">head revision</a>
    (or <a href="http://svn.berlios.de/viewcvs/dingus/trunk/dingus/dingus/kernel/EffectLoader.cpp?rev=328&view=markup">revision</a> at the time of writing).</li>
</ul>
<p>
The implementation is not the nicest or the most robust one, but it works and hopefully illustrates the whole idea.
</p>

<h3>Comments?</h3>
<p>
If I've written complete nonsense here <em>(or described your own patented technique)</em>, feel free to drop me a mail: <strong>nearaz at gmail dot com</strong>
</p>
