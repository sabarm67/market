# Security Architecture

Scope: MVP. See [NFR §5](../01-requirements/non-functional-requirements.md) and
[Role & Access Tier Matrix](../01-requirements/role-access-tier-matrix.md).

## Authentication

- Laravel Sanctum, SPA cookie-based session auth (per [ADR-0004](../decisions/0004-auth-approach.md)).
- Passwords hashed with bcrypt/argon2 (Laravel default), never stored or logged in
  plaintext.
- CSRF protection via Sanctum's cookie/token mechanism for all state-changing requests.

## Authorization (RBAC)

- Three roles: Guest (unauthenticated), Registered Investor, Admin — per
  [role-access-tier-matrix.md](../01-requirements/role-access-tier-matrix.md).
- Enforced server-side via Laravel Policies/Middleware on every endpoint — the frontend
  hiding a button is never the sole access control.
- Admin-only actions (Shariah import, user management, company metadata edit) require an
  explicit Admin-role check, returning HTTP 403 otherwise.
- Watchlist ownership enforced per-request (a user can only read/modify their own
  watchlists — checked via `watchlist.user_id === auth()->id()`, not just route
  visibility).

## Transport & Data Protection

- HTTPS/TLS enforced for all traffic (HSTS recommended).
- Database credentials, API keys (future), and other secrets stored in environment
  config, never committed to the repository.
- MySQL connection encrypted in transit where the hosting environment supports it.

## API Protection

- Rate limiting (Laravel's built-in throttle middleware) on authentication endpoints
  (login/register) to mitigate brute-force/credential-stuffing attempts.
- Rate limiting on search/autocomplete endpoints to prevent scraping abuse.

## PDPA (Malaysia) Compliance

- Collect only data necessary for the service: email, password, watchlist data. No
  unnecessary personal data collection in MVP (no phone number, no ID number, no address).
- Users can request account deletion; deletion cascades to owned watchlists
  (`ON DELETE CASCADE` on `watchlist_items` per [database-schema.md](../03-data-design/database-schema.md);
  `watchlists` deletion on user deletion to be enforced at the application/service layer
  or an equivalent cascade).
- Document a basic retention policy: account data retained while active; deletion request
  honored within a defined window (exact SLA TBD, recommend legal input).
- **Recommend formal legal/compliance review before production launch** — this document
  is a good-faith technical baseline, not a substitute for legal sign-off (see Risk R4 in
  [assumptions-dependencies-risks.md](../00-overview/assumptions-dependencies-risks.md)).

## Audit Logging

- Log Admin actions with accountability: Shariah import events already carry
  `imported_by_user_id` (per [erd.md](../03-data-design/erd.md)); extend the same pattern
  to company metadata edits and user role changes.

## Explicitly Deferred (Roadmap)

MFA/biometric login, OAuth/SSO third-party login, formal penetration testing, SOC 2/ISO
27001 certification, GDPR-specific tooling (MVP targets PDPA Malaysia; GDPR support is a
Phase 2+ concern if/when the platform serves EU users).
