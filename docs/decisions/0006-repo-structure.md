# ADR-0006: Repository Structure

## Status

Accepted (MVP)

## Context

Backend is Laravel 12/PHP 8.4, frontend is Vue 3/TypeScript. Both are being built by the
same small team for the same product in this phase.

## Decision

**Monorepo**, with the Laravel app at the **repo root** and the Vue 3 SPA in a
`/frontend` subdirectory, plus the `/docs` tree this document lives in.

Laravel-at-root (rather than nested under `/backend`) was chosen after hitting friction
deploying the nested layout on Laravel Forge — Forge's tooling (Web Directory default,
env file location, some site-management assumptions) is built around the Laravel app
living at the site root. Nesting it under `/backend` meant fighting that default
everywhere (see [ADR-0008](0008-hosting-provider.md)); keeping only the frontend in its
own subdirectory avoids the friction since Forge has no opinion about non-Laravel code
living alongside the app.

## Consequences

- Single source of truth for the whole product; easier to keep API contract (OpenAPI spec)
  and both codebases in sync during early, fast-moving development.
- Single CI/CD pipeline configuration (when that phase begins) can coordinate both sides.
- Forge deployment is simpler: default Web Directory (`public`), no `.env` bridging step,
  no `cd backend` wrapping in the deploy script — see
  [`deploy/forge-deploy.sh`](../../deploy/forge-deploy.sh).
- The Vue build output is emitted into `public/app` (see `frontend/vite.config.ts`) and
  served by Laravel's own catch-all route, so frontend and backend stay in one deployable
  unit even though only the frontend has its own subdirectory.
- If backend and frontend later need independent release cadences or separate teams/
  access control, they can still be split into separate repos — a reversible decision,
  not a one-way door.

## Alternatives Considered

- **Separate repos from day one** — rejected for MVP: adds cross-repo versioning overhead
  with no current need for independent deploy cadences or separate team access.
