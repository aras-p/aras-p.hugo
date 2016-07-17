---
tags:
- code
comments: true
date: 2013-08-21T00:00:00Z
title: 'Inter-process Communication: How?'
url: /blog/2013/08/21/inter-process-communication/
---

*A post of mostly questions, and no answers!*

So I needed to do some IPC ([Inter-process Communication](http://en.wikipedia.org/wiki/Inter-process_communication)) lately for shader compilers. There are several reasons why you'd want to move some piece of code into another process; in my case they were:

* Bit-ness of the process; I want a 64 bit main executable but some of our platforms have only 32 bit shader compiler libraries.
* Parallelism. For example you can call NVIDIA's Cg from multiple threads, but it will just lock some internal mutex for most of the shader compilation time. Result is, you're trying to compile shaders on 16 cores, but they end up just waiting on each other. By running 16 processes instead, they are completely independent, and the shader compiler does not even have to be thread-safe ;)
* Memory usage and fragmentation. This is less of an issue in 64 bit land, but in 32 bit it helps to put some stuff into separate process with its own address space.
* Crash resistance. A crash or memory trasher in a shader compiler should not bring down whole editor.

Now of course, all that comes with a downside: IPC is much more cumbersome than just calling a function in some library directly. So I'm wondering - **how people do that in C/C++ codebases**?

*(I'm getting flashbacks of [CORBA](http://en.wikipedia.org/wiki/Common_Object_Request_Broker_Architecture) from my early enterprisey days... but hey, that was last millenium, and seemed like a good idea at the time...)*


** Transport layer? **

So there's a question of over what medium the processes will communicate?

* Files, named pipes, sockets, shared memory?
* Roll your own code for one of the above?
* Use some larger libraries like [libuv](https://github.com/joyent/libuv), [0MQ](http://zeromq.org/), [nanomsg](http://nanomsg.org/) or *(shudder)* [boost::asio](http://www.boost.org/doc/libs/1_54_0/doc/html/boost_asio/overview.html)?

What I do right now is just some code for named pipes (on Windows) and stdin/stdout (on Unixes). We already had some code for that lying around anyway.


** Message protocol? **

And then there's a question, how do you define the "communication protocol" between the processes. Ease of development, need (or no need) for backward/forward compatibility, robustness in presence of errors etc. all come into play.

* Manually written, some binary message format?
* Manually written, some text/line based protocol?
* JSON, XML, YAML etc.?
* Helper tools like [protobuf](https://code.google.com/p/protobuf/) or [Cap'n Proto](http://kentonv.github.io/capnproto/)?

Right now I'm having a manually-written, line-based message format. But it's quite a laborous process to write all that communication code, especially when you also want to do some error handling. It's not hard, but stupid boring work, and high chance of accidental bugs due to ~~bored programmer copy-pasting nonsense~~ me.

Maybe I should use protobuf? (looked at Cap'n Proto, but can't afford to use C++11 compilers yet)

Am I missing some easy, turnkey solution for IPC in C/C++?
