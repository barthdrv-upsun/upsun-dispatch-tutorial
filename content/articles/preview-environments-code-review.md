---
title: "Why Preview Environments Are the Best Thing to Happen to Code Review"
description: "Reading a diff tells you what changed. A preview environment tells you whether it works. Here's why every pull request deserves a running copy of your app."
author: "Marta Olsen"
role: "Staff Platform Engineer"
date: "2026-06-18"
category: "Platform Engineering"
tags: ["preview environments", "git", "code review", "ci/cd"]
featured: true
views: 9860
---

For most of software history, code review meant reading. You opened a diff, you squinted at the red and green lines, and you tried to simulate the program in your head. We got remarkably good at this. We also got remarkably good at approving changes that looked correct and behaved otherwise.

Preview environments break that habit. Instead of imagining what a branch does, you click a link and use it.

## The diff is a lossy format

A diff is a wonderful tool for understanding *intent*. It is a terrible tool for understanding *behavior*. Consider a one-line change:

```diff
- const timeout = 30_000;
+ const timeout = 3_000;
```

The diff is trivial. The consequences are not. Does the upstream service respond within three seconds at p99? Does the retry logic compensate? You cannot answer that by reading. You answer it by running the code against something that looks like production.

> A reviewer who can only read the diff is reviewing the author's intentions. A reviewer with a live environment is reviewing reality.

## What a preview environment actually is

A preview environment is a complete, isolated, throwaway copy of your application, spun up automatically for a single branch or pull request. Crucially, it includes:

- The application code from that branch
- The services it depends on (databases, caches, queues)
- A representative slice of data
- Its own URL

The "representative slice of data" is the part teams underestimate. An empty database hides entire classes of bugs. The best platforms clone production data into each preview, sanitizing sensitive fields on the way through.

## Why this changes the review conversation

When every pull request ships with a working URL, three things happen:

1. **Designers and PMs review the actual thing.** No more "can you record a video?" They open the link.
2. **Bugs surface before merge, not after.** The cost of a defect rises by an order of magnitude at each stage it survives. Catching it in review is the cheapest it will ever be.
3. **The author builds with deployment in mind.** Knowing a preview will spin up changes how people write code. Migrations get tested. Config gets externalized.

## The infrastructure problem nobody mentions

Preview environments sound obvious. The reason they are not universal is that they are *operationally annoying* to build yourself. Each one needs:

| Concern | Naive approach | What you actually need |
| --- | --- | --- |
| Provisioning | Manual `terraform apply` | Automatic on push |
| Data | Empty DB | Sanitized production clone |
| Teardown | Someone remembers | Automatic on merge/close |
| Cost | Surprise bill | Sub-resource, idle-friendly pricing |

Get the teardown wrong and you wake up to four hundred orphaned environments and a cloud invoice that ruins your quarter. This is precisely the kind of toil that a managed platform absorbs so your team does not have to.

## A pragmatic adoption path

You do not need to boil the ocean. Start here:

- Wire preview environments to your default branch's pull requests only.
- Clone data for one critical service first.
- Add the preview URL as a required status check so reviewers actually open it.

Within a sprint, the culture shifts. "LGTM" stops meaning "the diff looked fine" and starts meaning "I used it."

## The bottom line

Reading code will always matter. But the gap between *looks correct* and *is correct* is where production incidents live. Preview environments close that gap for the price of a click. If your review process still runs entirely on imagination, you are leaving the cheapest quality wins on the table.
