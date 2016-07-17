---
layout: page
title: Numbers in Transformation Matrices
comments: true
sharing: true
footer: true
menusection: texts
url: /texts/matrices.html
---

Way too often I see people trying to jump through hoops to get something into transformation
matrices. They wonder about the correct order of compositing <tt>glTranslate</tt>/<tt>glRotate</tt>/... calls;
or they track object axis vectors over time, calculate angles between them, then try to convert
that into basic transformation primitives (translate/rotate), then put that into correct
order to get some object correctly positioned. Instead of just using the axis vectors they
already have!

The single most useful thing I remember from the university: it made me understand transformation
matrices; what they actually are and how they work. And believe me, this does give a whole
new meaning to life, unverse and everything.

Here I'll try to explain what the numbers in a regular transformation matrix actually mean.
I won't be touching perspective or other funky matrices, because I don't actually <em>know</em>
how they work. I hope to learn that someday!



<h3>What are the values in the matrix?</h3>

A regular 3D transformation matrix is a 4x4 grid of numbers (notes on layout
<a href="#notation">below</a>):
<pre>
Xx Xy Xz 0
Yx Yy Yz 0
Zx Zy Zz 0
Ox Oy Oz 1
</pre>

In the matrix <tt>(Xx, Xy, Xz)</tt> is the X vector, <tt>(Yx, Yy, Yz)</tt> is the Y vector,
<tt>(Zx, Zy, Zz)</tt> is the Z vector and <tt>(Ox, Oy, Oz)</tt> is the origin (position). One
common convention is that X means "right side", Y means "up", Z means "forward" and position is,
well, position.

What does this mean? Basically two things:
<ul>
<li>If you know the orientation of your object as vectors ("my spaceship points along this vector,
and it's "up" is along that vector) and it's position, you can construct the matrix directly!
Note that unless the object is skewed, the third axis vector will be perpendicular to the other
two and can be trivially found via cross product.</li>
<li>If you know the matrix of some object, you can find out it's position and orientation vectors
directly. This also makes operations like "move forward by <em>amount</em>" fairly trivial -
moving forward is adding Z axis multiplied by <em>amount</em> to the position. You can do all
this directly on the matrix if you want to!</li>
</ul>

Basically that's it. The coordinate axes are directly in the matrix. If the matrix is for just
some rotated object, then the axes are all perpendicular and of unit length. If the object is scaled
then the axes are of non-unit length; if the object is skewed then the axes are not perpendicular.
Simple as that!


<a name="notation">
<h4>Note on layout and notation</h4></a>

The matrix notation above is like usually presented in Direct3D. OpenGL folks are more used to
this notation:
<pre>
Xx Yx Zx Ox
Xy Yy Zy Oy
Xz Yz Zz Oz
0  0  0  1
</pre>
which is just transposed form of the above. But it's actually just the notation, as the layout of
matrices in memory is exactly the same between those APIs. That is, if a matrix is represented as
array of 16 floats, then X axis is [0], [1], [2] elements and positions is [12], [13], [14] elements
in both D3D and GL.


<h3>Primitive transformations</h3>
Understanding what numbers go into matrices also reveals why the "simple primitive" transformations
do look like they look like. For example, a translation matrix is (transpose if you're OpenGL user):
<pre>
1  0  0  0
0  1  0  0
0  0  1  0
Tx Ty Tz 1
</pre>
Why it is like this? It should be pretty obvious by now: this matrix represents something that is
not rotated in any way in relation to it's parent: the X, Y, Z axes are all just
<tt>(1,0,0)</tt>, <tt>(0,1,0)</tt>, <tt>(0,0,1)</tt> - they correspond to parent axes. The origin
is at position <tt>(Tx,Ty,Tz)</tt> inside the parent. So this is translation!

Similarly for scale:
<pre>
Sx 0  0  0
0  Sy 0  0
0  0  Sz 0
0  0  0  1
</pre>
Here the X, Y, Z axis vectors are <tt>(Sx,0,0)</tt>, <tt>(0,Sy,0)</tt>, <tt>(0,0,Sz)</tt> - just
parent axes at different lengths. Scale!

Question now: given a matrix of some object, how would you construct a new matrix that has the
object scaled along X axis twice, without doing matrix multiplication? Of course, just multiply
the first three elements of the first row by two - it scales the X axis. Note that doing that
<em>does not mean</em> it's going to be faster than matrix multiplication, especially if your
matrix math is SIMD optimized (don't assume anything is going to be faster without
asking the profiler first).



<h3>That's it!</h3>

Well, that's it. For me this was a breakthrough moment - it made me realize that a matrix
<em>is</em> a coordinate space, instead of a bunch of magic numbers that transform stuff into stuff.
I was also going to write about how matrices are spaces expressed in terms of other spaces etc., but
that proved to be hard to explain. I'll just keep that for myself!
