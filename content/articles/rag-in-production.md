---
title: "Retrieval-Augmented Generation in Production: Lessons Learned"
description: "RAG demos are easy. RAG in production is a retrieval problem wearing a generation costume. Hard-won lessons from shipping it for real."
author: "Aisha Mwangi"
role: "ML Platform Lead"
date: "2026-06-02"
category: "AI Engineering"
tags: ["rag", "llm", "vector search", "embeddings", "production"]
featured: false
views: 7290
---

Retrieval-Augmented Generation has a deceptive learning curve. The weekend demo — embed some documents, drop them in a vector store, stuff the top matches into a prompt — works astonishingly well. Then you put it in front of real users with real questions, and the cracks appear immediately.

After shipping several RAG systems, the single most important thing I can tell you is this: **RAG is a retrieval problem first and a generation problem second.** Teams obsess over the model and neglect the part that actually determines quality.

## The model is rarely the bottleneck

When a RAG answer is wrong, the instinct is to blame the LLM or reach for a bigger one. In practice, the overwhelming majority of bad answers trace to bad retrieval:

- The relevant chunk was never retrieved.
- The relevant chunk was retrieved but buried below noise.
- The chunk was retrieved but lacked the context to be useful on its own.

A perfect model cannot answer from documents it never received. Fix retrieval before you touch the model.

## Chunking is where quality is won or lost

How you split documents matters more than almost any other decision. Naive fixed-size chunking — every 512 tokens, hard cut — slices sentences in half and severs context from its heading.

> A chunk should be the smallest unit that still makes sense on its own. Smaller than that and it loses meaning; larger and it dilutes relevance.

Practical guidance that has held up:

1. **Respect structure.** Split on headings, paragraphs, and sections, not arbitrary token counts.
2. **Carry context down.** Prefix each chunk with its document title and section path so an isolated chunk still knows where it came from.
3. **Overlap modestly.** A little overlap between adjacent chunks prevents answers from falling in the cracks.

## Hybrid search beats pure vectors

Pure semantic search is excellent at concepts and terrible at specifics. Ask for error code `E4012` and a vector search may return philosophically similar errors. Keyword search nails the exact token but misses paraphrases.

The answer is hybrid: run both, then combine. A simple reciprocal-rank fusion is enough to start.

```python
def hybrid_search(query, k=10):
    dense = vector_store.search(embed(query), k=k)
    sparse = keyword_index.search(query, k=k)
    return reciprocal_rank_fusion(dense, sparse)[:k]
```

Add a reranking step on top — a cross-encoder that scores each candidate against the query — and quality jumps again. Retrieve broadly, rerank precisely.

## Evaluation, or you are flying blind

The most common production failure is having no way to know if a change helped. "It seems better" is not a metric. Before optimizing anything, build an evaluation set:

- A few dozen real questions with known-good source documents.
- A retrieval metric: did the right chunk appear in the top *k*?
- An answer metric: faithfulness (is the answer grounded in retrieved text?) and relevance.

With this in place, every change becomes measurable. Without it, you are tuning a system you cannot see.

## Operational realities

A few things the demos never warn you about:

- **Freshness.** Your index is a cache of your documents. When the docs change, the cache lies. Build re-indexing into your deployment pipeline.
- **Cost and latency.** Reranking and large context windows are not free. Budget them.
- **Citations are non-negotiable.** Users trust answers they can verify. Always return the sources you retrieved from.

## The bottom line

RAG works in production, but not the way the tutorials suggest. Pour your effort into retrieval — chunking, hybrid search, reranking — and build an evaluation harness before you optimize anything. The model is the easy part. The pipeline that feeds it is the product.
