---
title: "Containers, Unikernels, and the Future of Application Packaging"
description: "Containers won the packaging war so completely we stopped asking what comes next. A tour of where application packaging is actually heading."
author: "Tomás Lindqvist"
role: "Distributed Systems Architect"
date: "2026-04-30"
category: "Architecture"
tags: ["containers", "unikernels", "wasm", "packaging"]
featured: false
views: 2140
---

A decade ago, "how do we package and ship this application?" was a genuinely hard question. Then containers arrived, the question seemed answered, and the industry moved on. But the container model carries trade-offs we have quietly accepted, and the alternatives emerging around it are worth understanding — not because containers are going away, but because they are no longer the only sensible answer.

## What containers actually solved

Containers solved *dependency hell* and *environment drift* in one stroke. By packaging the application together with its userland, they made "works on my machine" into "works everywhere the image runs." That is a genuine achievement, and it is why containers won.

What containers did *not* do is make things small or fast to start. A container still ships an entire operating system userland. It still boots a process inside a shared kernel. For long-running services, none of this matters. For workloads that scale to zero and back, it matters a lot.

## The cost of the full userland

Consider what a typical container image contains to run a small service:

```text
your application        ~10 MB
language runtime        ~80 MB
OS packages & libc     ~120 MB
shell, coreutils, etc.  ~40 MB
--------------------------------
mostly things you never call
```

Most of that image is attack surface and cold-start weight you never deliberately chose. Every package is something to patch, something to scan, something to secure. The minimal-image movement — distroless builds, Alpine bases, multi-stage builds that copy only the binary — is the industry trying to undo this bloat after the fact.

## Unikernels: compile away the OS

Unikernels take the opposite approach. Instead of shipping a general-purpose OS and running your app on top, you compile your application *together with* only the OS functions it actually uses into a single, bootable image.

> A unikernel is not your app running on an operating system. It is your app that happens to include exactly the operating system it needs and nothing else.

The payoffs are striking: tiny images, millisecond boot times, and a drastically reduced attack surface — there is no shell to exploit because there is no shell. The cost is operational maturity. Debugging is harder, the tooling is younger, and the ecosystem is thinner. Unikernels remain specialized rather than mainstream, but their ideas are leaking into everything.

## WebAssembly: the dark-horse contender

The most interesting development is WebAssembly outside the browser. Wasm gives you a portable, sandboxed compilation target with near-native speed and startup measured in microseconds. For edge workloads and fine-grained functions, that profile is compelling:

- **Portable.** One artifact runs anywhere there is a Wasm runtime.
- **Secure by default.** Sandboxed with explicit, capability-based access to the outside world.
- **Fast to start.** No userland to boot; instantiate and go.

The component model maturing around Wasm is what turns it from a curiosity into a packaging story.

## So what should you actually use?

For the overwhelming majority of teams, today, the answer is still containers — and that is fine. The pragmatic position:

1. **Use containers for long-running services.** The ecosystem is unmatched and the trade-offs are irrelevant at steady state.
2. **Watch Wasm for edge and function workloads.** Where cold-start and portability dominate, it is already competitive.
3. **Steal unikernel thinking regardless.** Minimize your image. Ship only what you call. Treat every included package as a liability.

The deeper lesson is that **the packaging format should be an implementation detail.** What you want to specify is your application's runtime, its dependencies, and how it should scale. A good platform takes that specification and chooses the right packaging underneath — container today, something lighter tomorrow — without you rewriting anything.

## The bottom line

Containers won, and they earned it. But "won" is not "final." The trend lines all point the same direction: smaller artifacts, faster starts, less included surface. Whether the future is minimal containers, unikernels, or Wasm, the winning move for application teams is to describe *what* they need to run and let the platform decide *how* to package it.
