---
tags:
- code
comments: true
date: 2005-02-07T10:55:00Z
slug: linear-programming-redefined
status: publish
title: Linear programming, redefined
url: /blog/2005/02/07/linear-programming-redefined/
wordpress_id: "8"
---

I'd like to redefine the term ['linear programming](http://en.wikipedia.org/wiki/Linear_programming)['](http://en.wikipedia.org/wiki/Linear_programming). No, it's not about optimization problems; instead it's about programming style. You know you deal with linear programming when:



  
* You see a function that's 6 pages long. It's been programmed _linearly_, literally. Similar things: a try-catch block of several pages, in C++, that catches everything and prints just "error occured" into log.

* You see 6 functions that each is pretty long, and the differences between them are a couple of lines.
  
* In a big project, you find 10 long functions that are _exactly_ the same, each do _exactly_ the same thing, but are defined in 10 different places/modules.

The other name for it could be 'copy-paste programming', except for the 1st point, where everything is _coded_ linearly.

I tend to find lots of linear programming at work.
