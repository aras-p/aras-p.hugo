---
layout: page
title: D3D Resource Management
comments: true
sharing: true
footer: true
menusection: texts
url: /texts/d3dresources.html
---

<p>
Here I'll try to describe how I do Direct3D resource management. I find this way quite convenient, however, don't take it too seriously. I don't think I'm the first
using the technique described below; and of course there can be better ways.
</p>


<H3>The problems</H3>
<p>
Several problems arise in D3D resource management:
<ul>
	<li>Resources (textures, buffers, etc.) depend on some particular D3D device. If you want to change the device, you have to recreate the resources.</li>
	<li>Some types of resources can be "lost" (those living in default pool) in some situations (eg. fulscreen-windowed switch). These need to be recreated in these
		situations.</li>
	<li>You may want to be able to reload some resources "on the fly" (alt-tab, edit a shader, alt-tab, reload - and you see immediate feedback).</li>
</ul>
It would be cool if all those problems could be solved in some easy and transparent to the application way <em>(you know, "seamlessly integrates" :))</em>.
</p>


<H3>The solution</H3>

<p>In short: <strong>Proxy objects!</strong></p>

<p>More detailed: a <em>proxy object</em> in this case is an object that just holds a pointer to the actual D3D resource object. Pseudocode:
<pre>
struct CD3DTexture {
    IDirect3DTexture9* object;
};
</pre>
Of course, you'll probably use templates/macros to define all those proxy classes (rough list: <em>texture, cube texture, volume texture, surface, index buffer,
vertex buffer, pixel shader, vertex shader, query, state block, vertex declaration</em>; probably some others that proxy D3DX objects, like <em>effect, mesh</em>).
Some of the proxies can contain convenience methods, etc.
</p>
<p>
Now, the <strong>main idea</strong>: the application (and most parts of engine/framework) don't mess with D3D objects directly - all they know is the proxy objects.
Any proxy object is <strong>never recreated or changed</strong>. The application can just store a pointer to such <em>CD3DTexture</em>, and it will be valid at anytime, no matter
if D3D device is changed, lost or some other bad things happen. The texture may even be reloaded - the proxy doesn't change.
Of course, the objects that the proxies <em>refer to</em> may change.
</p>
<p>
Simple, isn't it?
</p>


<h3>The details <em>(and the devil)</em></h3>

<p>
For whole this proxy-thing to work, some code needs to deal will all the details. For D3D, basically we have four situations:
<ol>
	<li>D3D device is created. Here, all the proxy objects that released their resources in 2nd situation must re-create them.</li>
	<li>D3D device is destroyed. All proxy objects must be released by now, either here or in 3rd situation.</li>
	<li>D3D device is lost.</li>
	<li>D3D device is reset after 3rd situation.</li>
</ol>
The first two situations usually affect D3D resources living not in the default pool; and the last two situations - default pool resources. Some types
of resources, for example <em>ID3DXEffect</em> proxies, must deal with all 4 situations (create in 1, release in 2, and call corresponding methods in 3 and 4).
</p>

<p>
For this, I create an abstract interface like this (the details like abstract virtual destructors are omitted):
<pre>
struct IDeviceResource {
    virtual void createResource() = 0;    // 1
    virtual void activateResource() = 0;  // 4
    virtual void passivateResource() = 0; // 3
    virtual void deleteResource() = 0;    // 2
};
</pre>
</p>

<p>
Then, all resource management is centralized in some "resource managers" that do loading/creating resources on demand <em>and</em> implement this
<em>IDeviceResource</em> interface. The resource managers are registered into some single "notifier" that calls <em>IDeviceResource</em> methods in response
to the above four situations.
</p>

<p>
A simple resource manager can perform an operation like "here's the resource ID, give me the resource". Usually
it contains a (resource ID->proxy) map, so it can return the same physical resource for the same ID (so only the first query actually loads it, the rest
just look it up in the map). Resource ID usually somehow indicates the file name where the resource is.
</p>

<p>
A basic resource manager, for example, a texture manager (static textures loaded from storage, in managed pool),
deals with this like:
<pre>
<em>// internal method: load and create basic, non-proxied texture</em>
IDirect3DTexture9* CTextureBundle::loadTexture( resourceID ) { /* ... */ }

<em>// public method: return the texture proxy given texture ID</em>
CD3DTexture* CTextureBundle::getResourceById( resourceID ) {
  if id->proxy map contains proxy for given resourceID {
    return it;
  } else {
    create new proxy P;
    P.object = loadTexture( resourceID );
    insert (resourceID->P) into id->proxy map;
  }
}

<em>// IDeviceResource: re-load all textures into existing proxies</em>
void CTextureBundle::createResource() {
  for each element P in id->proxy map {
    P.proxy = loadTexture( P.resourceID );
  }
}
<em>// IDeviceResource: release all textures</em>
void CTextureBundle::deleteResource() {
  for each element P in id->proxy map {
    P.proxy.object->Release(); // release the D3D texture
    P.proxy.object = NULL;
  }
}
<em>// IDeviceResource: nothing to do in these</em>
void CTextureBundle::activateResource() { }
void CTextureBundle::passivateResource() { }
</pre>
</p>


<h3>Making it more complex</h3>
<p>
Simple loaded resources are easy, you say - but what about resources that are created procedurally or are modified versions of the loaded resources?
</p>

<p>
Let's take textures created by the application (for rendertargets, procedurally computed textures and the like) for example.
While in above case only simple "resource ID" is enough to create the resource at any time - this is more complex. A created texture must have some unique ID
<strong>and</strong> all the information that enabled you to create it. I do it like this - have an abstract interface "texture creator":
<pre>
struct ITextureCreator {
  virtual IDirect3DTexture9* createTexture() = 0;
};
</pre>
Some realization of this interface, that creates rendertargets proportional to backbuffer size, might look like this:
<pre>
class CScreenBasedTextureCreator : public ITextureCreator {
public:
  CScreenBasedTextureCreator( float widthFactor, float heightFactor,
      int levels, DWORD usage, D3DFORMAT format, D3DPOOL pool );
  virtual IDirect3DTexture9* createTexture();
private:
  // members
};
</pre>
</p>

<p>
A resource manager for such textures stores a map that maps resource ID to the proxy <em>and</em> this <em>ITextureCreator</em>. The manager also
has a method like "here's the ID and the creator object, please register this resource". So, whole texture manager might look like this:
<pre>
void CSharedTextureBundle::registerTexture( resourceID, ITextureCreator* creator ) {
  create new proxy P;
  P.object = creator->createTexture();
  insert ( resourceID -> pair(P,creator) ) into the map;
}

<em>// public method: return the texture proxy given texture ID</em>
CD3DTexture* CSharedTextureBundle::getResourceById( resourceID ) {
  <em>// the resource map must contain the ID - else it's error</em>
  return proxy from the resourceID->(proxy,creator) map;
}
</pre>
The rest is pretty similar, but this time some textures may be in default pool (those are dealt with in <em>activateResource/passivateResource</em>).
</p>

<p>
Using this technique, application registers the rendertargets/procedural textures at start of day (or at some other time), and then can look them up or just
store the pointers to the proxies (remember, the proxies never change :)).
</p>


<h3>The caveats</h3>
<p>
All above assumes that either the application uses proxies only at "right" time moments (eg. between the device loss and reset the application doesn't do anything,
just the needed resources are released and recreated), or it manually checks for NULL objects in proxies and knows how to handle that.
</p>
<p>
Some resources may depend on the other resources. For example, meshes might want to store their vertex declarations; procedural meshes may want to be initialized
from stored meshes, etc. So it's important to order the notifications to the resource managers carefully.
</p>


<h3>Some alternatives</h3>
<p>
The "resource ID" isn't necessarily a string. For example, my current vertex declaration manager takes a "vertex/stream format" as a resource ID, and just
constructs D3D vertex declarations from that.
</p>
<p>
The managers for both loaded and created resources can be unified - afterall, loading from files is just another type of <em>ITextureCreator</em> in
textures case. Some thought is needed on how to avoid "register resource" for loaded resources...
</p>


<h3>The hidden part</h3>
<p>
If you look closely, my <a href="../projShaderey.html">Shaderey</a> demo sourcecode contains quite much of whole this stuff (not the most
recent version though). A really up-to-date version can be found at my demo/game engine <a href="http://dingus.berlios.de"><strong>dingus</strong></a> website.
</p>

<p>
If I've written complete nonsense here <em>(or described your own patented technique :))</em>, feel free to drop me a
<a href="mailto:nearaz_at_gmail_dot_com">mail</a>.
</p>
