---
categories:
- gpu
- papers
- rendering
- work
comments: true
date: 2009-09-17T09:59:01Z
slug: strided-blur-and-other-tips-for-ssao
status: publish
title: Strided blur and other tips for SSAO
url: /blog/2009/09/17/strided-blur-and-other-tips-for-ssao/
wordpress_id: "409"
---

If you're new to SSAO, here are good overview blog posts: [meshula.net](http://meshula.net/wordpress/?p=145) and [levelofdetail](http://levelofdetail.wordpress.com/2008/02/10/2007-the-year-ssao-broke/). Some tips and an idea on strided blur below.

**Bits and pieces I found useful**


* SSAO can be generated at a smaller resolution than screen, with depth+normals aware upsample/blur step.

* If random offset vector points away from surface normal, flip it. This makes random vectors be in the upper hemisphere, which reduces false occlusion on flat surfaces. Of course this requires having surface normals.

* When generating random vectors for your AO kernel:	
	
    * Generate vectors _inside_ unit sphere (not _on_ unit sphere).
    * Use energy minimization to distribute your samples better, especially at low sample counts. See [malmer.ru](http://www.malmer.nu/index.php/2008-04-11_energy-minimization-is-your-friend) blog post.

* In your AO blurring/upsampling step: no need to sample each pixel for blur. Just skip some of them, i.e. make kernel offsets larger. See below.




**Strided blur for AO**

Normally you'd blur AO term using some sort of standard blur, for example separable Gaussian: horizontal blur, followed by vertical blur. How one can imagine horizontal blur kernel:

[![Horizontal Blur Kernel](http://aras-p.info/blog/wp-content/uploads/2009/09/blur1.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/blur1.png)

Here's how [Rune](http://runevision.com/) taught me how to blur better:


>**Rune:** The other thing is the blur. I tried to make the blur 4 times stronger, and it looks much better IMO without any artifacts I could see. I could even use 4x downsampling with that blur amount and still get acceptable results.
>
>**Aras:** how did you make it 4x stronger? _(I was going to say that blur step is already quite expensive, and I don't want to add more samples to make it even more expensive, yadda yadda)_
>
>**Rune:**  
>    m_SSAOMaterial.SetVector ("_TexelOffsetScale", m_IsOpenGL ?  
>	 new Vector4 (**4**,0,1.0f/m_Downsampling,0) :  
>	 new Vector4 (**4.0f**/source.width,0,0,0));  
> And similar for vertical.
>
>**Aras:** hmm. that's strange :)
>
>**Rune:** I have no idea what I'm doing of course but it looks good.
>
>**Aras:** so this way it does not do Gaussian on 9x9 pixels, but instead only takes each 4th pixel. Wider area, but... it should not work! :)
>
>**Rune:** It creates a very fine pattern at pixel level but it's way more subtle than the noise you get otherwise.
>
>**Aras:** ok _(hides in the corner and weeps)_ 





So yeah. The blur kernel can be "spread" to skip some pixels, effectively resulting in a larger blur radius for the same sample count:

[![Blur with 2 pixel stride](http://aras-p.info/blog/wp-content/uploads/2009/09/blur2.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/blur2.png)

Or even this:

[![Blur with 3 pixel stride](http://aras-p.info/blog/wp-content/uploads/2009/09/blur3.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/blur3.png)


Yes, it's not correct blur. **But that's okay**, we're not building nuclear reactors that depend on SSAO blur being accurate. _If you are, SSAO is probably a wrong approach anyway, I've heard it's not that useful for nuclear stuff_.

I'm not sure how this blur should be called. Strided blur? Interleaved blur? Interlaced blur? Or maybe everyone is doing that already and it has a well established name? Let me know.

Some images of blur in action. Raw AO term (very low - 8 - sample count and increased contrast on purpose):

[![Raw AO at low sample count](http://aras-p.info/blog/wp-content/uploads/2009/09/AO1raw-500x270.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/AO1raw.png)

Regular 9x9 blur (does not blur over depth+normals discontinuities):

[![Blurred AO](http://aras-p.info/blog/wp-content/uploads/2009/09/AO2blur-500x270.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/AO2blur.png)

Blur that goes in 2 pixel stride (effectively 17x17):

[![Blurred AO with stride 2](http://aras-p.info/blog/wp-content/uploads/2009/09/AO3blur2-500x271.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/AO3blur2.png)

It does create a fine interleaved pattern because it skips pixels. But you get wider blur!

[![Blurred AO with stride 2, magnified](http://aras-p.info/blog/wp-content/uploads/2009/09/AO3blur2mag.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/AO3blur2mag.png)

Blur that goes in 3 pixel stride (effectively 25x25):

[![Blurred AO with stride 3](http://aras-p.info/blog/wp-content/uploads/2009/09/AO4blur3-500x269.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/AO4blur3.png)

At 3 pixel stride the artifacts are becoming apparent. But hey, this is very
low AO sample count, increased contrast and no textures in the scene.

[![Blured AO with stride 3, magnified](http://aras-p.info/blog/wp-content/uploads/2009/09/AO4blur3mag.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/AO4blur3mag.png)


For sake of completeness, the same raw AO term, but computed at 2x2 smaller resolution (still using low sample count etc.):

[![AO computed at lower resolution](http://aras-p.info/blog/wp-content/uploads/2009/09/AO5down2-500x270.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/AO5down2.png)

Now, 2x2 smaller AO, blurred with 3 pixels stride:

[![AO at lower resolution, blurred with 3 pixel stride](http://aras-p.info/blog/wp-content/uploads/2009/09/AO6down2blur3-499x272.png)](http://aras-p.info/blog/wp-content/uploads/2009/09/AO6down2blur3.png)

Happy blurring!
