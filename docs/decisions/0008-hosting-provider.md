# ADR-0008: Hosting Provider

## Status

Accepted — resolved 2026-08-06.

## Context

No hosting/cloud provider account existed at documentation time (Dependency D4).
[Infrastructure Architecture](../04-architecture/infrastructure-architecture.md) was
written cloud-agnostic pending this decision.

## Decision

**Laravel Forge**, managing a single server hosting the site `market.rcaquacycle.com`,
with a MySQL database (`market`, user `adminmarket`). Forge deploys automatically on
push to the GitHub `main` branch.

Since the repo is a monorepo (`backend/` = Laravel, `frontend/` = Vue) rather than a
Laravel app at repo root, two Forge-specific adjustments apply:

- Forge's site **Web Directory** is set to `backend/public` (not the repo root).
- The Vue SPA is built during deploy straight into `backend/public/app`
  ([`vite.config.ts`](../../frontend/vite.config.ts)), and Laravel serves it via a
  catch-all route ([`routes/web.php`](../../backend/routes/web.php)) for any path that
  isn't `/api/*` or `/sanctum/*`. This makes the SPA and API same-origin in production,
  which is why [ADR-0002](0002-realtime-delivery-mechanism.md)'s CORS/Sanctum
  cross-domain setup is a dev-only concern — production needs neither.

See [`deploy/forge-deploy.sh`](../../deploy/forge-deploy.sh) for the deploy script and
[`backend/.env.production.example`](../../backend/.env.production.example) for the
environment template (Forge's Environment tab).

## Consequences

- Infrastructure Architecture's cloud-agnostic capability list now maps concretely to
  Forge-managed services: compute = the Forge server, DB = its MySQL instance, scheduled
  jobs = Forge's cron-managed Laravel Scheduler, queue = a Forge-managed queue worker.
- PDPA data-residency question (flagged as open in the original ADR) should be confirmed
  against the actual Forge server's region — not re-litigated here, but worth a follow-up
  check before handling real user data in production.
- Dependency D4 in [assumptions-dependencies-risks.md](../00-overview/assumptions-dependencies-risks.md)
  is resolved; update that log accordingly.

## Alternatives Considered

Not re-evaluated — the user had already provisioned Forge + the target domain before
this decision was recorded, so this ADR documents the decision made, not a comparison.
