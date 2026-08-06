# Non-Functional Requirements (NFR)

Scope: Phase 1 (MVP) — Bursa Malaysia only (~1,800 listed securities).

## 1. Performance

| ID | Requirement |
|---|---|
| NFR-P1 | Page load (dashboard, company profile) under 2 seconds on a broadband connection, for cached/pre-aggregated data. |
| NFR-P2 | Data ingestion job (EOD price/fundamental sync) completes within a defined batch window (e.g., overnight); exact SLA depends on the eventual data provider (Dependency D1) and is TBD pending procurement. |
| NFR-P3 | Search/autocomplete for company lookup responds in under 500ms for a ~1,800-security universe. |

## 2. Availability

| ID | Requirement |
|---|---|
| NFR-A1 | Target 99% uptime for MVP (not enterprise-grade 99.9%+) — appropriate for a free-tier, pre-revenue product. |
| NFR-A2 | Scheduled maintenance windows are acceptable and should be communicated in-app; no requirement for zero-downtime deploys in MVP. |

## 3. Scalability

| ID | Requirement |
|---|---|
| NFR-S1 | Architecture should comfortably support the full Bursa Malaysia universe (~1,800 securities) and an initial user base in the low thousands without redesign. |
| NFR-S2 | Database and application design should not preclude later horizontal scaling or extraction into services (see [Application/Service Design](../04-architecture/application-service-design.md)), but MVP itself does not need to be built for that scale yet. |

## 4. Data Freshness & Accuracy

| ID | Requirement |
|---|---|
| NFR-D1 | All price and fundamental data must display an explicit "as of" timestamp (per BR3). |
| NFR-D2 | Shariah status must display the SC Malaysia source publication date (per BR4). |
| NFR-D3 | No requirement for real-time (sub-second/streaming) data in MVP — delayed/batch is acceptable (see [ADR-0002](../decisions/0002-realtime-delivery-mechanism.md)). |

## 5. Security & Compliance

| ID | Requirement |
|---|---|
| NFR-SEC1 | Passwords hashed (bcrypt/argon2 via Laravel defaults); no plaintext storage anywhere. |
| NFR-SEC2 | All traffic over HTTPS/TLS. |
| NFR-SEC3 | Role-based access control enforced server-side for Admin-only functions (Shariah import, user/company management). |
| NFR-SEC4 | Personal data handling complies with PDPA (Malaysia) — collect only necessary data, provide account deletion, document retention policy. Recommend legal review before production launch (see Risk R4). |
| NFR-SEC5 | API rate limiting on public/authenticated endpoints to prevent abuse (see [Security Architecture](../04-architecture/security-architecture.md)). |

## 6. Usability

| ID | Requirement |
|---|---|
| NFR-U1 | Responsive design — usable on desktop and mobile browsers (PWA/native apps are roadmap, not MVP). |
| NFR-U2 | Dark mode and light mode support. |
| NFR-U3 | Clear visual/textual distinction between factual data display and any interpretive content (per BR6), even though MVP has no AI-generated content yet — this UX pattern should be established early. |

## 7. Maintainability

| ID | Requirement |
|---|---|
| NFR-M1 | Codebase (future build phase) follows Laravel and Vue community conventions to ease onboarding of future contributors. |
| NFR-M2 | Architecture documented (this phase) sufficiently that a new engineer can onboard from docs alone. |

## 8. Explicitly Deferred NFRs

Enterprise-grade SLAs (99.9%+ uptime), multi-region redundancy, real-time streaming
performance targets, and formal penetration testing/compliance certification (SOC 2, ISO
27001) are Phase 2+ concerns once the platform has paying/institutional customers — see
[`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md).
