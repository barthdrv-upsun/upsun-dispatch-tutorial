---
title: "Infrastructure as Code Without the Tears: A Pragmatic Guide"
description: "IaC promised reproducible infrastructure. Too often it delivered a second codebase nobody wants to maintain. Here's how to keep the promise without the pain."
author: "Sofia Reyes"
role: "Principal SRE"
date: "2026-05-27"
category: "Platform Engineering"
tags: ["iac", "terraform", "gitops", "automation"]
featured: false
views: 3580
---

Infrastructure as Code began as a liberation. Instead of clicking through a console and praying you could reproduce it, you declared your infrastructure in files, committed them, and let a tool converge reality to match. Reproducible. Reviewable. Version-controlled.

Then the files multiplied. And the modules. And the state files. And the workspaces. And suddenly your "infrastructure" was a second application — one with worse tooling and no tests.

This guide is about keeping the original promise without inheriting the sprawl.

## The core idea is still right

Declarative infrastructure is correct. The principle is simple:

> Describe the desired state. Let a tool figure out the diff. Never make manual changes.

That last sentence is the whole game. The moment someone makes a manual change in a console, your code no longer describes reality, and every future `apply` becomes a game of roulette. Drift is the original sin of IaC.

## Where teams go wrong

Most IaC pain traces back to three mistakes:

1. **Treating infrastructure code like a dumping ground.** No structure, no review standards, copy-pasted modules. You would never ship application code this way.
2. **Over-abstracting too early.** A five-layer module hierarchy to manage three services. Abstraction has a carrying cost; pay it only when duplication actually hurts.
3. **Managing state by hand.** Local state files, shared over Slack, overwritten in a race. State should be remote, locked, and boring.

## A layered mental model

Think of your infrastructure in three tiers, each with a different change frequency:

| Tier | Examples | Change frequency |
| --- | --- | --- |
| Foundation | Networks, IAM, DNS zones | Rarely |
| Platform | Clusters, databases, queues | Occasionally |
| Application | Services, routes, env config | Constantly |

The mistake is managing all three with the same tool, the same cadence, and the same blast radius. Foundation changes should be deliberate and rare. Application changes should be fast and frequent. Coupling them means every routine deploy risks your network layer.

## Push the application tier into the platform

Here is the pragmatic move that saves the most tears: **stop hand-writing IaC for the application tier.**

The day-to-day infrastructure of an app — its runtime, its routes, the services it binds to — changes constantly and should live *next to the code*, in a single configuration file that ships with the repository. Something like:

```yaml
applications:
  api:
    type: "php:8.5"
    relationships:
      database: "postgres:main"
    web:
      locations:
        "/":
          passthru: "/index.php"

services:
  postgres:
    type: "postgresql:18"
```

When the platform reads this from your repo on every push, three problems vanish at once: there is no separate state to drift, no second pipeline to maintain, and the infrastructure is reviewed in the same pull request as the code that depends on it. Foundation-level IaC still has its place. The application tier does not need it.

## Rules that keep it sane

- **Everything in git, nothing in the console.** Read-only console access for humans; writes only through pipelines.
- **Plan on every pull request.** Reviewers should see the infrastructure diff before merge.
- **Small blast radius.** Separate state per tier so a typo in app config cannot delete a subnet.
- **Delete fearlessly.** If recreating something is scary, your IaC is not actually working yet.

## The bottom line

IaC is not about writing more configuration. It is about ensuring that the configuration you write is the single source of truth. Keep the foundation declarative and deliberate, push the application tier into a platform that reads it straight from your repo, and refuse — absolutely refuse — to make manual changes. Do that, and infrastructure as code stops being a second job.
