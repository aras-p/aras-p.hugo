---
title: "Various details about Handles"
date: 2020-04-11T17:02:10+03:00
tags: ['unity', 'work']
comments: true
---

*I wanted to fix one thing about Unity editor [Handles](https://docs.unity3d.com/ScriptReference/Handles.html),
and accidentally ended up fixing some more. So here's more random things about Handles than you can handle!*


#### What I wanted to fix

For several years now, I wanted built-in tool (Move/Scale/Rotate) handle lines to be thicker than one pixel.
One pixel might have been fine when displays were 96 DPI, but these days a pixel is a *tiny* little
[non-square](http://alvyray.com/Memos/CG/Microsoft/6_pixel.pdf).

Often with some real colored content behind the tool handles, my old eyes can't quite see them. Look (click for full size):<br/>
[{{<img src="/img/blog/2020/handles/Scene1Move0.png" width="250px">}}](/img/blog/2020/handles/Scene1Move0.png)
[{{<img src="/img/blog/2020/handles/Scene1Rotate0.png" width="250px">}}](/img/blog/2020/handles/Scene1Rotate0.png)
[{{<img src="/img/blog/2020/handles/Scene2Rotate0.png" width="232px">}}](/img/blog/2020/handles/Scene2Rotate0.png)

That's way too thin for my taste. So I looked at how other similar programs (Maya, Blender, 3dsmax, Modo, Unreal) do it,
and all of them except 3dsmax have thicker tool handles. Maya in particular has *extremely customizable* handles where
you can configure
[everything](https://knowledge.autodesk.com/support/maya/learn-explore/caas/CloudHelp/cloudhelp/2018/ENU/Maya-Customizing/files/GUID-8740471F-BD72-4BDC-87B4-4DE870AD51E5-htm.html)
to your taste -- perhaps too much even :)

Recently at work I got an *"ok aras do it!"* approval for making the handles thicker, and so I went:<br/>
[{{<img src="/img/blog/2020/handles/Scene1Move2.png" width="250px">}}](/img/blog/2020/handles/Scene1Move2.png)
[{{<img src="/img/blog/2020/handles/Scene1Rotate2.png" width="250px">}}](/img/blog/2020/handles/Scene1Rotate2.png)
[{{<img src="/img/blog/2020/handles/Scene2Rotate2.png" width="232px">}}](/img/blog/2020/handles/Scene2Rotate2.png)

That by itself is not remarkable at all *(well, except that I spent way too much time figuring out how to do something,
that is placed in world space, to be constant size in pixel space, LOL)*. But while playing around with handle thickness,
I noticed a *handful* other little things that were bugging me, I just never clearly thought about them.

*Here they are in no particular order, in no other reason than an excuse to post some pictures & gifs!*

#### Hover indicator with yellow color is not great

When mouse is hovering over or near enough the handle axis, it changes color to yellow-ish, to indicate that when you click,
the handle will get picked up. The problem is, "slight yellow" is too similar to say the green (Y axis) color, or to the
gray "rotate facing screen" outline. In the two images below, one of them has the outer rotation circle highlighted since I'm
hovering over it. It's not easy to tell that the highlight is actually happening.<br/>
[{{<img src="/img/blog/2020/handles/HoverYellowOff.png" width="240px">}}](/img/blog/2020/handles/HoverYellowOff.png)
[{{<img src="/img/blog/2020/handles/HoverYellowOn.png" width="240px">}}](/img/blog/2020/handles/HoverYellowOn.png)

What I tried doing instead is: upon hovering, the existing handle color turns a bit brighter, more opaque and the handle gets
a bit thicker.<br/>
[{{<img src="/img/blog/2020/handles/HoverRotateHighlight.gif" width="228px">}}](/img/blog/2020/handles/HoverRotateHighlight.gif)
[{{<img src="/img/blog/2020/handles/HoverMoveHighlight.gif" width="250px">}}](/img/blog/2020/handles/HoverMoveHighlight.gif)

#### Move/Scale tool cap hover detection is weird

In some cases, the mouse being directly over one cap still picks another (close, but further from the mouse) axis. Here,
the mouse is directly over the red axis cap, yet the blue one is “picked”. That seems to be fine with axes, but
the “cap” part feels wonky.<br/>
[{{<img src="/img/blog/2020/handles/CapHitZone.gif" width="250px">}}](/img/blog/2020/handles/CapHitZone.gif)

I dug into the code, and the cause turned out to be that cone & cube caps use very approximate “distance to sphere”
mouse hover calculation. E.g. for the Move tool, these spheres are what the arrow caps “look like” for the mouse picking.
Which does not quite match the actual cone shape :) For a Scale tool, the virtual spheres are even way larger than cubes.
A similar issue was with the scene view axis widget, where axis mouse hit detection was using spheres of this size for picking
*:facepalm:* <br/>
[{{<img src="/img/blog/2020/handles/CapHitMove.png" width="250px">}}](/img/blog/2020/handles/CapHitMove.png)
[{{<img src="/img/blog/2020/handles/CapHitScale.png" width="244px">}}](/img/blog/2020/handles/CapHitScale.png)
[{{<img src="/img/blog/2020/handles/CapHitScene.png" width="228px">}}](/img/blog/2020/handles/CapHitScene.png)

Now, I get that checking distance to sphere is much easier, particularly when it has to be done in screen space, but come on.
A sphere is not a great approximation for a cone :) Fixed this by writing "distance to cone" and "distance to cube" (in screen space)
functions. Underneath both are "distance to a 2D convex hull of these points projected into screen space". Yay my first
convex hull code, I don't remember ever writing that!


#### At almost parallel viewing directions, axis is wrongly picked

Here, moving the mouse over the plane widget and try to move, poof the Z axis got picked instead (see
[this tweet](https://twitter.com/antonkudin/status/1248314083906146304) too).<br/>
[{{<img src="/img/blog/2020/handles/MoveAxisBad.gif" width="250px">}}](/img/blog/2020/handles/MoveAxisBad.gif)


What I did: 1) when the axis is almost faded out, never pick it. The code was  trying to do that, but only when the axis
is almost entirely invisible. 2) for a partially faded out axis, make the hover indicator not be faded out, so you
can clearly see it being highlighted.<br/>
[{{<img src="/img/blog/2020/handles/MoveAxisPlane.gif" width="250px">}}](/img/blog/2020/handles/MoveAxisPlane.gif)
[{{<img src="/img/blog/2020/handles/MoveAxisBetterHighlight.gif" width="250px">}}](/img/blog/2020/handles/MoveAxisBetterHighlight.gif)


#### Cap draw ordering issues (Z always on top of X etc.)

Handle axes were always rendered in X, Y, Z order. Which makes the Z always look “in front” of X even when it’s actually behind:<br/>
[{{<img src="/img/blog/2020/handles/WrongDrawOrder.png" width="250px">}}](/img/blog/2020/handles/WrongDrawOrder.png)

Fixing this one is easy, just sort the axis handles before processing them.



#### Free rotate circle is barely visible

The inner circle of rotation gizmo that is for “free” (arcball) rotation is barely visible:<br/>
[{{<img src="/img/blog/2020/handles/FreeRotateBefore.png" width="240px">}}](/img/blog/2020/handles/FreeRotateBefore.png)

Make it more visible. A tiny bit more visible when not hovering (left), thicker + more visible when hovering (right):<br/>
[{{<img src="/img/blog/2020/handles/FreeRotateAfter.png" width="240px">}}](/img/blog/2020/handles/FreeRotateAfter.png)
[{{<img src="/img/blog/2020/handles/FreeRotateAfterHover.png" width="235px">}}](/img/blog/2020/handles/FreeRotateAfterHover.png)


#### Move tool plane handles are on the opposite side in Ortho projection

With an orthographic scene view camera, the "move on plane" handles are on the opposite side of the gizmo:<br/>
[{{<img src="/img/blog/2020/handles/PlaneHandlesPerspOrtho.gif" width="250px">}}](/img/blog/2020/handles/PlaneHandlesPerspOrtho.gif)

Turns out that was just a sign bug.


*And that's it for this round of Handles tweaks!* There's a ton more that could be done (see replies to
[Will's tweet](https://twitter.com/willgoldstone/status/1248278216206360577)), but that's for some other day. ~~Nothing from
above has shipped or landed to mainline code branch yet, by the way. So no promises :)~~ *Update: should ship in Unity 2020.2 alpha 9 soon!*


