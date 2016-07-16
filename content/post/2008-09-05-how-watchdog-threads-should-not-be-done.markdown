---
categories:
- code
- work
comments: true
date: 2008-09-05T11:48:22Z
slug: how-watchdog-threads-should-not-be-done
status: publish
title: How watchdog threads should NOT be done...
url: /blog/2008/09/05/how-watchdog-threads-should-not-be-done/
wordpress_id: "207"
---

Here, a thread function that checks whether some tool got stuck:



     static void WatchdogFunc()
     {
         while( true )
         {
             time_t now = time(NULL);		
             Mutex::AutoLock lock(g_WatchdogMutex);
             if( now - g_StartTime > kWatchdogTimeout )
                 ComplainLoudlyAndDoSomething();
             Thread::Sleep( 0.1f );
         }
     }



Mutex is taken because g_StartTime can be occasionally updated by the same tool. Yes, possibly a mutex is an overkill here, and aligned variable + some memory fences should be enough (or just nothing), but hey, this is some random offline tool code.

What is horribly wrong with it?

Mutex is held locked for the whole duration of Sleep! That is, almost all the time; and other thread(s) barely have a chance to ever update g_StartTime.

And this is the code I've written. Oh stupid me.
