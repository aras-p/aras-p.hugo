---
categories:
- unity
- work
comments: true
date: 2009-09-14T15:16:02Z
slug: usability-depends-on-context
status: publish
title: Usability depends on context!
url: /blog/2009/09/14/usability-depends-on-context/
wordpress_id: "387"
---

Here's a little story on how usability decisions need to depend on context.

In Unity editor pretty much any window can be "detached" from the main window. An obvious use case is putting it onto a separate monitor. But of course you can just end up having a ton of detached windows overlapping each other.

Here I have four windows in total on OS X:

[![Overlapped Windows on OS X](http://aras-p.info/blog/wp-content/uploads/2009/09/OSXOverlapped-500x324.jpg)](http://aras-p.info/blog/wp-content/uploads/2009/09/OSXOverlapped.jpg)

Here I have four windows on Windows:

[![Overlapped Windows on Windows](http://aras-p.info/blog/wp-content/uploads/2009/09/WinOverlapped-500x312.jpg)](http://aras-p.info/blog/wp-content/uploads/2009/09/WinOverlapped.jpg)

However, users of OS X and Windows are used to applications behaving differently.

On OS X, it is _very_ common that a single application has many overlapping windows. Usually users don't have problems finding their windows either, thanks to Exposé. Press a key, voilà, here they are:

[![Exposé on OS X](http://aras-p.info/blog/wp-content/uploads/2009/09/OSXExpose-500x316.jpg)](http://aras-p.info/blog/wp-content/uploads/2009/09/OSXExpose.jpg)

On Windows, there is no Exposé. So there's a problem: when a detached window is obscured by another window, how do you get to it? One would ask "well, what's wrong in having windows partially overlapped, like in above screenshot?", to which I'd say "you're a Mac user".

Windows users do not have a ton of windows on screen. They tend to maximize the application they are currently working with. _I was doing this myself_ all the time, and it took 3 years of Mac laptop usage before I stopped maximizing everything on my Windows box!

So what a typical Windows user might see when using Unity is this. Now, where are the other three detached windows?

[![Maximized](http://aras-p.info/blog/wp-content/uploads/2009/09/WinMaximized-500x312.jpg)](http://aras-p.info/blog/wp-content/uploads/2009/09/WinMaximized.jpg)


On Windows, it is _very uncommon_ for a single application to have many overlapped windows. When an application does that, the "detached" windows are always positioned on top of the main window. There are some applications that do not do this (yes I'm looking at you GIMP), and almost everyone is not happy with their usability.

So we decided to take this context into account. Windows users do not have Exposé, _and_ they expect "detached" windows to be always on top of the main window. Unity 2.6 will do this soon.

[![In Front on Windows](http://aras-p.info/blog/wp-content/uploads/2009/09/WinInFront-500x312.jpg)](http://aras-p.info/blog/wp-content/uploads/2009/09/WinInFront.jpg)

Of course, you still can dock all the windows together and this whole "windows are obscured by other windows" issue goes away:

[![Docked on Windows](http://aras-p.info/blog/wp-content/uploads/2009/09/WinDocked-500x312.jpg)](http://aras-p.info/blog/wp-content/uploads/2009/09/WinDocked.jpg)

_Hmm... I think the screenshots above show two new big features in upcoming Unity 2.6. Preemptive note: UI of the stuff above is not final. Anything might change, don't become attached to any particular pixel!_
