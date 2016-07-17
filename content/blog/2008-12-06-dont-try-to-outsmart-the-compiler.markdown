---
tags:
- code
comments: true
date: 2008-12-06T23:58:04Z
slug: dont-try-to-outsmart-the-compiler
status: publish
title: Don't try to outsmart the compiler
url: /blog/2008/12/06/dont-try-to-outsmart-the-compiler/
wordpress_id: "245"
---

The other day at work there was a need to flip an image vertically, in a way that did not bring large portions of other code that deals with images. Flipping vertically is easy:


     for( int y = 0; y < height/2; ++y ) {
         memswap( img+y*width, img+(height-y-1)*width, width*img(arr[0]) );
     }


memswap function was done this way:

     // why isnt this in the std lib?
     // using XOR to avoid tmp var
     void memswap( void* m1, void* m2, size_t n )
     {
         char *p = (char*)m1; char *q = (char*)m2;
         while ( n-- ) {
             *p ^= *q; *q ^= *p; *p ^= *q;
             p++; q++;
         }
     }


The comment above the function was what triggered my interest. I just added:

    // because it can be slower (local variable is likely in register;
    // whereas using XOR involves reads/writes to memory)


But then I got interested in this, I just _had to_ check what happens in one or another case.

Using Apple's gcc 4.0.1 on Core 2 Duo, the above memory swapping code takes about 12.5 clock cycles per swapped image pixel (pixel = 4 bytes). The inner loop is this:


     movzx  eax,BYTE PTR [edx-0x1]
     xor    al,BYTE PTR [ecx-0x1]
     mov    BYTE PTR [edx-0x1],al
     xor    al,BYTE PTR [ecx-0x1]
     mov    BYTE PTR [ecx-0x1],al
     xor    BYTE PTR [edx-0x1],al
     dec    ebx
     inc    edx
     inc    ecx
     cmp    ebx,0xffffffff
     jne    loopstart


So the loop is three memory reads, three writes and some increments of the pointers / loop counter. Visual C++ 2008 compiles it very similarly, just uses more complex addressing mode to save one loop counter:


     movzx       edx,byte ptr [ecx+eax] 
     xor         byte ptr [eax],dl 
     mov         dl,byte ptr [eax] 
     xor         byte ptr [ecx+eax],dl 
     mov         dl,byte ptr [ecx+eax] 
     xor         byte ptr [eax],dl 
     dec         esi  
     inc         eax  
     test        esi,esi 
     jne         loopstart



What if we don't do this "XOR trick", and just swap the contents using a temporary variable?


 
     // ...
     char t = *p; *p = *q; *q = t;
     // ...
     



Lo and behold, now it runs at 7 cycles / pixel (almost twice as fast), and the inner loop is two memory reads and two writes:


 
     movzx  edx,BYTE PTR [ebx-0x1]
     movzx  eax,BYTE PTR [ecx-0x1]
     mov    BYTE PTR [ebx-0x1],al
     mov    BYTE PTR [ecx-0x1],dl
     // ... incrementing pointers / counter here, like in previous case
     



So yeah. The XOR trick is pretty much useless here - it's twice as slow. Hey, it can even be slower as images get larger - if tested on a 2048x2048 image, regular swap still takes 7 cycles/pixel, but XOR trick takes 55 cycles/pixel!

I guess XOR trick is useful only in quite rare situations, for example when you're inside of some inner loop and want to swap register values without spilling them to memory or using an additional register. Heh, [Wikipedia has info on this](http://en.wikipedia.org/wiki/XOR_swap_algorithm), so I'm not saying anything new :)

Now of course, if we happen to know that our pixels are 32 bits in size, there's no good reason to keep the loop in bytes. We can operate on integers instead:


 
     void memswapI( void* m1, void* m2, size_t n )
     {
         size_t nn = n/sizeof(int);
         int *p = (int*)m1; int *q = (int*)m2;
         while ( nn-- ) {
             int t = *p; *p = *q; *q = t;
             p++; q++;
         }
     }



This runs at 1.5 cycles/pixel (XOR variant at 2.5 cycles/pixel). The assembly is pretty much the same, just with 32 bit registers.

Another option? If you use STL, just use:


     std::swap_ranges(p, p+n, q);


on the pixel datatype. On 32 bit pixels, this also runs at 1.5 cycles/pixel.

So yeah. Don't try to outsmart the compiler without measuring it.
