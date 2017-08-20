---
title: LTGameJam 2003 dev log
comments: true
sharing: true
footer: true
url: jam2log.html
---

<A href="http://jammy.sourceforge.net">LTGameJam website</A>.

<p clear="all"><strong>2003 05 01</strong></P>
<p>
<div style="float: right">
<A href="img/jam2/0501-split1.jpg"><img src="img/jam2/tn0501-split1.jpg"></A>
<A href="img/jam2/0501-split2.jpg"><img src="img/jam2/tn0501-split2.jpg"></A>
</div>
The splitscreen - so I don't have to implement the network, and the programmers don't have to implement AI :)
</p>


<p clear="all"><strong>2003 04 25-29</strong></P>
<p>
<div style="float: right">
<A href="img/jam2/0429-01.jpg"><img src="img/jam2/tn0429-01.jpg"></A>
<A href="img/jam2/0429-02.jpg"><img src="img/jam2/tn0429-02.jpg"></A>
<A href="img/jam2/0429-03.jpg"><img src="img/jam2/tn0429-03.jpg"></A>
</div>
I've had really hard time getting collision/physics to work... Now, after some 40 hours of
tuning, chasing bugs, fixing bugs and making new bugs, it works at least a bit reasonably. In the beginning
it was hard to drive a flat road without flipping up several times!
</p>
<p>
The shots show a car in action (with wrong texture on the wheels :)) - thanks to
<A href="http://www.reactor.lt/">Kestas Dumbra</A> for the model.
</p>
<p>
One really needs some shadows in racing scenes!
</p>
<p>
For the ones that may have missed it: <strong>This LTGameJam will happen on May 3-4! If you plan to participate, notify
<A href="mailto:nearaz_at_gmail_dot_com">me</A> very quickly!</strong>
</p>

<p clear="all"><strong>2003 04 22</strong></P>
<p>
<div style="float: right">
<A href="img/jam2/0422-game.jpg"><img src="img/jam2/tn0422-game.jpg"></A>
</div>
Ok, Easter's over, back to work. Now it's clear that I won't finish everything till this weekend, so I'm
putting the Jam to the next weekend (also because of KaunasJazz, Metalikonas and other events that get in the way).
</p>
<p>
A shot of yet-to-be game (it's the same track as was in the editor shots; and it's my same old TNT2 - I'll
probably go and get myself a Radeon9500 tomorrow :)).
</p>


<p clear="all"><strong>2003 04 16</strong></P>
<p>
<div style="float: right">
<A href="img/jam2/0416-editor.jpg"><img src="img/jam2/tn0416-editor.jpg"></A>
</div>
Finished level editor, now onto the main part... So far nothing impressive: need to write some more scripts for 3dsmax
(collision structures exporting), need to make some more models (my superb modelling skills!), etc.
</p>
<p>
In the shot there's thing editing in action (the signs and the cones) - nothing fancy, but that's the only thing
I can show at the moment :)
</p>

<p clear="all"><strong>2003 04 11</strong></P>
<p>
<div style="float: right">
<A href="img/jam2/0411-editor.jpg"><img src="img/jam2/tn0411-editor.jpg"></A>
</div>
Level editing is quite comfortable now and, I'd say, finished. Still you can't place arbitrary objects onto the level
(road signs, bushes and other stuff that gets in the way) - hopefully I'll do that in the next few days. I can assebmle
the track in the shot in about a minute - so reasonably complex tracks shouldn't take more than 10 minutes... I've
got to try the editor on some other person - maybe it's combortable only for me?
</p>

<p clear="all"><strong>2003 04 09</strong></P>
<p>
Level editor of static geometry is almost finished - of course it's not Max or Maya, but might do the trick.
One can place geometry parts (meshes) that have predefined "tags" (attachment points), and link the meshes
using their tags. I'll add obstacle/marker placement now (select a tree, click where it should be and it should
appear there; almost the same for various markers). So far everything's not so hard as I thought :)
</p>
<p>
In my "ideal version", the Jam code should already contain things like player cars, moveable obstacles,
weapon stuff (rockets/lasers/machineguns), various markers (track start/finish and checkpoint lines, etc.),
some simple menu (eg. choose a car). Jam attendant should just use/modify them and go create the game.
</p>

<p clear="all"><strong>2003 04 08</strong></P>
<p>
<div style="float: right">
<A href="img/jam2/0408-editor1.jpg"><img src="img/jam2/tn0408-editor1.jpg"></A>
<A href="img/jam2/0408-editor2.jpg"><img src="img/jam2/tn0408-editor2.jpg"></A>
</div>
I'm developing the "engine" almost a month - haven't written any log yet. This time everything is
"big" - we've got full-fledged engine, DX9; component-based entities and similar stuff. Don't know
if it's good or bad - we'll see at runtime.
</p>
<p>
This time everything's much more complicated - it's no more "terrain and lots of sprites"; that complicates
things not only for me, but also for the attendants of the Jam. Eg. take a racing game level - they'll have
to build it somehow, place obstacles onto it, set physics params, etc. - it's very tedious and slow to do that
from C++ or config/script files. Therefore, I've decided to build some "editor" - but that complicates things for
me. One rule when doing something is "don't get into writing editors - you'll never finish the main thing", so I'm
risking the whole Jam :)
</p>
<p>
Hope to finish the editor this week (long with it goes lots of shared code for Jam games); then I'll have 2 weeks
to finish everything up. The day's not long enough - as always.
</p>
<p>
I couldn't resist trying new things while doing "the engine" - first there was DX9 with HSLS/fx stuff. Then there was
component-based entities, using smart pointers everywhere. Now I'm using Lua in some places <em>(nono, not the scripts!
for configuration only!)</em>. Lua rocks as configuration language; even more - I can write functions and stuff inside the
files to reduce the amount of typing needed. I can export from 3D packages (eg. using MaxScript, MEL, etc.) directly into
Lua files - ain't that sweet? :)
</p>
