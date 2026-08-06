# Future Enhancement Roadmap

Everything scoped out of Phase 1 (MVP), captured here so it isn't lost. Not sequenced into
firm phases/dates — sequencing should happen once MVP ships and real usage data exists.

## Additional Markets

Bursa Malaysia's other segments already covered in MVP (Main/ACE/LEAP). Beyond that:

- United States: NYSE, NASDAQ, AMEX
- Europe: London, Germany, France
- Asia: Japan, Hong Kong, Singapore, China, South Korea, India
- Australia, Middle East
- Global ETFs, global indices, REITs, preferred shares, commodities, currencies
- Cryptocurrency (optional module, per original spec)

Each new market brings its own currency, trading calendar, and regulatory/disclosure
regime — treat as a significant scope addition, not a config toggle.

## AI-Powered Modules (Blocked on Dependency D2 — no Claude/OpenAI API access yet)

- **AI Fundamental Analysis** — business summary, moat, bull/bear/neutral case generation
- **AI Technical Analysis** — automated pattern recognition, entry/target/stop-loss with
  confidence scores
- **AI Recommendation Engine** — Buy/Accumulate/Hold/Reduce/Sell with reasoning and
  fundamental/technical/macro/risk/valuation sub-scores
- **AI News Intelligence** — news aggregation, summarization, impact/sentiment scoring
- **AI Research Assistant** — natural-language Q&A over platform data via RAG
- See [`ai-architecture.md`](../04-architecture/ai-architecture.md) for the target-state
  design already sketched for these.

## Additional Feature Modules

- ~~**Stock Screener**~~ — **Delivered.** Multi-criteria filtering (sector, sub-market,
  Shariah status, market cap, PE, PB, ROE, debt/equity, dividend yield, revenue growth,
  volume, RSI) — see [FRS Module 7](../01-requirements/functional-requirements-specification.md#module-7-stock-screener).
  Custom formula builder remains a future enhancement.
- **Portfolio Management** — multiple portfolios, allocation analysis, performance
  benchmarking, tax estimation, AI rebalancing suggestions
- **Alert Engine** — price/volume/corporate-action alerts via email, SMS, push, Telegram,
  WhatsApp
- **Advanced Charting** — Renko, Point & Figure, Market Profile, VWAP, drawing tools
  (Fibonacci, Gann, Pitchfork), multi-chart layout, replay mode, backtesting
- **Extended Shariah Module** — screening ratio breakdowns (debt/cash ratios, business
  activity detail), dividend purification calculator, zakat estimation, automatic
  change-notification alerts
- **Extended Fundamental Analysis** — DCF valuation, intrinsic value, Piotroski/Altman Z/
  Beneish M scores, AI-generated Quality/Value/Growth/Overall scores
- **Professional Research Reports** — Daily/Weekly/Monthly Market Reports, Company/Sector/
  Portfolio/Economic/Risk/Dividend reports, PDF/Excel/PPT export
- **Extended Company Profile** — SWOT analysis, competitor comparison, corporate structure
  diagrams, interactive milestone timelines

## Platform & Access

- Paid tiers (Premium/Professional/Research/Institution) and billing — see
  [`role-access-tier-matrix.md`](../01-requirements/role-access-tier-matrix.md#roadmap-tiers-not-in-mvp)
- Multi-tenant Organization/Institution accounts — see [ADR-0001](../decisions/0001-tenancy-model.md)
- MFA / biometric login
- Real-time (push/WebSocket) data delivery, once a push-capable provider is contracted —
  see [ADR-0002](../decisions/0002-realtime-delivery-mechanism.md)
- GraphQL API (if a multi-client/third-party developer need emerges) — see
  [ADR-0005](../decisions/0005-rest-vs-graphql.md)

## Delivery-Phase Items (Deferred From This Documentation Phase, Not "Future" Per Se)

These are needed to actually ship MVP once docs are approved — sequenced next, not
long-term roadmap:

- Laravel 12 backend implementation, Vue 3 frontend implementation
- Database migrations/seeders (from [`database-schema.md`](../03-data-design/database-schema.md))
- PWA packaging
- CI/CD pipeline, infrastructure-as-code, deployment guide
- User manual, administrator manual
- UAT execution
- High-fidelity mockups (this doc set only has low-fidelity wireframes)
- Resolving Dependencies D1 (market data provider), D4 (hosting provider), D5 (fundamental
  data source) — see [`assumptions-dependencies-risks.md`](../00-overview/assumptions-dependencies-risks.md)
