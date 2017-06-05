+++
tags = ['energy']
comments = true
date = "2017-06-04T22:53:43+03:00"
title = "Solar Roof"
+++

I had some solar panels installed on my roof, so here's a post with graphs & numbers & stuff!

*TL;DR*: should more or less cover my electricity usage; easy setup; more involved paperwork;
cost about 5k€ (should get 30% of that back someday due to government incentives);
expected ROI 10 years; panels themselves are less than half of the cost today.

#### Setup and Paperwork

[{{<imgright width="250" src="/img/blog/2017-06/solar-pic.jpg">}}](/img/blog/2017-06/solar-pic.jpg)

Sometime this winter, I had a small array of 12 solar panels (about 20 square meters;
3.12kWp, "kWp" is maximum power rating in ideal conditions) setup on my roof. Besides
the panels, a power inverter has to be set up (panels generate direct current). I had
it slapped on a wall in my garage.

The setup was fairly impressive - basically one dude did it all by himself in six hours.
Including getting the panels onto the roof. Efficient!

[{{<imgright width="250" src="/img/blog/2017-06/solar-inverter.jpg">}}](/img/blog/2017-06/solar-inverter.jpg)

And then came the paperwork... That took about three months at
[Vogon](https://en.wikipedia.org/wiki/Vogon) pace; every little piece of paper
had to be *signed in triplicate, sent in, sent back, queried, lost, found, subjected to
public inquiry, lost again, and finally buried in soft peat for three months
and recycled as firelighters*. Ok it's not that bad; most of the paper
busywork was done by the company that is doing the solar setup (I just had to sign an
occasional document). But still, the whole chain of "setting up solar roof requires
20 paperwork steps" means that all these execute in a completely serial fashion and
take three months.

Which means I could only "turn on" the whole thing a month ago.


#### How does it work?

The setup I have is a two-way electricity accounting system, where whatever the panels
generate I can use immediately. If I need more power (or the sun is not shining), I use
more from the regular grid. If I happen to generate more than I need, the surplus goes back
into the grid for "storage" that I can recoup later (during the night, or during winter etc.).
I pay about 0.03€ for kWh stored that way (regular grid electricity price is about 0.11€); the
grid essentially works as a battery.

A two-way meter is setup by the energy company that tracks both the power used & power
given back to the grid.

This setup is fairly new here in Lithuania; they only started doing this a couple years ago.
I've no idea how it is in other countries; you'll have to figure that out yourself!


#### Show me the graphs!

On a bright sunny day, the solar production graph *almost* follows the expected `dot(N,L)`
curve :)

[{{<img width="600" src="/img/blog/2017-06/solar-sunny.png">}}](/img/blog/2017-06/solar-sunny.png)

It generated a total of 21.4 kWh on that day. On a very cloudy/rainy day, the graph looks like
this though (total 7.5 kWh generated):

[{{<img width="600" src="/img/blog/2017-06/solar-rainy.png">}}](/img/blog/2017-06/solar-rainy.png)

My own usage averages to about 9 kWh/day, so output on a very cloudy summer day does not
cover it :|

Over the course of May, daily production was on average 15.4 kWh/day:

[{{<img width="600" src="/img/blog/2017-06/solar-month.png">}}](/img/blog/2017-06/solar-month.png)

For that month, from the 478 kWh produced I have returned/loaned/stored 371 kWh to
the grid, and used 188 kWh from the grid during evenings.

All this is during near-midsummer sunny month, in a fairly northern (55°N) latitude.
I'll have to see how that works out during winter :)


#### What's the cost breakdown?

Total cost of setting up everything was about 5200€:

* Solar panels 2120€ (40%)
* Power inverter 1310€ (25%)
* Design, paperwork, permits, all the Vogon stuff 800€ (15%)
* Construction work & transportation 680€ (14%)
* Roof fixture 300€ (6%)

So yeah, this means that even if future prices of the panels are expected
to fall further, by itself that won't affect the total cost *that* much. I would
expect that future paperwork prices *should* fall down too, since today that process
is way too involved & complexicated IMHO.

I *should* get about 30% of the cost back, due to some sort of government (or EU?)
programs that cover that amount of solar (and I guess wind/hydro too) installations.
But that will be more paperwork sometime later this year; will see.


#### What's the ROI? Why do this?

Return on investment is something like 10 years, assuming roughly stable
electricity prices, and if the solar modules retain 90% of their efficiency at
the end of that.

Purely from financial perspective, it's not an investment that would be worth the hassle,
at this scale at least.

However, that assumes that the price *I* pay for electricity is the total *actual* cost
of electricity. Which I *think* is not true; there's a massive hidden cost that we
all happily ignore -- but is distributed to other poor people (or future ourselves)
in terms of droughts, fires, floods, political instabilities, wars and so on.
Solar has some hidden cost too (mining rare minerals needed for the solar modules;
that is a finite resource and has long-lasting non-trivial effects on the places where
mining happens), but it *feels* like today that's a lesser evil than the alternative
of coal/oil/gas.

So if I can do even a tiny part towards "slightly better balance", I'm happy to do it.
That's it!

