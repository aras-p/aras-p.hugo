---
categories:
- code
- random
- rant
- work
comments: true
date: 2008-12-11T18:16:05Z
slug: achievement-of-the-week-makevistadwmhappydance
status: publish
title: 'Achievement of the week: MakeVistaDWMHappyDance'
url: /blog/2008/12/11/achievement-of-the-week-makevistadwmhappydance/
wordpress_id: "247"
---

This was the function that I added:


     void GUIView::MakeVistaDWMHappyDance()
     {
         // Looks like Vista has some bug in DWM. Whenever we maximize or dock
         // a view, we must do something magic, otherwise
         // white stuff appears in place of the view.
         // See http://forums.microsoft.com/MSDN/ShowPost.aspx?PostID=4208117&SiteID;=1
         	
         bool earlierThanVista = systeminfo::GetOperatingSystemNumeric() < 600;
         if( earlierThanVista )
             return;
     
         // What seems to work is drawing one pixel via GDI.
         // We draw it at (1,1) with usual background color.
         int grayColor = 0.61f * 255.0f;
         PAINTSTRUCT ps;
         BeginPaint(m_View, &ps;);
         SetPixel(ps.hdc, 1, 1, RGB(grayColor,grayColor,grayColor));
         EndPaint(m_View, &ps;);
     }



I know. Reading from screen when Aero is on is slow, bad and wrong. But then, what do you do? It's better than users staring an all-white window just because Vista decided to draw it white, no matter what you think you're drawing into it.

...still, `MakeVistaDWMHappyDance` is not nearly as cool as 


    internal interface ICanHazCustomMenu { ... }


that Nicholas added a while ago.
