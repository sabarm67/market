# Documentation Index

This is the documentation set for Phase 1 (MVP) of the Share Market Monitoring & Investment
Intelligence Platform: **Bursa Malaysia only**, six core modules (Market Dashboard, Company
Profile, Fundamental Analysis, Basic Technical Analysis, Watchlist, Shariah Compliance).

No application code exists yet — this phase is documentation only. See
[`00-overview/mvp-scope-definition.md`](00-overview/mvp-scope-definition.md) for the full
scope rationale.

## Status Legend

- **Draft** — written, not yet reviewed
- **In Review** — shared with stakeholder for feedback
- **Approved** — locked; downstream docs may depend on it

## Document Status

| # | Document | Status | Depends On |
|---|---|---|---|
| **00 — Overview** |||
| 1 | [Executive Summary](00-overview/executive-summary.md) | In Review | — |
| 2 | [MVP Scope Definition](00-overview/mvp-scope-definition.md) | In Review | — |
| 3 | [Glossary & Data Dictionary](00-overview/glossary-data-dictionary.md) | In Review | (living doc) |
| 4 | [Assumptions, Dependencies & Risks](00-overview/assumptions-dependencies-risks.md) | In Review | — |
| **01 — Requirements** |||
| 5 | [Business Requirements Specification](01-requirements/business-requirements-specification.md) | In Review | Exec Summary |
| 6 | [Functional Requirements Specification](01-requirements/functional-requirements-specification.md) | In Review | BRS, ADR-0001, ADR-0003 |
| 7 | [Non-Functional Requirements](01-requirements/non-functional-requirements.md) | In Review | BRS |
| 8 | [Use Cases](01-requirements/use-cases.md) | In Review | FRS |
| 9 | [User Stories](01-requirements/user-stories.md) | In Review | Use Cases |
| 10 | [Role & Access Tier Matrix](01-requirements/role-access-tier-matrix.md) | In Review | FRS |
| **02 — Process Design** |||
| 11 | [Business Process Flows](02-process-design/business-process-flows.md) | In Review | Use Cases |
| 12 | [Data Flow Diagrams](02-process-design/data-flow-diagrams.md) | In Review | Process Flows, FRS |
| **03 — Data Design** |||
| 13 | [Entity Relationship Diagram](03-data-design/erd.md) | In Review | FRS, DFD |
| 14 | [Database Schema](03-data-design/database-schema.md) | In Review | ERD |
| **04 — Architecture** |||
| 15 | [System Architecture](04-architecture/system-architecture.md) | In Review | NFR, ERD |
| 16 | [Application/Service Design](04-architecture/application-service-design.md) | In Review | System Architecture |
| 17 | [AI Architecture](04-architecture/ai-architecture.md) | In Review | System Architecture |
| 18 | [Security Architecture](04-architecture/security-architecture.md) | In Review | NFR, Role Matrix |
| 19 | [Infrastructure Architecture](04-architecture/infrastructure-architecture.md) | In Review | System Architecture |
| **05 — API** |||
| 20 | [API Overview](05-api/api-overview.md) | In Review | FRS, ERD |
| 21 | [OpenAPI Spec](05-api/openapi.yaml) | In Review | API Overview |
| **06 — UX** |||
| 22 | [Navigation / IA](06-ux/navigation-ia.md) | In Review | FRS |
| 23 | [Wireframes](06-ux/wireframes/) (6 screens) | In Review | Navigation/IA |
| **07 — Roadmap** |||
| 24 | [Future Enhancements Roadmap](07-roadmap/future-enhancements-roadmap.md) | In Review | all above |
| **Architecture Decision Records** |||
| — | [0001 — Tenancy Model](decisions/0001-tenancy-model.md) | In Review | — |
| — | [0002 — Real-Time Delivery Mechanism](decisions/0002-realtime-delivery-mechanism.md) | In Review | — |
| — | [0003 — Shariah Data Sourcing](decisions/0003-shariah-data-sourcing.md) | In Review | — |
| — | [0004 — Auth Approach](decisions/0004-auth-approach.md) | In Review | — |
| — | [0005 — REST vs GraphQL](decisions/0005-rest-vs-graphql.md) | In Review | — |
| — | [0006 — Repo Structure](decisions/0006-repo-structure.md) | In Review | — |
| — | [0007 — Time-Series Storage](decisions/0007-timeseries-storage.md) | In Review | — |
| — | [0008 — Hosting Provider](decisions/0008-hosting-provider.md) | In Review | — |

## Deferred to Later Phases (Not in This Doc Set)

Laravel/Vue source code, database migrations/seeders, PWA implementation, CI/CD pipelines
and infrastructure-as-code, installation/deployment guides, user and administrator manuals,
high-fidelity mockups, full admin portal design, UAT test plans.
