---
title: "Prompt Engineering Is Dead. Long Live Context Engineering."
description: "Clever prompt phrasing was always a fragile trick. The durable skill is assembling the right context — and that is an engineering discipline, not a wording game."
author: "Aisha Mwangi"
role: "ML Platform Lead"
date: "2026-06-12"
category: "AI Engineering"
tags: ["llm", "context", "prompting", "agents", "architecture"]
featured: false
views: 8730
---

For about two years, "prompt engineering" was a job title, a course category, and a LinkedIn flex. The premise was that the right magic words — "you are an expert," "take a deep breath," "think step by step" — unlocked dramatically better model output.

Some of that was real. Most of it was folklore that happened to correlate with better results because it nudged the model toward more deliberate responses. As models got better, the magic words mattered less and less. What did not stop mattering — what mattered *more* — was the information you put in front of the model. That is context engineering, and it is the real discipline.

## The shift in one sentence

> Prompt engineering asks "how do I phrase the request?" Context engineering asks "what does the model need to know to get this right, and how do I get that information into the window?"

The second question is an engineering problem. It involves retrieval, data modeling, summarization, state management, and tight token budgets. It does not reward clever phrasing; it rewards good systems.

## Context is a budget, not a bucket

The context window is finite, and every token competes with every other token. Treating it as an infinite bucket — stuff in everything that might be relevant — actively degrades results. Models attend less reliably to information buried in the middle of a long context, a failure mode sometimes called "lost in the middle."

So context engineering is fundamentally an act of *curation under constraint*:

- What is the minimum information needed to answer correctly?
- How do I rank candidates so the most relevant sits where the model attends best?
- What can be summarized rather than included verbatim?
- What can be fetched on demand instead of preloaded?

## The anatomy of a well-engineered context

A production context window is assembled, not written. A typical layout:

```text
┌─────────────────────────────┐
│ System: role, rules, format │  stable, cached
├─────────────────────────────┤
│ Tools & their schemas       │  what the model can do
├─────────────────────────────┤
│ Retrieved knowledge         │  curated, ranked, cited
├─────────────────────────────┤
│ Conversation / task state   │  compacted as it grows
├─────────────────────────────┤
│ The actual request          │  last, where attention is high
└─────────────────────────────┘
```

Each band is its own engineering problem. The retrieved-knowledge band is a retrieval pipeline. The task-state band is a summarization-and-compaction problem. The system band is a versioned, cached asset you test like code.

## Why this matters more for agents

For a single question-and-answer, you can sometimes get away with a sloppy context. For an agent that runs across dozens of steps, context engineering *is* the architecture. The agent accumulates history, tool outputs, and intermediate results, and the window fills relentlessly. Without deliberate compaction — summarizing old steps, dropping stale tool output, carrying forward only what the next step needs — the agent either runs out of room or drowns in its own transcript.

The teams shipping reliable agents are not the ones with the cleverest prompts. They are the ones with disciplined context management: clear state, aggressive summarization, and retrieval that puts the right facts in the right place at the right step.

## How to build the skill

If you want to get good at this, practice the engineering, not the wording:

1. **Measure token usage** per request and treat it as a cost to minimize.
2. **Build retrieval and ranking** so relevance, not luck, decides what gets included.
3. **Implement compaction** for anything that grows unbounded.
4. **Version and test your system context** the way you version and test code.
5. **Inspect what actually went into the window** when output is wrong — the bug is almost always there, not in the model.

## The bottom line

Prompt engineering was a useful phase, but it was always a thin layer over a deeper truth: models are only as good as the context they are given. The durable skill is not finding magic words. It is building systems that assemble the right information, in the right order, within a tight budget — every single time. That is engineering, and it is not going out of fashion.
