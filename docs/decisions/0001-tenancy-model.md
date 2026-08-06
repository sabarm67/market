# ADR-0001: Tenancy Model

## Status

Accepted (MVP)

## Context

The full platform vision includes an "Institution" role and mentions of research houses
and educational institutions as user types, which could imply multi-tenant organizational
accounts (shared logins, org-level data, org billing). MVP scope, however, is a single
free tier for individual registered users.

## Decision

**Single-tenant model.** Every account is an individual User. There is no Organization/
Tenant entity in MVP. Data (watchlists, notes) is scoped strictly to the owning User.
Admin is a role on a User account, not a separate tenant.

## Consequences

- Simplifies FRS, ERD (no Organization entity), and Security Architecture (no cross-tenant
  isolation concerns) for MVP.
- Institution-level accounts (shared seats, org billing, org-wide watchlists) are deferred
  to the roadmap — introducing multi-tenancy later will require adding an Organization
  entity and reworking access control, which is an accepted future cost.

## Alternatives Considered

- **Multi-tenant with Organization entity from day one** — rejected for MVP: adds schema
  and auth complexity with no current customer (research house/institution) committed to
  need it yet.
