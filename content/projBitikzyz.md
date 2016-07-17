---
layout: page
title: BiTikZyz (2001)
comments: true
sharing: true
footer: true
menusection: proj
url: projBitikzyz.html
---

<IMG src="img/bitikzyz.png" style="float: right"/>
<P>
BiTikZyz is a C++/Java like compiler and bytecode interpreter (aka virtual
machine). It is absolutely non-robust (i.e. it does not perform any checks),
but offers some OOP, decent performance and nice integration with C++.
</P>
<P>
Usage scenario is: you write BiTikZyz classes, compile them and you get
compiled bytecode and C++ stubs for your BiTikZyz classes. So you can just
use them from C++, call their methods and do other stuff. BiTikZyz runtime
provides additional services like instantiation by class name <em>(virtual
constructors or what?)</em>. There's nice support for calling native C++
methods from BiTikZyz: you just declare a method as "native", write normal
C++ code for that method anywhere you like, compile and you've got your
method running!
</P>
<P>
Compiler is written is fairly portable C++, with prebuilt executables for
Win32 and Linux ELF systems.
</P>

<H3>Notes and cautions</H3>
<P>
Although it's fun to play with, I doubt if BiTikZyz could be seriously used as a
scripting language - mainly because it's just a replacement for C/C++ and
offers no robustness or flexibility the other scripting languages (Lua,
Python, Ruby, etc.) provide.
</P>
<P>
Readme files, documentation and source code comments are in Lithuanian. The
compiler can output Lithuanian, English or fancy-language messages
(or you can configure it to your favourite language).
</P>
<P>
The source code is horrible! It was my first C++ program that was over 100
lines long, so I can (sort of) justify myself.
</P>

<H3>Download</H3>
<P>
Binary, source and docs <A href="files/bitikzyz.zip"><strong>here</strong></A> (477 KB).
</P>
