---
layout: page
title: Oblique Near-Plane Clipping with Orthographic Camera
comments: true
sharing: true
footer: true
menusection: texts
url: /texts/obliqueortho.html
---

An often used trick for rendering water reflections, mirrors and so on is modifying the projection
matrix so that near plane coincides with the water/mirror plane. See <a href='http://www.terathon.com/code/oblique.php'>code by Eric Lengyel</a>
or his article in Game Programming Gems 5 on the same subject.

The code however only works with perspective projections. Sometimes people want water/mirrors in
orthographic cameras as well. It's not hard, but I haven't seen any info on how to do this, so here it is!


<h3>Modifying orthographic projection for oblique near plane</h3>

The derivation is exactly the same as with original Lengyel's approach: near &amp; far clipping planes
are defined by third +- fourth rows of the matrix (using OpenGL style notation here). The fourth
row in usual orthographic projections is <tt>(0 0 0 1)</tt>, so if our custom near plane is <tt>(a b c d)</tt>, that means
the third row must be <tt>(a b c d-1)</tt>.

This however hoses the far clipping plane, so use the same scaling trick to move the far plane so that it
includes the original frustum.

The resulting frustum looks like this (gray cube is the original frustum; blue is new near plane, red is new
far plane, clipping plane coindices with the grid): <br />
<img src='img/oblique-ortho.png' />


<h3>The code</h3>

<del>Pseudocode</del> actual C# code to do this could look like below. Matrix
inverse optimization from Eric's code is for perspective case, so we do the full inverse
here (can also be optimized for canonical ortho matrices).

```c
// modifies projection matrix in place
// clipPlane is in camera space
void CalculateObliqueMatrixOrtho( ref Matrix4x4 projection, Vector4 clipPlane )
{
    Vector4 q;
    q = projection.inverse * new Vector4(
        sgn(clipPlane.x),
        sgn(clipPlane.y),
        1.0f,
        1.0f
    );
    Vector4 c = clipPlane * (2.0F / (Vector4.Dot (clipPlane, q)));
    projection[2] = c.x;
    projection[6] = c.y;
    projection[10] = c.z;
    projection[14] = c.w - 1.0F;
}
```

Of course it's also possible to write code that works with both perspective and
orthographic projections:

```c
// modifies projection matrix in place
// clipPlane is in camera space
void CalculateObliqueMatrixOrtho( ref Matrix4x4 projection, Vector4 clipPlane )
{
    Vector4 q = projection.inverse * new Vector4(
        sgn(clipPlane.x),
        sgn(clipPlane.y),
        1.0f,
        1.0f
    );
    Vector4 c = clipPlane * (2.0F / (Vector4.Dot (clipPlane, q)));
    // third row = clip plane - fourth row
    projection[2] = c.x - projection[3];
    projection[6] = c.y - projection[7];
    projection[10] = c.z - projection[11];
    projection[14] = c.w - projection[15];
}
```

That's it!
