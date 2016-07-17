---
tags: [ ]
comments: true
date: 2013-07-07T00:00:00Z
title: Reviewing ALL THE CODE
url: /blog/2013/07/07/reviewing-all-the-code/
---

I like to review ALL THE THINGS that are happening in our codebase. Currently we have about 70 programmers,
mostly comitting to a single Mercurial repository (into a ton of different branches), producing about
120 commits per day. I used to review all that using [RhodeCode's](http://rhodecode.org/) "journal" page, but [Lucas](https://twitter.com/lucasmeijer) taught me a much
better way. So here it is.


** Quick description of our current setup **

We use [Mercurial](http://mercurial.selenic.com/) for source control, with `largefiles` extension for versioning big binary files.

Branches ("named branches", i.e. not "bookmarks") are used for branching. Joel's [hg init](http://hginit.com/05.html) talks about using physical separate repositories to emulate branching, but don't listen to that. That way lies madness. Mercurial's branches work perfectly fine and are *much* superior workflow (we used to use "separate repos as branches" in the past, back [when we used Kiln](http://aras-p.info/blog/2011/04/18/mercurialkiln-experience-so-far/) - not recommended).

We use [RhodeCode](http://rhodecode.org/) as a web interface to Mercurial, and to manage repositories, user permissions etc. It's also used to do "pull requests" and for commenting on the commits.


** 1. Pull everything **

Each day, pull *all the branches* into your local repository clone. Just `hg pull` (difference from normal workflow,
where you pull only your current branch, `hg pull -b .`).

{{<img src="/img/blog/2013-07/review-pull.png">}}

Now you have the history of *everything* on your own machine.


** 2. Review in SourceTree **

Use [SourceTree's](http://www.sourcetreeapp.com/) Log view and there you have the commits. Look at each and every one of them. 

[{{<img src="/img/blog/2013-07/review-sourcetree-500.jpg">}}](/img/blog/2013-07/review-sourcetree.png)

Next, setup a "custom action" in SourceTree to go to a commit in RhodeCode. So whenever I see a commit that I want
to comment on, it's just a right click away:

{{<img src="/img/blog/2013-07/review-customaction.png">}}

SourceTree is awesome by the way (and it's now both on Windows and Mac)!


** 3. Comment in RhodeCode **

Add comments, approve/reject the commit etc.:

[{{<img src="/img/blog/2013-07/review-rhodecode-500.png">}}](/img/blog/2013-07/review-rhodecode.png)

And well, that's about it!


** Clarifications **


*Why not use RhodeCode's Journal page?*

I used to do that for a long time, until I realized I'm wasting my time. The journal is okay to see that "some activity is happening", but not terribly useful to get any real information:

{{<img src="/img/blog/2013-07/review-journal.png">}}

I can see the commit SHAs, *awesome*! To see even the commit messages I have to hover over each of them and wait a second for the commit message to load via some AJAX. To see the actual commit, I have to open a new tab. At 100+ commits per day, that's massive waste of browser tabs!


*Why not use Kiln?*

We [used to use Kiln](http://aras-p.info/blog/2011/04/18/mercurialkiln-experience-so-far/) indeed. Everything seemed nice and rosy until we hit *massive* scalability problems (team size grew, build farm size grew etc.). We had problems like build farm agents stalling the checkout for half an hour, just waiting for Kiln to respond (Kiln itself is the only gateway to the underlying Mercurial repository, so even the build farm had to go through it).

Afer many, many months of trying to find solutions to the scalability problems, we just gave up. No amount of configuration / platform / hardware tweaking seemed to help. That was Kiln 2.5 or so; they might have improved since then. But, once bitten twice shy.

Kiln still has the best code review UI I've ever seen though. If only it scaled to our size...


*Seriously, you review everything?*

Not *really*. In the areas where I'd have no clue what's going on anyway (audio, networking, build infrastructure, ...), I just glance at the commit messages. Plus, all the code (or most of it?) is reviewed by other people as well; usually folks who have some clue.

I tried tracking review time last week, and it looks like I'm spending about an hour each day reviewing code like this. Is that too low or too high? I don't know.

There's a rumor going on that my office is nothing but a giant wall of monitors for watching all the code. That is not true. Really. Don't look at the wall to your left.


*How many issues do you find this way?*

3-5 minor issues each day. By far the most common one: accidentally comitting some debugging code leftovers or totally unrelated files. More serious issues every few days, and a *"stop! this is, like, totally wrong"* maybe once a week.

Another side effect of reviewing everything, or at least reading commit messages: I can tell who just started doing what and preemptively prevent others from starting the same thing. Or relate a newly introduced problem (since these slip through code reviews anyway) to something that I remember was changed recently.

