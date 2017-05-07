+++
tags = ['random', 'rants', 'code']
comments = true
date = "2017-05-07T10:53:12+03:00"
title = "User's POV and Empathy"
+++

Recently someone at work said _"hey Aras, you write great feature overview docs, what are the
tips & tricks to do them"_ and that got me thinking... The only obvious one I have is:

**Imagine what a user would want to know about _{your thing}_, and write it down**.

I had some more detail in there, e.g. when writing a feature overview or "proposal for a thing", write down:

1. How do things work right now, what are current workflows of achieving this and what are the problems with it.
1. How will this new feature solve those problems or help in other ways.
1. Write down what a user would need to know. Including use-cases, examples and limitations.


Now, that is simple enough. But looking at a bunch of initial docs, release notes, error messages,
UI labels and other user-facing things, both at Unity and elsewhere, it's presumably not _that obvious_
to everyone :)


### Random Examples

#### Release Notes

[{{<imgright width="150" src="/img/blog/2017-05/empathy-relnotes-hg34.png">}}](/img/blog/2017-05/empathy-relnotes-hg34.png)

[Mercurial](https://www.mercurial-scm.org/) DVCS used to have release notes that were incomprehensible
(to me as a user at least). For example here, take a look at
[hg 3.4 notes](https://www.mercurial-scm.org/wiki/WhatsNew#Mercurial_3.4_.282015-05-01.29). What does
_"files: use ctx object to access dirstate"_ or _"dirstate: fix order of initializing nf vs f"_ mean? Are these even words?!

Even [git](https://git-scm.com/), which I think is a somewhat _Actively Hostile To The User_<sup>(*)</sup> version control
system, had better release notes. Check out git
[2.12.0 notes](https://raw.githubusercontent.com/git/git/master/Documentation/RelNotes/2.12.0.txt) for example.

_(*) I know, "Yeah, well, that's just, like, your opinion, man" etc._

[{{<imgright width="150" src="/img/blog/2017-05/empathy-relnotes-hg41human.png">}}](/img/blog/2017-05/empathy-relnotes-hg41human.png)

Thankfully, since Mercurial 3.7 they started writing "[feature overview](https://www.mercurial-scm.org/wiki/Release4.1)"
pages for each release, which helps
a lot to point out major items in actual human language. Now I can read it and go "oh this looks sweet, I should upgrade"
instead of "I know the words, but the sentences don't mean anything. Eh, whatever!".

Yes, this does mean that you can't just dump a sorted list of all source control commit messages and call them release notes.
Someone has to sit down and write an overview. However, your users will thank you for that!


#### Pull Requests

A [pull request](https://en.wikipedia.org/wiki/Distributed_version_control#Pull_requests) or a code review request
asks other people to look at your code. They have to spend _their time_ doing that! Time they could perhaps spend doing
something else. If you can spend five minutes making their job easier, that is often very much worth it.

I used to review _a lot_ of code in 2015/2016 (~20 PRs each week), and the "ugghhh, not this again" reaction was whenever
I saw pull requests like this. In each case, that single sentence is the only thing in PR description, with no further info
besides list of commits & code diff:

* "Updates to 2D framework + all new animation window full of win". 374 commits, 100 files changed, 8000 lines of code.
* "Latest MT rendering work". 64 commits, 95 files, 3000 lines of code.
* "Multithreaded rendering refactor". 119 commits, 79 files, 4000 lines of code.
* "UWP Support". 224 commits, 219 files, 7000 lines of code.
* "Graphics jobs (preliminary)". 114 commits, 112 files, 2000 lines of code.

Seriously. You just spent several months doing all that work, and have one sentence to describe it?!

_Sometimes_ just a list of commit messages is enough to describe pull request contents for the reviewers (this is mostly true
for PRs that are a bunch of independent fixes; with one commit per fix). If that is the case, say that in the PR description!
The above list of PR examples were _very much not_ this case though :)

What would be good PR descriptions that make reviewer's job significantly easier? Here's some good ones I've seen:

[{{<img width="230" src="/img/blog/2017-05/empathy-pr-good-d3d12.png">}}](/img/blog/2017-05/empathy-pr-good-d3d12.png)
[{{<img width="230" src="/img/blog/2017-05/empathy-pr-good-particles.png">}}](/img/blog/2017-05/empathy-pr-good-particles.png)
[{{<img width="230" src="/img/blog/2017-05/empathy-pr-good-scripting.png">}}](/img/blog/2017-05/empathy-pr-good-scripting.png)
[{{<img width="230" src="/img/blog/2017-05/empathy-pr-good-serialize.png">}}](/img/blog/2017-05/empathy-pr-good-serialize.png)
[{{<img width="230" src="/img/blog/2017-05/empathy-pr-good-metal.png">}}](/img/blog/2017-05/empathy-pr-good-metal.png)
[{{<img width="230" src="/img/blog/2017-05/empathy-pr-good-jobqueue.png">}}](/img/blog/2017-05/empathy-pr-good-jobqueue.png)
[{{<img width="230" src="/img/blog/2017-05/empathy-pr-good-shaderserialize.png">}}](/img/blog/2017-05/empathy-pr-good-shaderserialize.png)


_How do you make more PRs have good descriptions?_

Often I would go and poke PR authors asking for the description, especially for large ones. Something like
"Wow this is a big PR! Can you write up a summary of changes in the description; would make reviewing it
much easier".

Another thing we did was make our pull request tool pre-fill the PR description field:

> **Purpose of this PR**:
>
> [Desc of feature/change. Links to screenshots, design docs, user docs, etc. Remember
> reviewers may be outside your team, and not know your feature/area that should be explained more.]
>
> **Testing status**:
>
> [Explanation of what's tested, how tested and existing or new automation tests. Can include
> manual testing by self and/or QA. Specify test plans. Rarely acceptable to have no testing.]
>
> **Technical risk**:
>
> [Overall product level assessment of risk of change. Need technical risk & halo effect.]
>
> **Comments to reviewers**:
> 
> [Info per person for what to focus on, or historical info to understand who have previously
> reviewed and coverage. Help them get context.]

This simple change had a _massive impact_ on quality of PR descriptions. Reviewers are now more happy and less grumpy!


#### Commit Messages and Code Comments

A while ago I was looking at some screen resolution handling code, and noticed that some sequence of operations
was done in a different order on DX11 compared to DX9 or OpenGL. Started to wonder why, turns out I myself
have changed it to be different... five years ago... with a commit message that says "fixed resolution switches"
(100 lines of code changed).

_Bad Aras, no cookie!_

Five years later (of heck, even a few months later) you yourself will not remember what case exactly this was fixing.
Either add that info into the commit message, or write down comments on tricky parts of code. Or both.
_Especially_ in code that is known to be hard to get right (and anything involving resolution
switches on Windows is). Yes, sometimes it does make sense to write ten lines of comments for a single line
of code. Future you, or your peers, or anyone who will look into this code will thank you for that.

I know that since I have had "pleasure" of maintaining my own code ten years later :) Cue "...and if you tell that to the
young people today, they won't believe you..." [quote](https://en.wikipedia.org/wiki/Four_Yorkshiremen_sketch).

Any time someone might be wondering "why is this done?" about a piece of code, write it down. My own code these days often
has comments like:

```
// All "folders" in the solution are represented as "fake projects" too;
// with a special parent GUID and instead of pointing to vcxproj files they
// just point to their own names.
var foldersParentGuid = "2150E333-8FDC-42A3-9474-1A3956D46DE8";
```
or
```
// VS filters file needs filter elements to exist for the whole folder hierarchy
// (e.g. if we have "Editor\Platform\Windows", we need to have "Editor\Platform",
// and "Editor" too). Otherwise it will silently skip grouping files into filters
// and display them at the root.
var parentFolders = allFolders.SelectMany(f => f.RecursiveParents).Where(p => p.Depth > 0).ToArray();
allFolders.UnionWith(parentFolders);

// VS seems to load projects a bit faster if file/folder lists are sorted
var sortedFolders = allFolders.OrderBy(f => f); 
```

If anything else, I write these for future myself.


### Empathy

What all the above has in common?

Empathy.

Putting yourself into position of the reader, user, colleague, future self, etc. What do _they_ want
to know? What makes _their_ job/task/life easier?

[Mikko Mononen](https://twitter.com/mikkomononen) once showed these amazing resources:

* [**The Paradox of Empathy**](https://jenson.org/paradox/) by Scott Jenson.
* [**Gel 2009 talk**](https://vimeo.com/9198586) by magician Jamy Ian Swiss.

I'm not sure what conclusion I wanted to make, so, ehhh, that's it. Read the resources above!
