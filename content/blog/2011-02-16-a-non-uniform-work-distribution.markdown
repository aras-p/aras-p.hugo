---
tags:
- rant
- work
comments: true
date: 2011-02-16T17:47:57Z
slug: a-non-uniform-work-distribution
status: publish
title: A Non-Uniform Work Distribution
url: /blog/2011/02/16/a-non-uniform-work-distribution/
wordpress_id: "630"
---

_Warning: a post with stupid questions and no answers whatsoever!_

You need to do ten thousand things for the gold master / release / ShipIt(tm) moment. And you have 40 people who do the actual work... this means each of them _only_ has to do 10000/40=250 things, which is not that bad. Right?

Meanwhile in the real world... it does not actually work like that. And that's something that has been on my mind for a long time. I don't know how much of this is truth vs. perception, or what to do about it. But here's my feeling, simplified:

**20 percent of the people are responsible for getting 80 percent of the work done**

I am somewhat exaggerating just to keep it consistent with the [Pareto principle](http://en.wikipedia.org/wiki/Pareto_principle). But my feeling is that "work done" distribution is highly non uniform everywhere I worked where the team was more than a handful of people.

Here are some stupid statistics to illustrate my point (with graphs, and everyone loves graphs!):

Graph of bugs fixed per developer, over one week during the bug fixing phase. Red/yellow/green corresponds to priority 1,2,3 issues:

[![](http://aras-p.info/blog/wp-content/uploads/2011/02/graphbugs.png)](http://aras-p.info/blog/wp-content/uploads/2011/02/graphbugs.png)

The distribution of bugs fixes is, shall we say, _somewhat_ non uniform.

Is it a valid measure of "productivity"? Absolutely not. Some people probably haven't been fixing bugs at all that week. Some bugs are _way_ harder to fix than others. Some people could have made major part of the fix, but the finishing touches & the act of actually resolving the bug was made by someone else. So yes, this statistics is absolutely flawed, but do we have anything else?

We could be checking version control commits.

[![](http://aras-p.info/blog/wp-content/uploads/2011/02/svntimeline-500x243.png)](http://aras-p.info/blog/wp-content/uploads/2011/02/svntimeline.png)

Or putting the same into "commits by developer":

[![](http://aras-p.info/blog/wp-content/uploads/2011/02/svnauthor-500x269.png)](http://aras-p.info/blog/wp-content/uploads/2011/02/svnauthor.png)

Of course this is even easier to game than resolving bugs. _"Moving buttons to the left", "Whoops, that was wrong, moving them to the right again"_ anyone? And people will be trolling statistics just because they can.

[![](http://aras-p.info/blog/wp-content/uploads/2011/02/svntroll.png)](http://aras-p.info/blog/wp-content/uploads/2011/02/svntroll.png)

However, there is still this highly subjective "feeling" that some folks are way, _way_ faster than others. And not in just "can do some mess real fast" way, but in the "gets actual work done, and done well" way.

Or is it just my experience? How is it in your company? What can be done about it? Should something be done about it? I don't know the answers...
