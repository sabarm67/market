# Role & Access Tier Matrix

Scope: MVP has a single free tier (see [`mvp-scope-definition.md`](../00-overview/mvp-scope-definition.md#tiers))
and three roles: Guest, Registered Investor, Admin. No Premium/Professional/Research/
Institution tiers in MVP — those are roadmap (see bottom of this doc).

## Access by Module

| Module | Guest | Registered Investor | Admin |
|---|---|---|---|
| Market Dashboard | View | View | View |
| Company Profile | View | View | View + edit company metadata |
| Fundamental Analysis | View | View | View |
| Basic Technical Analysis | View | View | View |
| Watchlist | — (no account) | Create/edit/delete own | View own; no access to other users' watchlists |
| Shariah Compliance (view) | View | View | View |
| Shariah Compliance (import) | — | — | Import/preview/commit SC list updates |
| User Management | — | Manage own profile | Manage all users (activate/deactivate, role changes) |

## Role Definitions

| Role | Description |
|---|---|
| Guest | Unauthenticated visitor. Full read access to public market/company/fundamental/technical/Shariah data. No watchlist capability (requires an account). |
| Registered Investor | Authenticated user, single free tier. All Guest capabilities plus personal watchlists. |
| Admin | Internal operator role. All Registered Investor capabilities plus Shariah data import and user/company data management. Not a customer-facing tier. |

## Enforcement

- Enforced server-side via Laravel middleware/policies (see
  [Security Architecture](../04-architecture/security-architecture.md)), not just hidden
  in the UI.
- Admin-only endpoints (Shariah import, user management) return 403 for non-Admin roles
  regardless of UI state.

## Roadmap Tiers (Not in MVP)

Premium, Professional, Research, and Institution tiers — with differentiated access to
future modules (screener, portfolio management, AI research assistant, professional
report export, multi-market data) and billing — are deferred. Introducing them later
will require adding a `tier` concept to the User entity and tier-gating logic across
modules, which the MVP data model does not preclude but also does not build now.
