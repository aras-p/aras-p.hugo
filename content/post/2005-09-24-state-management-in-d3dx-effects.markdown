---
categories:
- d3d
- papers
comments: true
date: 2005-09-24T18:45:00Z
slug: state-management-in-d3dx-effects
status: publish
title: State management in D3DX Effects
url: /blog/2005/09/24/state-management-in-d3dx-effects/
wordpress_id: "68"
---

In my projects I've been using D3DX Effects with no device state saving/restoring. Instead, each effect contained a dummy "last pass" that restores "needed" state (see [here](http://dingus.berlios.de/index.php?n=Main.D3DXEffects); more lengthy article coming in [ShaderX4](http://www.shaderx4.com/TOC.html)).

I always wrote this "state restore" by hand. This is obviously very error-prone; it's ok if I'm the only one writing effects but would be unusable in any real world scenario.

I think I could automatically generate the "state restore" pass. Somehow the engine knows which states need to be restored; which must be set in every effect etc. (this could be read from some file). It first loads each effect file and examines what states it touches. This can be done by supplying a custom ID3DXEffectStateManager and "executing" the effect - the state manager then would remember all states (left-hand sides of state assignments) touched by the effect.

Then the engine generates the "state restore" pass and loads the effect again. I'd image it would do it like this: each effect has to contain a macro RESTORE_PASS:


    technique Foo {
      pass P1 { ... }
      pass P2 { ... }
      RESTORE_PASS
    }

Which would be empty during first load and which would expand to the generated restore pass on the second load (you can supply generated macro definitions when loading the effect). The engine can check whether the generated pass exists after second load (if it doesn't then RESTORE_PASS is missing from the effect - an error).

The downside of this scheme is that each effect file has to be loaded twice - first time for examining its state assignments and second time for actually loading it with the generated restore pass. It's not a problem for me, I guess, because effect loading doesn't take much time anyway... And if it would become really slow, all this stuff can be done as a preprocess (e.g. during a build).

There are many upsides of this scheme, I think: the whole system is robust and error-proof again (no longer depends on the effect writer to remember all the details about states). And as far as I can see, no performance would be lost at all (performance was the main point why I'm using this "restore pass").

Gotta go and implement all this!

