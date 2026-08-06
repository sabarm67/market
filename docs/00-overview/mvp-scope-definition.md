# MVP Scope Definition

## Purpose

The full platform vision covers ~15 global market regions and dozens of AI-powered modules
(see the original spec, condensed into [`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md)).
Building that in one pass isn't realistic. This document formalizes the scope boundary
agreed for Phase 1 (MVP), so every downstream document can be checked against a single
source of truth.

## In Scope for MVP

### Market

- **Bursa Malaysia only**: Main Market, ACE Market, LEAP Market.
- Currency: MYR only.
- Trading calendar: Bursa Malaysia trading days/hours only.

### Modules (full functional detail)

1. **Market Dashboard** — top gainers/losers/volume, sector performance, market breadth,
   at a Bursa Malaysia level only (no global heat map, no cross-market comparison).
2. **Company Profile** — overview, business segments, management, major shareholders, for
   Bursa-listed companies.
3. **Fundamental Analysis** — standard financial ratios and statements (revenue, margins,
   EPS, ROE, valuation multiples, etc.) sourced from company filings.
4. **Basic Technical Analysis** — candlestick charts, a core indicator set (Moving Average,
   RSI, MACD, Bollinger Bands, Volume), standard timeframes. Not the full indicator/pattern
   library from the original spec.
5. **Watchlist** — multiple watchlists, add/remove securities, basic notes/tags.
6. **Shariah Compliance** — current/historical Shariah status per the SC Malaysia list,
   sourced via manual curated import (see
   [ADR-0003](../decisions/0003-shariah-data-sourcing.md)).

### Users

- Guest (unauthenticated, browse-only)
- Registered Investor (single free tier — see [ADR on tiers below](#tiers))
- Admin (manages Shariah data import, company data, users)

### Data Freshness

- Delayed/batch data (scheduled polling or EOD ingestion), not real-time push. See
  [ADR-0002](../decisions/0002-realtime-delivery-mechanism.md).

### Tiers

- Single free tier for all registered users in MVP. No billing/subscription logic. Tiering
  (Free/Premium/Professional/etc.) is a roadmap item.

## Explicitly Out of Scope for MVP

Documented only at roadmap level (one paragraph, no FRS-level detail), not built:

- All markets other than Bursa Malaysia (US, Europe, other Asia, Australia, Middle East,
  global ETFs/indices/commodities/currencies/crypto).
- AI Recommendation Engine, AI News Intelligence, AI Research Assistant (RAG chat), AI
  Technical Analysis (pattern recognition), AI Fundamental Analysis narrative generation.
- Stock Screener, Portfolio Management module, multi-channel Alert Engine (email/SMS/push/
  Telegram/WhatsApp).
- Advanced/TradingView-level charting (Renko, Point & Figure, Market Profile, replay mode,
  backtesting, multi-chart layout).
- Paid tiers, monetization, and billing.
- Professional research report generation and export (PDF/Excel/PPT).

## Out of Scope for This Phase (Regardless of Module)

Even for the 6 in-scope modules, this phase (documentation) does not produce:

- Laravel/Vue source code, database migrations/seeders.
- PWA implementation, mobile app builds.
- CI/CD pipelines, infrastructure-as-code, deployment guides.
- User/administrator manuals, UAT execution.
- High-fidelity (pixel-level) mockups — only low-fidelity described wireframes.

## Why This Scope

- **Bursa Malaysia first**: it's the user's home market, has a well-defined regulatory
  and Shariah framework (SC Malaysia), and avoids the complexity of multi-currency,
  multi-timezone, multi-regulatory-regime handling in the first release.
- **Shariah Compliance included even in MVP**: it's a genuine differentiator versus
  generic market-data tools and doesn't require AI or a live data feed to implement
  (rule-based screening against a periodically published list).
- **No AI-dependent modules in MVP**: no AI/LLM API keys are provisioned yet (see
  [assumptions-dependencies-risks.md](assumptions-dependencies-risks.md)), so building
  AI-dependent modules now would be speculative.
