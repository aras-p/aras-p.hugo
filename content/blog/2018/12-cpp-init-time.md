---
title: "C++11 way of initializing integers"
date: 2018-12-20T17:07:10+03:00
tags: ['code', 'rant']
comments: true
---

So [this tweet](https://twitter.com/zeuxcg/status/1075614364579835909) by Arseny Kapoulkine got me interested:

> One of the most annoying changes to C++ that happened in the last decade for me
> personally is the introduction of initialization-using-curly-braces and people starting
> to use it all over the place. There are rare cases when that might make sense,
> but seriously, `int count{ 0 };`?

Now, I *love* the curly-braces initializers in C#, but I haven't used them in C++ much. There might or might not
be good reasons for using them, I don't have an opinion on that aspect. What got me interested, is this:
**how much work for a compiler is there?**. I.e. if you have `int a = 7;` vs `int a { 7 };`, which one is faster to compile?

*Oh come on Aras, who would care about trivial thing like that?!* you say... well, I would care, for one.
I was also recently optimizing compile times of our C++ testing framework, and turns out, once you have
things that are repeating thousands of times (e.g. equality checks in unit tests), even tiny savings do add up.

### Simple test: half a million integer initializations on Clang

At first I did a simple file that would initialize and use half a million integers, and measured the time
it takes for [Clang](https://clang.llvm.org/) to compile that:

```c++
// test.cpp
#ifdef MODERN
#define STUFF { int a {7}; c += a; }
#else
#define STUFF { int a = 7; c += a; }
#endif
#define STUFF1 STUFF STUFF STUFF STUFF
#define STUFF2 STUFF1 STUFF1 STUFF1 STUFF1
#define STUFF3 STUFF2 STUFF2 STUFF2 STUFF2
#define STUFF4 STUFF3 STUFF3 STUFF3 STUFF3
#define STUFF5 STUFF4 STUFF4 STUFF4 STUFF4
#define STUFF6 STUFF5 STUFF5 STUFF5 STUFF5
#define STUFF7 STUFF6 STUFF6 STUFF6 STUFF6
#define STUFF8 STUFF7 STUFF7 STUFF7 STUFF7

#include <stdio.h>
int main()
{
	int c = 0;
	STUFF8; STUFF8; STUFF8; STUFF8; STUFF8; STUFF8; STUFF8; STUFF8;
	printf("got %i\n", c);
	return 0;
}
```

Of course I did not want to *type* half a million integer initializers, so preprocessor to the rescue. Each `STUFFn` expands previous one
four times, and reaching half a million repeats is easy that way. It just ends up being either `{ int a = 7; c += a; }` or `{ int a {7}; c += a; }`
over and over again, and the final value of `c` is printed.

So, let's measure! This is on Intel Core i9 8950HK 2.9GHz (2018 MacBookPro), clang "Apple LLVM version 10.0.0" version (the one in Xcode 10).

* Traditional `time clang -std=c++11 test.cpp`: 8.2 seconds.
* C++11 style curly brace `time clang -std=c++11 -DMODERN test.cpp`: 9.0 seconds. That's **9.7% longer**!

So from this, it would seem that on Clang, using C++11 style initializer-list syntax for simple types *does* create more work for the compiler.
In doing a debug build, Clang spends about 10% more time on the whole compilation (which also includes things like machine code generation, so
the overhead on the frontend itself is larger).

*What about the other compilers? That's where things got a bit more complicated... :)*


### Initializing lots of integers on other compilers

Then I ran the same test on [MSVC](https://en.wikipedia.org/wiki/Microsoft_Visual_C%2B%2B) (VS2017, 15.9.4 version), and... crash with out of memory. Ok I was using the 32 bit `cl.exe`. Trying the
64 bit one, it proceeds to use about 25 gigabytes (!) of memory, and after about 3 minutes of compilation time I just gave up.

Reducing the amount of initializers four times (128 thousand), and testing on AMD ThreadRipper 1950X 3.4GHz:
```c++
#ifdef MODERN
#define STUFF { int a {7}; c += a; }
#else
#define STUFF { int a = 7; c += a; }
#endif
#define STUFF1 STUFF STUFF STUFF STUFF
#define STUFF2 STUFF1 STUFF1 STUFF1 STUFF1
#define STUFF3 STUFF2 STUFF2 STUFF2 STUFF2
#define STUFF4 STUFF3 STUFF3 STUFF3 STUFF3
#define STUFF5 STUFF4 STUFF4 STUFF4 STUFF4
#define STUFF6 STUFF5 STUFF5 STUFF5 STUFF5
#define STUFF7 STUFF6 STUFF6 STUFF6 STUFF6

#include <stdio.h>
int main()
{
	int c = 0;
	STUFF7; STUFF7; STUFF7; STUFF7; STUFF7; STUFF7; STUFF7; STUFF7;
	printf("got %i\n", c);
	return 0;
}
```

* Traditional `ptime cl test.cpp`: 42-44 seconds.
* C++11 style `ptime cl /DMODERN test.cpp`: 42-44 seconds. So, *way*, ***way*** longer than Clang, but there's
  no compile time difference in how the integer is initialized.
  * Clang on Mac with the 4x reduced initializer count takes 2.0s (traditional) and 2.2s (curly brace).


What about [gcc](https://gcc.gnu.org/) then? With half a million initializers, I also could not wait for it to finish. With 128k initializers, on the same ThreadRipper
PC; "traditional" style using `time gcc test.cpp -std=c++11`, and "curly brace" style using `time gcc test.cpp -std=c++11 -DMODERN`:

* gcc 5.4 in WSL: crashes after several minutes in both styles,
* gcc 7.3 in VMWare: traditional 181 sec, curly brace 188 sec. So maybe 4% slower, but I did not do many measurements.

I've also tested Clang 3.8 on WSL and Clang 6.0 in VMWare on the same ThreadRipper PC; timings are consistent with Mac results, i.e.
initializing *a lot* of integers using C++11 curly brace syntax makes compile time about 9% slower.

> Note: I tested the manually-expanded giant C++ file with 128 thousand initializers in it, to figure
> out if it's the macro expansion that is slowing down the compilers. On MSVC, it still takes the same ~42 seconds to compile,
> so the bottleneck is definitely not the macro expansion.

### Takeaways

* Clang seems to be about 9% slower at compiling *a lot* of `int a { 7 };` initializers, compared to traditional `int a = 7;` ones.
* Gcc *might* be about 3-4% slower at compiling the curly brace integer initializers.
* MSVC compiles both forms of integer initialization in the same time.
* Both MSVC and Gcc are *way* slower than Clang at compiling a function that has hundreds of thousands of integer initializers.
  Of course this is not a typical case, but I was still surprised at the compiler either taking 25 gigabytes of memory,
  or outright crashing.
* Most of this does not matter in typical use cases. Unless you're optimizing compile times for something used *extremely* often in a 
  large codebase.
