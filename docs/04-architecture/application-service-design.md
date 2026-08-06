# Application / Service Design

Reframed from the original spec's "Microservices Design" — MVP uses a **modular monolith**,
not microservices. This document defines internal module boundaries designed so they
*could* later be extracted into services, without committing to that complexity now.

## Decision

**Modular monolith.** A single Laravel 12 application, internally organized into bounded
modules with clear interfaces (e.g., Laravel-style domain folders or packages), rather
than separate deployable services.

## Rationale

- MVP scope (6 modules, single market, no AI) doesn't have independent scaling,
  independent deployment, or independent team-ownership needs that justify microservices
  overhead (network calls, service discovery, distributed transactions).
- A well-modularized monolith keeps development velocity high for a small team while
  preserving the *option* to extract a module into a service later if it develops
  distinct scaling needs (e.g., if AI/RAG processing is added in Phase 2+ and needs GPU
  infrastructure separate from the web tier).

## Internal Module Boundaries (MVP)

| Module | Responsibility | Depends On |
|---|---|---|
| Auth | Registration, login, session management (Sanctum) | — |
| MarketData | Companies, securities, price data, sectors/markets | Auth (for admin edit ops) |
| Fundamentals | Fundamental data storage and retrieval | MarketData (company reference) |
| Technicals | Indicator computation (MA, RSI, MACD, Bollinger) over PriceData | MarketData |
| Shariah | ShariahStatus storage, admin import workflow | MarketData (security reference), Auth (admin check) |
| Watchlist | Watchlist/WatchlistItem CRUD | Auth, MarketData |
| Dashboard | Aggregation/read-model over MarketData + Shariah for the dashboard view | MarketData, Shariah |

Each module maps to its own set of Eloquent models, controllers, and (where computation is
involved, e.g., Technicals) service classes — kept in separate namespaces/directories to
enforce the boundary even within one codebase.

## Future Service Extraction Candidates (Not Done in MVP)

If/when Phase 2+ modules are built, these are the most likely first candidates for
extraction into standalone services, given their distinct resource profiles:

- **AI/RAG service** (AI Research Assistant, News Intelligence) — likely needs different
  scaling (LLM API calls, embedding storage) than the core web app.
- **Ingestion service** — if multi-market data ingestion grows complex enough to warrant
  independent scheduling/scaling from the web tier.
- **Alert/notification dispatch** — multi-channel delivery (email/SMS/push/Telegram/
  WhatsApp) benefits from independent retry/queue semantics.

This is directional, not a committed roadmap — revisit when those modules are actually
scoped.
