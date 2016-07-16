---
categories:
- code
comments: true
date: 2012-07-09T00:00:00Z
title: Careful with That Initializer, Eugene
url: /blog/2012/07/09/careful-with-that-initializer/
---

I was profiling something, and noticed that `HighestBit()` was taking suspiciously large amount of time. So I looked at the code.
It had some platform specific implementations, but the cross-platform fallback was this:

``` c
// index of the highest bit set, or -1 if input is zero
inline int HighestBitRef (UInt32 mask)
{
	int base = 0;
	if ( mask & 0xffff0000 )
	{
		base = 16;
		mask >>= 16;
	}
	if ( mask & 0x0000ff00 )
	{
		base += 8;
		mask >>= 8;
	}
	if ( mask & 0x000000f0 )
	{
		base += 4;
		mask >>= 4;
	}
	const int lut[] = {-1,0,1,1,2,2,2,2,3,3,3,3,3,3,3,3};
	return base + lut[ mask ];
}
```

Not the best implementation of the functionality, but probably not the worst either. Takes three branches, and then a small look-up table.

_Notice anything suspicious?_

Let's take a look at the assembly (MSVC 2010, x86).


``` nasm
; int HighestBitRef (UInt32 mask)
push        ebp  
mov         ebp,esp  
sub         esp,44h  
mov         eax,dword ptr [___security_cookie] ; MSVC stack-smashing protection
xor         eax,ebp  
mov         dword ptr [ebp-4],eax  
; int base = 0;
mov         ecx,dword ptr [ebp+8]  
xor         edx,edx  
; if ( mask & 0xffff0000 )
test        ecx,0FFFF0000h  
je          _lbl1
mov         edx,10h  ; base = 16;
shr         ecx,10h  ; mask >>= 16;
_lbl1: ; if ( mask & 0x0000ff00 )
test        ecx,0FF00h  
je          _lbl2
add         edx,8  ; base += 8;
shr         ecx,8  ; mask >>= 8;
_lbl2: ; if ( mask & 0x000000f0 )
test        cl,0F0h  
je          _lbl3
add         edx,4  ; base += 4;
shr         ecx,4  ; mask >>= 4;
_lbl3:
; const int lut[] = {-1,0,1,1,2,2,2,2,3,3,3,3,3,3,3,3};
mov         eax,1  
mov         dword ptr [ebp-3Ch],eax  
mov         dword ptr [ebp-38h],eax  
mov         eax,2  
mov         dword ptr [ebp-34h],eax  
mov         dword ptr [ebp-30h],eax  
mov         dword ptr [ebp-2Ch],eax  
mov         dword ptr [ebp-28h],eax  
mov         eax,3  
mov         dword ptr [ebp-24h],eax  
mov         dword ptr [ebp-20h],eax  
mov         dword ptr [ebp-1Ch],eax  
mov         dword ptr [ebp-18h],eax  
mov         dword ptr [ebp-14h],eax  
mov         dword ptr [ebp-10h],eax  
mov         dword ptr [ebp-0Ch],eax  
mov         dword ptr [ebp-8],eax  
mov         dword ptr [ebp-44h],0FFFFFFFFh  
mov         dword ptr [ebp-40h],0  
; return base + lut[ mask ];
mov         eax,dword ptr [ebp+ecx*4-44h]  
mov         ecx,dword ptr [ebp-4]  
xor         ecx,ebp  
add         eax,edx  
call        functionSearch+1 ; MSVC stack-smashing protection
mov         esp,ebp  
pop         ebp  
ret  
```

Ouch. **It is creating that look-up table**. Each. And. Every. Time.

Well, the code asked for that: `const int lut[] = {-1,0,1,1,2,2,2,2,3,3,3,3,3,3,3,3}`, so the compiler does exactly what it was told.
Could the compiler be smarter, notice that the table is actually always constant, and put that into the data segment? 
*"I would if I was a compiler, and I'm not even smart!"* The compiler could do this, I guess, but it does not *have to*. More often
than not, **if you're expecting the compiler to "be smart", it will do the opposite**.

So the above code, it fills the table. This makes the function long enough that the compiler decides to not inline it. And since it's
filling up some table on the stack, MSVC's "stack protection" code bits come into play (which are on by default), making the code even longer.

I've done a quick test and timed how long does this take: `for (int i = 0; i < 100000000; ++i) sum += HighestBitRef(i);` on a
Core i7-2600K @ 3.4GHz... **565 milliseconds**.



The fix? Do not initialize the lookup table each time!

``` c
const int kHighestBitLUT[] = {-1,0,1,1,2,2,2,2,3,3,3,3,3,3,3,3};

inline int HighestBitRef (UInt32 mask)
{
	// ...
	return base + kHighestBitLUT[ mask ];
}
```

Note: I could have just put in a `static const int lut[]` in the original function code. But that _sounds_ like this might not be thread-safe
(at least similar initialization of more complex objects isn't; not sure about array initializers). A quick test with MSVC2010 reveals that it is thread-safe, but I wouldn't want to rely on that.

How much faster now? 298 milliseconds if explicitly non-inlined, **110 ms** when inlined. **Five times faster** by moving one line up!
For completeness sake, using MSVC `_BitScanReverse` intrinsic (`__builtin_clz` in gcc), which compiles down to x86 `BSR` instruction, takes 94 ms in the same test.

So... yeah. Careful with those initializers.
