---
title: "Designing for Failure: Resilience Patterns in Distributed Systems"
description: "In a distributed system, failure is not an exception — it is the steady state. The patterns that keep services standing when their dependencies fall over."
author: "Tomás Lindqvist"
role: "Distributed Systems Architect"
date: "2026-05-14"
category: "Architecture"
tags: ["distributed systems", "resilience", "reliability", "patterns"]
featured: false
views: 4310
---

There is a moment in every engineer's career when they stop asking "what if this dependency fails?" and start assuming it already has. That shift — from hoping for reliability to designing for failure — is what separates systems that survive a bad afternoon from systems that make the news.

In a single process, a function call either returns or throws. Across a network, there is a third outcome that ruins everything: it might *hang*. The call neither succeeds nor fails; it just sits there, holding a thread, while the timeout you forgot to set never fires.

## Assume the network is hostile

The fallacies of distributed computing are decades old and still violated daily. The network is not reliable, latency is not zero, and bandwidth is not infinite. Design accordingly:

- Every remote call has a timeout. No exceptions.
- Every timeout is shorter than the patience of whoever is calling *you*.
- Every retry has a limit and a backoff.

That second point is subtle and important. If your timeout is longer than your caller's timeout, you do work nobody is waiting for anymore.

## The circuit breaker

When a dependency is failing, the worst thing you can do is keep hammering it. Retries pile up, threads exhaust, and your failure becomes their failure becomes everyone's failure. This is a cascading failure, and it is how small incidents become outages.

The circuit breaker pattern borrows from electrical engineering:

1. **Closed** — calls flow normally. Failures are counted.
2. **Open** — too many failures; calls fail fast without even trying. The struggling dependency gets room to recover.
3. **Half-open** — after a cooldown, a trickle of calls is allowed through to test the waters.

> A circuit breaker does not prevent failure. It prevents one failure from becoming many.

## Bulkheads: contain the blast

Ships survive hull breaches because they are divided into watertight compartments. One flooded section does not sink the vessel. Apply the same idea to resources:

```text
Single shared pool:        Bulkheaded pools:
[==========]               [===] payments
 one slow dependency        [===] search
 drains every connection    [===] recommendations
                            a slow search cannot
                            starve payments
```

Give each downstream dependency its own connection pool or thread budget. A slow recommendations service should never be able to consume the connections that payments needs.

## Graceful degradation

Not every feature is equally important. When something fails, decide *in advance* what you can live without:

- The product page must load. The "customers also bought" carousel can disappear.
- Checkout must work. The loyalty-points preview can show a placeholder.

A system that degrades gracefully feels reliable even while parts of it are on fire. A system that treats every dependency as critical fails completely when any one of them does.

## Idempotency: the unsung hero

Retries are essential, and retries are dangerous: if the first request actually succeeded but the response was lost, your retry just charged the customer twice. The defense is idempotency — design operations so that doing them twice is the same as doing them once. An idempotency key on every mutating request makes retries safe.

## Test the failure, not just the success

The patterns above are worthless if they have never been exercised. Inject failure deliberately:

- Kill instances and confirm traffic reroutes.
- Add latency to a dependency and watch the circuit breaker trip.
- Block a service entirely and verify graceful degradation.

If you have never seen your resilience patterns activate, you do not have resilience patterns. You have hopeful code.

## The bottom line

Resilience is not a feature you add at the end. It is a set of assumptions you build in from the start: timeouts everywhere, circuit breakers around dependencies, bulkheads between resources, graceful degradation by design, and idempotency on every mutation. Failure is the steady state. Design for it, and the steady state stops being scary.
