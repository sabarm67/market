# ADR-0004: Authentication Approach

## Status

Accepted (MVP)

## Context

System Architecture is a Laravel 12 backend serving a Vue 3 SPA (see
[ADR-0006](0006-repo-structure.md)). MVP has a single free tier (no billing) and a small
role set (Guest, Registered Investor, Admin) — see [ADR-0001](0001-tenancy-model.md).

## Decision

**Laravel Sanctum**, using its SPA cookie-based authentication mode (not token-based API
mode). This is Laravel's built-in fit for a first-party SPA sharing the same top-level
domain, without the overhead of a full OAuth2 server (Passport).

## Consequences

- Simple session-based auth for the Vue SPA; CSRF-protected.
- If a public third-party API (for external developers) is needed later, Sanctum's token
  mode or a move to Passport can be layered on without replacing the core auth model.
- Third-party OAuth login (Google/Facebook) is not in MVP scope but can be added via
  Laravel Socialite alongside Sanctum without architectural conflict.
- MFA/biometric login (mentioned in the original spec) is deferred to the roadmap — MVP
  ships with email/password + Sanctum session auth only.

## Alternatives Considered

- **Laravel Passport (OAuth2)** — rejected for MVP: designed for third-party API clients
  and multiple OAuth grant types, more complexity than a single first-party SPA needs.
- **Stateless JWT** — rejected: loses Laravel's built-in CSRF/session protections for
  same-domain SPA use without a corresponding benefit at MVP scale.
