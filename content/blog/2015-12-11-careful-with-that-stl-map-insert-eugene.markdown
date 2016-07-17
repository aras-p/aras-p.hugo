---
tags: [ code ]
comments: true
date: 2015-12-11T00:00:00Z
title: Careful With That STL map insert, Eugene
url: /blog/2015/12/11/careful-with-that-stl-map-insert-eugene/
---

So we had this pattern in some of our code. Some sort of "device/API specific objects" need to be created
out of simple "descriptor/key" structures. Think [D3D11 rasterizer state](https://msdn.microsoft.com/en-us/library/windows/desktop/ff476516.aspx) or
[Metal pipeline state](https://developer.apple.com/library/ios/documentation/Metal/Reference/MTLRenderPipelineState_Ref/index.html), or something similar to them.

Most of that code looked something like this (names changed and simplified):

``` c++
// m_States is std::map<StateDesc, DeviceState>

const DeviceState* GfxDevice::CreateState(const StateDesc& key)
{
	// insert default state (will do nothing if key already there)
	std::pair<CachedStates::iterator, bool> res = m_States.insert(std::make_pair(key, DeviceState()));
	if (res.second)
	{
		// state was not there yet, so actually create it
		DeviceState& state = res.first->second;
		// fill/create state out of key
	}
	// return already existing or just created state
	return &res.first->second;
}
```

Now, past the initial initialization/loading, absolute majority of CreateState calls will just return already
created states.

`StateDesc` and `DeviceState` are simple structs with just plain old data in them; they can be created on the stack
and copied around fairly well.

**What's the performance of the code above?**

It is O(logN) complexity based on how many states are created in total, that's a given (std::map is a tree,
usually implemented as a red-black tree; lookups are logarithmic complexity). Let's say that's not a problem,
we can live with logN complexity there.

Yes, STL maps are not quite friendly for the CPU cache, since all the nodes of a tree are separately allocated
objects, which could be all over the place in memory. Typical C++ answer is "use a special allocator". Let's say
we have that too; all these maps use a nice "STL map" allocator that's designed for fixed allocation size of a node
and they are all mostly friendly packed in memory. Yes the nodes have pointers which take up space etc., but let's
say that's ok in our case too.

In the common case of "state is already created, we just return it from the map", besides the find cost,
are there any other concerns?

Turns out... **this code allocates memory. _Always (*)_**. And in the major case of state already being in the map,
frees the just-allocated memory too, right there.

> "bbbut... why?! how?"

_(*) not necessarily **always**, but at least in some popular STL implementations it does._

Turns out, quite some STL implementations have map.insert written in this way:

``` c++
node = allocateAndInitializeNode(key, value);
insertNodeIfNoKeyInMap(node);
if (didNotInsert)
	destroyAndFreeNode(node);
```

So in terms of memory allocations, calling map.insert with a key that already exists is more costly (incurs
allocation+free). _Why?!_ I have no idea.


I've tested with several things I had around.

** STLs that always allocate: **

Visual C++ 2015 Update 1:
``` c++
_Nodeptr _Newnode = this->_Buynode(_STD forward<_Valty>(_Val));
return (_Insert_nohint(false, this->_Myval(_Newnode), _Newnode));
```
(`_Buynode` allocates, `_Insert_nohint` at end frees if not inserted).

Same behaviour in Visual C++ 2010.


Xcode 7.0.1 default libc++:
``` c++
__node_holder __h = __construct_node(_VSTD::forward<_Vp>(__v));
pair<iterator, bool> __r = __node_insert_unique(__h.get());
if (__r.second)
    __h.release();
return __r;
```

** STLs that only allocate when need to insert: **

These implementations first do a key lookup and return if found,
and only if not found yet then allocate the tree node and insert it.

Xcode 7.0.1 with (legacy?) libstdc++.

EA's EASTL. See [red_black_tree.h](https://github.com/paulhodge/EASTL/blob/community/include/EASTL/internal/red_black_tree.h).

[@msinilo's](https://twitter.com/msinilo) RDESTL. See [rb_tree.h](https://github.com/msinilo/rdestl/blob/master/rb_tree.h).


** Conclusion? **

STL is hard. Hidden differences between platforms like that can bite you.
Or as [@maverikou](https://twitter.com/maverikou) said, "LOL. this calls for a new emoji".

In this particular case, a helper function that manually does a search, and only insert if needed would
help things. Using a lower_bound + insert with iterator "trick" to avoid second O(logN) search on insert
might be useful. See this [answer on stack overflow](http://stackoverflow.com/a/101980).

Curiously enough, on that (and other similar)
SO threads other answers are along the lines of "for simple key/value types, just calling insert will be as efficient".
Ha. Haha.

