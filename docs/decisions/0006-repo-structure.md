# ADR-0006: Repository Structure

## Status

Accepted (MVP)

## Context

Backend is Laravel 12/PHP 8.4, frontend is Vue 3/TypeScript. Both are being built by the
same small team for the same product in this phase.

## Decision

**Monorepo** with top-level `/backend` (Laravel) and `/frontend` (Vue 3 SPA) directories,
plus the `/docs` tree this document lives in.

## Consequences

- Single source of truth for the whole product; easier to keep API contract (OpenAPI spec)
  and both codebases in sync during early, fast-moving development.
- Single CI/CD pipeline configuration (when that phase begins) can coordinate both sides.
- If backend and frontend later need independent release cadences or separate teams/
  access control, they can be split into separate repos — a reversible decision, not a
  one-way door, since Laravel and Vue don't share build tooling that would make splitting
  hard.

## Alternatives Considered

- **Separate repos from day one** — rejected for MVP: adds cross-repo versioning overhead
  with no current need for independent deploy cadences or separate team access.
