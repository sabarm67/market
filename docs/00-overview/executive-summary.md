# Executive Summary

## Vision

Build an AI-assisted investment intelligence platform that becomes, over time, a
"Bloomberg Terminal for individual investors" — combining real-time market monitoring,
fundamental and technical analysis, AI-generated research, and Shariah-compliant investing
support, at a fraction of the cost of institutional terminals (Bloomberg, Refinitiv,
FactSet). The platform is intended for retail investors, professional traders, fund
managers, Islamic investors, financial advisers, research houses, and educational
institutions.

The full vision spans ~15 global market regions and dozens of modules — see
[`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md) for the
complete long-term scope.

## Phase 1 (MVP): What We're Building Now

Phase 1 narrows this to a shippable, coherent slice:

- **Market:** Bursa Malaysia only (Main Market, ACE Market, LEAP Market).
- **Modules:** Market Dashboard, Company Profile, Fundamental Analysis, Basic Technical
  Analysis, Watchlist, and Shariah Compliance.
- **Users:** Guest browsing, a single free registered tier, and an Admin role.
- **Data:** Delayed/batch, not real-time (no live provider contracted yet).

Full rationale in [`mvp-scope-definition.md`](mvp-scope-definition.md).

## Why This Scope

Bursa Malaysia is a well-scoped starting market with a defined regulatory and Shariah
framework, letting the platform ship a genuinely useful, differentiated product (Shariah
screening is not commonly offered by mainstream retail tools) without the complexity of
multi-market, multi-currency, or AI-provider dependencies that aren't resolved yet.

## Current Status

This phase is **documentation only**. No application code exists. The immediate deliverable
is a complete requirements, data-design, architecture, API, and UX specification that a
subsequent build phase can implement directly, minimizing rework.

Two hard external blockers are tracked openly (see
[`assumptions-dependencies-risks.md`](assumptions-dependencies-risks.md)):
no market data provider is contracted, and no AI (Claude/OpenAI) API access is provisioned.
Neither blocks documentation, but both must be resolved before a working prototype can
ingest real data or offer AI features.

## Guiding Principle

The platform is explicitly for **education and research**. All outputs — fundamental
scores, technical signals, AI-generated commentary (Phase 2+) — must clearly distinguish
factual analysis from investment recommendations, and the platform performs no brokerage,
order execution, or fund transfer functions.
