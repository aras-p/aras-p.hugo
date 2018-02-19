---
title: "Unity in 2018"
date: 2018-02-18T21:50:27+02:00
tags: ["unity", "work"]
comments: true
---
I don't remember if I ever was as excited for what's coming to Unity, as I am right now.
And I have been through quite some times, all the way from Unity 1.5! *(that was in 2006, or
somewhere in the middle of Priabonian age)*

[{{<img src="/img/blog/2018/unity2018-gfx.jpg">}}](/img/blog/2018/unity2018-gfx.jpg)

*A lot* of exciting things are falling into place:

[{{<imgright src="/img/blog/2018/unity2018-packagemanager.png" width="200px">}}](/img/blog/2018/unity2018-packagemanager.png)

* Package Manager.
* [ProBuilder](https://blogs.unity3d.com/2018/02/15/probuilder-joins-unity-offering-integrated-in-editor-advanced-level-design/);
  finally Unity gets really good level blockout & building tools!
* C# Job System and Burst Compiler; see [Unite Austin talk](https://www.youtube.com/watch?v=tGmnZdY5Y-E) and some
  [cool stuff](https://twitter.com/LotteMakesStuff/status/964612077708070912) that people are already doing with it.
* Scriptable Render Pipeline; see [overview](https://blogs.unity3d.com/2018/01/31/srp-overview) and upcoming
  [HD Render Pipe](https://github.com/Unity-Technologies/ScriptableRenderPipeline).
* [Shader Graph](https://github.com/Unity-Technologies/ShaderGraph).

[{{<img src="/img/blog/2018/unity2018-shadergraph.png">}}](/img/blog/2018/unity2018-shadergraph.png)

A lot of other stuff is happening too; many pieces that were considered "experimental/preview" before
will soon drop their experimental labels (e.g. [Progressive Lightmapper](https://docs.unity3d.com/Manual/ProgressiveLightmapper.html)
or [.NET 4.6 Scripting Runtime](https://docs.unity3d.com/Manual/ScriptingRuntimeUpgrade.html)).

[{{<imgright src="/img/blog/2018/unity2018-nest.png" width="64px">}}](/img/blog/2018/unity2018-nest.png)

And then *way* more stuff is being developed; some of it fairly close to shipping and I hope will ship this year; some still a bit
further out. I wish I could tell more... suffice to say, among other things we have this custom emoji -- whatever it might mean --
in the company Slack, and it's getting quite a lot of usage lately.

*This is all very exciting!*

But, what is perhaps even better, is that I think **we've found a way how to do a big jump/move from "where we are today" to
"where we want to be in 5 years"**.

This is one of the hardest problems in evolving a fairly popular product; it's very hard to realize how hard it is without
actually trying to do it. Almost every day you're off with something you'd want to change, but *a lot* of possible changes
might break some existing content. A "damned if you do, damned if you don't" type of situation, that
[@mcclure111](https://twitter.com/mcclure111) described so brilliantly:

> Library design is this: You have made a mistake. It is too late to fix it. There is production code depending on
> the mistake working exactly the way the mistake works. You will never be able to fix it. You will never be able
> to fix anything. You wrote this code nine seconds ago. [[source](https://twitter.com/mcclure111/status/954137509843398656)]

It's easy to make neat tech that barely anyone uses. It's moderately simple to make technically brilliant engine that gets two dozen
customers, and then declare a ground-up 100% rewrite or a whole new engine, that *This Time Will Be Even More Brilliant-er-er*.
Get two dozen customers, rinse & repeat again.

Doing a re-architecture of an engine (or anything, really), while hundreds of thousands of projects are in flight, and trying
to disrupt them as little as possible, is a hundred times harder. And I'm not exaggerating, it's easily a *hundred* times harder.
When I was doing customer-facing features, improvements & fixes, this was *the* hardest part in doing it all.

So I'm super happy that we seem to have a good plan in how to tackle this! The Package Manager is a huge part of that. The new
Entity Component System is the first big piece of this "re-architecture the whole thing". You can opt in to use it, or you can ignore
it for a bit... but we hope the benefits are too big to ignore. You can also start to use it piece by piece, transitioning your
knowledge & production to it.

Many other systems are likely to follow in a similar fashion. For example the current Scriptable Render Pipeline approach replaces the
high-level rendering code with C#, but the underlying "graphics platform" layer is more or less the same. Some parts of it are
in less-than-ideal state or design... I've been thinking that it would be possible to "upgrade" it in-place to be way more modern,
but by now it feels like maybe parts of it should be started anew. And so at some point a new graphics platform layer will be built,
a new material/shader runtime will happen, etc. etc. It will live side by side with the "old stuff" for a while, similar to how the
new ECS and the old GameObject/Component system will live together.

And this time I feel like we will be able to pull it off, more so than previous times :) Wish us luck!
