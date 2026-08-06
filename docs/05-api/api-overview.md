# API Overview

Full machine-readable spec: [`openapi.yaml`](openapi.yaml). This document gives the
human-readable summary and conventions.

## Conventions

- **Base path:** `/api/v1`
- **Format:** JSON request/response bodies.
- **Auth:** Laravel Sanctum session cookie (per [ADR-0004](../decisions/0004-auth-approach.md)).
  Endpoints marked "Auth required" need an authenticated session; "Admin required" need
  the Admin role.
- **Pagination:** list endpoints use `?page=&per_page=` query params, responding with a
  standard `{ data: [...], meta: { current_page, per_page, total } }` envelope.
- **Errors:** standard HTTP status codes; body `{ message: string, errors?: {field: [msg]} }`
  for validation errors (422).
- **Timestamps:** all data-freshness fields (`as_of`, `source_publication_date`) are ISO
  8601, per NFR-D1/NFR-D2.

## Endpoint Groups

| Group | Purpose | Auth |
|---|---|---|
| `/auth/*` | Register, login, logout | Public / session |
| `/dashboard/*` | Market dashboard aggregates | Public |
| `/companies/*` | Company search, profile, fundamentals, technicals | Public |
| `/watchlists/*` | CRUD for the current user's watchlists | Auth required |
| `/shariah/*` | Shariah status lookups, browse, admin import | Public (read) / Admin (import) |
| `/screener`, `/sectors` | Multi-criteria stock screener ([FRS Module 7](../01-requirements/functional-requirements-specification.md#module-7-stock-screener)) | Public |
| `/alerts/*` | Watchlist alert rules and triggered-alert history ([FRS Module 8](../01-requirements/functional-requirements-specification.md#module-8-watchlist-alerts)) | Auth required |
| `/portfolios/*` | Portfolio, transaction, and holdings management ([FRS Module 9](../01-requirements/functional-requirements-specification.md#module-9-portfolio-management)) | Auth required |

No endpoints exist for AI chat yet — see
[`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md).
