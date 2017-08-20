---
title: Reverse extruded shadow volumes
comments: true
sharing: true
footer: true
menusection: texts
url: /texts/revext.html
---

<p>
My colleague Renaldas Zioma aka ReJ (<a href="mailto:rej_at_scene_dot_lt">email</a>) came
up with this idea. So far we don't know of anyone else who have used it before.
Extended version can be found in <A href="http://www.shaderx2.com">ShaderX2</A> book.
</p>

<H3>The technique</H3>
<p>
Suppose that for some reason we want to use stenciled shadow volumes and don't want
to use CPU to find silhouette edges - we want static geometry and a vertex shader.
We also want self-shadowing, but don't want to use high-poly mesh for shadow volume.
Eg.: a mesh and a simplified mesh:
</p>
<center><IMG src="img/revext-models.png"></center>

<p>
Conventional shadow volume is built by extruding back-facing vertices away from
light source: check the angle between light and a normal and either extrude away
or leave vertex in place.
</p>
<p>
Reverse extruded shadow volume is built by extruding front-facing vertices - leave
back-facing ones in place, but send away from light the ones that face the light.
</p>
<p>
Illustrated: in the "normal" case the marked vertices are extruded, and the rest left
in place. We get a shadow volume that covers both the object and a shadow space behind
it. In "reverse" case, we extrude the marked vertices, and get a shadow volume that
covers only the shadow space behind the object.
</p>
<center><IMG src="img/revext-extrusions.png"></center>

<p>
Conventional extrusion may cause problems when simplified mesh is used - either you have
to manually fatten or shrink the volume, push frontfaces backwards a bit, or disable
self-shadowing. Illustrated:
</p>
<center><IMG src="img/revext-normLow.png"></center>
<p>
The self-shadowing artifacts on the lit side are caused by the simplified mesh - it's
protruding from the original mesh in these places.
</p>

<p>
If we reverse the extrusion, the artifacts are gone - the frontfacing vertices are
pushed back, so there's nothing to protrude. Illustrated:
</p>
<center><IMG src="img/revext-revLow.png"></center>
<p>
What we lose by reversing the extrusion are direct shadows on the backside of the
object. But these can be dealt with the shading system - "no light" is nearly the same
as "in shadow". In the picture some artifacts can be seen - it's because per-vertex
lighting is used there, not per-pixel.
</p>

<p>
We've used this "reverse" technique in our game on skinned characters
(mesh ~8000 vertices, simplified mesh ~2000 vertices) and so far it
worked well.
</p>

<H3>Demo</H3>

<p>
A small demo illustrating all this (normal vs. reverse extrusion, high vs. low poly
shadow mesh). Requires DirectX 8.1 and stencil buffer support.
</p>
<p>
Binary and source <A href="files/RevExtShadows.zip"><strong>here</strong></A> (394 KB).
</p>
<p>
The extrusion here is performed with a vertex shader (all the other transformations
also). Geometry isn't "prepared well" for shadow volumes - there are no thin
polygons between real polygons. But the idea is illustrated :)
</p>
