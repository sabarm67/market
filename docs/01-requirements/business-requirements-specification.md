# Business Requirements Specification (BRS)

Scope: Phase 1 (MVP) — Bursa Malaysia only. See
[`mvp-scope-definition.md`](../00-overview/mvp-scope-definition.md).

## 1. Business Goals

| ID | Goal |
|---|---|
| BG1 | Give retail investors in Malaysia a single place to monitor Bursa Malaysia-listed companies: prices, fundamentals, basic technicals, and Shariah status. |
| BG2 | Provide Shariah-conscious investors a reliable, dated reference for Shariah compliance status, differentiating from generic market-data tools. |
| BG3 | Establish a documentation and architecture foundation that can extend to additional markets and AI-powered modules in later phases without major rework. |
| BG4 | Keep the platform clearly positioned as an education/research tool, not a source of investment advice or a brokerage. |

## 2. Stakeholders

| Stakeholder | Interest |
|---|---|
| Retail Investor (Guest/Registered) | Free access to company data, fundamentals, basic technicals, watchlists, and Shariah status for Bursa-listed securities. |
| Shariah-Conscious Investor | Accurate, dated Shariah compliance status per SC Malaysia's published list. |
| Admin | Ability to import/maintain Shariah status data and manage user accounts and company data quality. |
| Product Owner (the user) | A shippable MVP that validates the platform concept before investing in the full multi-market, AI-powered vision. |

Deferred (roadmap-only, not MVP stakeholders): fund managers, financial advisers, research
houses, and educational institutions — these are targeted by Phase 2+ modules (portfolio
management, professional research reports, institution accounts).

## 3. Business Rules

| ID | Rule |
|---|---|
| BR1 | All market data and pricing displayed is scoped to Bursa Malaysia (Main, ACE, LEAP markets) only. |
| BR2 | All currency values are displayed in MYR. |
| BR3 | Data freshness is delayed/batch, not real-time; the UI must indicate the data's "as of" timestamp on every price/fundamental display (see [ADR-0002](../decisions/0002-realtime-delivery-mechanism.md)). |
| BR4 | Shariah status is sourced solely from the officially published SC Malaysia list, imported by an Admin; each status record carries its source publication date (see [ADR-0003](../decisions/0003-shariah-data-sourcing.md)). |
| BR5 | The platform does not execute trades, hold funds, or connect to brokerage accounts. |
| BR6 | All AI-generated content is out of MVP scope; any future AI output must be clearly labeled as analysis, not investment advice (carried forward as a requirement for Phase 2+). |
| BR7 | Registered users get one free tier; there is no paid tier or billing logic in MVP (see [ADR on tiers, mvp-scope-definition.md](../00-overview/mvp-scope-definition.md#tiers)). |

## 4. Success Criteria (MVP)

- A registered user can find any Bursa Malaysia-listed company, view its profile,
  fundamentals, basic technical chart, and current/historical Shariah status.
- A registered user can create watchlists and add/remove Bursa-listed securities.
- An Admin can import an SC Malaysia Shariah list update and have it reflected in the
  Shariah Compliance module within the same session.
- Documentation set (this phase) is complete and internally consistent, enabling a
  follow-on build phase to begin implementation without re-deriving requirements.

## 5. Out of Scope

See [`mvp-scope-definition.md`](../00-overview/mvp-scope-definition.md#explicitly-out-of-scope-for-mvp)
for the full list (other markets, AI modules, screener, portfolio management, alerts,
advanced charting, monetization).
