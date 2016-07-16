---
layout: page
title: Snowflake Generator
comments: true
sharing: true
footer: true
section: proj
url: projSnowflake.html
---

<P>
In March 2002 <em>Kestutis Tauchkela aka ProNinja</em> announced a "Lithuania's C++ Guru"
coding contest. The task was to write a snowflake image generator. Of course,
there were many discussions: whether this task shows any C++ programming skills
at all, what are the precise requirements (afterall, what is "a good
snowflake"?), etc. However, the contest began.
</P>
<P>
Sadly, there were only two entries... The one presented here was grantly
assigned a first place by jury <em>(that is, Kestutis)</em>.
</P>

<H3>Info</H3>
<P>
What I did was a <em>kind-of</em>-L-System based snowflake generator (and with
some bonus rules and effects for fun). As the contest was called "C++ guru", I
used bits of C++ features (templates, OOP) here and there (mostly where they
were absolutely unneeded).
</P>
<P>
As the program had to work on simple Pentium MMX computer with no 3D accelerator
and other fancy features, I've had a chance to remember old times - doing things
by hand: line drawing, transparency, sorting, z-buffer (actually z-buffer
is not needed, but used for "fun" post-processing filters like image-space edge
detection). It was pretty cool :)
</P>

<H3>Download</H3>
<P>
Binary and source release <A href="files/snowflake.zip"><strong>here</strong></A> (85 KB).
Compiles under MSVC6 (should compile with others as well), runs from the
command line with args (that was the requirement).
</P>

<H3>Some generated images</H3>
<P>
Thumbnails, click for a larger shot.
</P>
<A href="img/snowflake1.jpg"><IMG src="img/tn/snowflake1.jpg"></A>
<A href="img/snowflake2.jpg"><IMG src="img/tn/snowflake2.jpg"></A>
<A href="img/snowflake3.jpg"><IMG src="img/tn/snowflake3.jpg"></A>
<A href="img/snowflake4.jpg"><IMG src="img/tn/snowflake4.jpg"></A>
<A href="img/snowflake5.jpg"><IMG src="img/tn/snowflake5.jpg"></A>
<A href="img/snowflake6.jpg"><IMG src="img/tn/snowflake6.jpg"></A>
