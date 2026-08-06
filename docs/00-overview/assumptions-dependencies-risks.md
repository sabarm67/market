# Assumptions, Dependencies & Risks Log

## Assumptions

| ID | Assumption | Impact if wrong |
|---|---|---|
| A1 | Delayed/batch market data is acceptable for MVP users (not real-time). | NFR latency targets and System Architecture would need rework. |
| A2 | SC Malaysia's biannual Shariah list (PDF/Excel) is an acceptable authoritative source, manually imported. | Shariah Compliance module accuracy/freshness would be questioned; may need a licensed data vendor instead. |
| A3 | A single free tier (no billing) is acceptable for MVP — monetization is a Phase 2+ concern. | Security Architecture, Role Matrix, and FRS would need tier logic added. |
| A4 | The platform is monitoring/information only — no brokerage, no order execution, no funds transfer. | Would introduce a much larger compliance/regulatory scope (e.g., CMSL licensing) if wrong. |
| A5 | English (and possibly Bahasa Malaysia) is sufficient for MVP; no other localization required. | UX/IA and content docs would need a localization strategy. |
| A6 | MYR is the only currency needed for MVP (Bursa-listed securities only). | Multi-currency handling would need to be designed into ERD/Schema now instead of later. |

## Dependencies (External, Unresolved)

| ID | Dependency | Status | Blocks |
|---|---|---|---|
| D1 | Bursa Malaysia market data feed (licensed) or a third-party aggregator (Alpha Vantage, Polygon.io, Finnhub, Twelve Data, Financial Modeling Prep) covering Bursa-listed securities | **Not procured** | Any real data ingestion; System/Infrastructure Architecture written provider-agnostic in the meantime. |
| D2 | Claude and/or OpenAI API key | **Not procured** | All AI-dependent modules — explicitly out of MVP scope because of this (see AI Architecture). |
| D3 | SC Malaysia Shariah-compliant securities list access (public PDF/Excel, published biannually) | Publicly available, not yet integrated into any process | Shariah Compliance module — MVP works around this via manual curated import (ADR-0003). |
| D4 | Hosting/cloud provider account | **Resolved** — Laravel Forge, site `market.rcaquacycle.com`, MySQL DB `market` | Was blocking Infrastructure Architecture's provider-specific detail; see [ADR-0008](../decisions/0008-hosting-provider.md). |
| D5 | Company fundamental data source (financial statements, ratios) for Bursa-listed companies | **Not procured** — likely bundled with D1 or a separate provider | Fundamental Analysis module data ingestion. |

## Risks

| ID | Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|---|
| R1 | No market data provider is contracted; MVP cannot actually ingest live data until D1 is resolved. | High (certain, today) | High — blocks any working prototype, not just documentation | Documentation is written to be provider-agnostic; flag D1 as the top blocker before any coding phase begins. |
| R2 | Manual Shariah list import (ADR-0003) becomes stale between SC Malaysia's biannual publications, or a company's status changes intra-period via other announcements. | Medium | Medium — could show incorrect Shariah status | Document the manual re-import process clearly; flag "last updated" date prominently in the UI (captured in FRS). |
| R3 | Scope creep back toward the full original spec during requirements/architecture drafting. | Medium | Medium — dilutes MVP focus, delays a shippable v1 | Every doc explicitly cross-references `mvp-scope-definition.md`; final consistency pass greps for out-of-scope content before sign-off. |
| R4 | PDPA (Malaysia) and general data-protection obligations are underestimated since no legal review has occurred. | Low-Medium | High if wrong | NFR and Security Architecture document PDPA-relevant requirements as best-effort; recommend legal review before production launch. |
| R5 | Company/financial data licensing terms may restrict redistribution or display formatting in ways not yet known. | Medium | Medium-High | Flag as a procurement-time question when negotiating with data providers (D1/D5); do not assume unrestricted display rights. |
