+++
tags = ['code', 'rant']
comments = true
date = "2016-12-09T07:38:20+02:00"
title = "Amazing Optimizers, or Compile Time Tests"
+++

I wrote some tests to verify sorting/batching behavior in rendering code, and they were producing different
results on Windows (MSVC) vs Mac (clang). The tests were creating a "random fake scene" with a random number
generator, and at first it sounded like our "get random normalized float" function was returning slightly different results
between platforms _(which would be super weird, as in how come no one noticed this before?!)_.

So I started digging into random number generator, and the unit tests it has. This is what amazed me.

Here's one of the unit tests (we use a custom native test framework that started years ago on an old version of
[UnitTest++](https://unittest-cpp.github.io/)):

``` c++
TEST (Random01_WithSeed_RestoredStateGenerateSameNumbers)
{
	Rand r(1234);
	Random01(r);
	RandState oldState = r.GetState();
	float prev = Random01(r);
	r.SetState(oldState);
	float curr = Random01(r);
	CHECK_EQUAL (curr, prev);
}
```

Makes sense, right?

Here's what MSVC 2010 compiles this down into:

``` asm
push        rbx  
sub         rsp,50h  
mov         qword ptr [rsp+20h],0FFFFFFFFFFFFFFFEh  
	Rand r(1234);
	Random01(r);
	RandState oldState = r.GetState();
	float prev = Random01(r);
movss       xmm0,dword ptr [__real@3f47ac02 (01436078A8h)]  
movss       dword ptr [prev],xmm0  
	r.SetState(oldState);
	float curr = Random01(r);
mov         eax,0BC5448DBh  
shl         eax,0Bh  
xor         eax,0BC5448DBh  
mov         ecx,0CE6F4D86h  
shr         ecx,0Bh  
xor         ecx,eax  
shr         ecx,8  
xor         eax,ecx  
xor         eax,0CE6F4D86h  
and         eax,7FFFFFh  
pxor        xmm0,xmm0  
cvtsi2ss    xmm0,rax  
mulss       xmm0,dword ptr [__real@34000001 (01434CA89Ch)]  
movss       dword ptr [curr],xmm0  
	CHECK_EQUAL (curr, prev);
call        UnitTest::CurrentTest::Details (01420722A0h)
; ...
```

There's some bit operations going on (the RNG is [Xorshift 128](https://en.wikipedia.org/wiki/Xorshift)), looks fine on the first glance.

But wait a minute; this seems like it only has code to generate a random number once, whereas the test is supposed to call `Random01`
three times?!

Turns out the compiler is smart enough to see through some of the calls, folds down all these computations and goes, "yep, so the 2nd call
to `Random01` will produce 0.779968381 (0x3f47ac02)". And then it kinda partially does actual computation of the 3rd Random01 call, and
eventually checks that the result is the same.

Oh-kay!

Now, what does clang (whatever version Xcode 8.1 on Mac has) do on this same test?

``` asm
pushq  %rbp
movq   %rsp, %rbp
pushq  %rbx
subq   $0x58, %rsp
movl   $0x3f47ac02, -0xc(%rbp)   ; imm = 0x3F47AC02 
movl   $0x3f47ac02, -0x10(%rbp)  ; imm = 0x3F47AC02 
callq  0x1002b2950               ; UnitTest::CurrentTest::Results at CurrentTest.cpp:7
; ...
```

Whoa. There's no code left at all! Everything just became an "is 0x3F47AC02 == 0x3F47AC02" test. It became a _compile-time test_.

w(☆o◎)w


By the way, the original problem I was looking into? Turns out RNG is fine _(phew!)_. What got me was code in my own test that
I should have known better about; it was roughly like this:

```c++
transform.SetPosition(Vector3f(Random01(), Random01(), Random01()));
```
See what's wrong?

.

.

.

The function argument evaluation order in C/C++ is [unspecified](http://en.cppreference.com/w/cpp/language/eval_order).

(╯°□°)╯︵ ┻━┻

Newer languages like C# or Java have guarantees that arguments are evaluated from left to right. Yay sanity.
