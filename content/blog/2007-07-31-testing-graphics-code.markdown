---
tags:
- code
- unity
- work
comments: true
date: 2007-07-31T23:49:45Z
slug: testing-graphics-code
status: publish
title: Testing graphics code
url: /blog/2007/07/31/testing-graphics-code/
wordpress_id: "129"
---

Everyone is saying "unit tests for the win!" all over the place. That's good, but how would you actually test graphics related code? Especially considering all the different hardware and drivers out there, where the result might be different just because the hardware is different, or because the hardware/driver understands your code in a _funky_ way...

Here is how we do it at [work](http://unity3d.com). This took quite some time to set up, but I think it's very worth it.

[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2007/07/test-lab.thumbnail.jpg" title="'Testing Lab in action'">}}](http://aras-p.info/blog/wp-content/uploads/2007/07/test-lab.jpg)First you need **hardware** to test things on. For a start just a couple of graphics cards that you can swap in and out might do the trick. A larger problem is integrated graphics cards - it's quite hard to swap them in and out, so we bit the bullet and bought a machine for each integrated card that we care about. The same machines are then used to test discrete cards (we have several shelves of those by now, going all the way back to... _does ATI Rage, Matrox G45 or S3 ProSavage say anything to you?_).

[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2007/07/test-shots.thumbnail.png" title="'It looks pretty random, huh?'">}}](http://aras-p.info/blog/wp-content/uploads/2007/07/test-shots.png)Then you make the **unit tests** (or perhaps these should be called the functional tests). Build a small scene for every possible thing that you can imagine. Some examples:




  * Do all blend modes work?


  * Do light cookies work?


  * Does automatic texture coordinate generation and texture transforms work?


  * Does rendering of particles work?


  * Does glow image postprocessing effect work?


  * Does mesh skinning work?


  * Do shadows from point lights work?



This will result in a lot of tests, with each test hopefully testing a small, isolated feature. Make some setup that can load all defined tests in succession and take screenshots of the results. Make sure time always progresses at fixed rate (for the case where a test does not produce a constant image... like particle or animation tests), and take a screenshot of, for example, frame 5 for each test (so that some tests have some data to warm up... for example motion blur test).

By this time you have something that you can run and it spits out lots of screenshots. This is already **very useful**. Get a new graphics card, upgrade to new OS or install a new shiny driver? Run the tests, and obvious errors (if any) can be found just by quickly flipping through the shots. Same with the changes that are made in rendering related code - run the tests, see if anything became broken.

[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2007/07/test-perl.thumbnail.png" title="'My crappy Perl code...'">}}](http://aras-p.info/blog/wp-content/uploads/2007/07/test-perl.png)The testing process can be further **automated**. Here we have a small set of Perl scripts that can either produce a suite of test images for the current hardware, or run all the tests and compare the results with "known to be correct" suite of images. As graphics cards are different from each other, the "correct" results will be somewhat different (because of different capabilities, internal precision etc.). So we keep a set of test results for each graphics card.

[{{<imgright src="http://aras-p.info/blog/wp-content/uploads/2007/07/test-drivers.thumbnail.png" title="'That’s an awful lot of drivers!'">}}](http://aras-p.info/blog/wp-content/uploads/2007/07/test-drivers.png)Then these scripts can be run for **various driver versions** on every graphics card. They compare results for each test case, and for failed tests copy out the resulting screenshot, the correct screenshot, log the failures into a wiki-compatible format (to be posted on some internal wiki), etc.

I've heard that some folks even go a step further - fully automate the testing of all driver versions. Install one driver in silent mode, reboot the machine, after reboot runs another script that launches the tests and proceeds with the next driver version. I don't know if that is only an urban legend or if someone actually does this<sup>\*</sup>, but that would be an interesting thing to try. The testing per card then would be: 1) install a card, 2) run the test script, 3) coffee break, happiness and profit!

\* My impression is that at least with the big games it works the other way around - you don't test with the hardware; instead the hardware guys test with your game. That's how it looks for a clueless observer like me at least.

So far this unit test suite was really helpful in a couple of ways: making of the [just-announced](http://unity3d.com/unity/whats-new/unity-2.0) Direct3D renderer and discovering new & exciting graphics card/driver workarounds that we have to do. Making of the suite did take a lot of time, but I'm happy with it!
