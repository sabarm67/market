# ADR-0002: Real-Time Delivery Mechanism

## Status

Accepted (MVP) — confirmed with stakeholder.

## Context

No market data provider is contracted yet (Dependency D1). The original spec envisions
real-time monitoring via WebSockets. Building push infrastructure now, with nothing to
push, would be speculative.

## Decision

**Delayed/batch data for MVP.** Data is ingested via scheduled jobs (Laravel Scheduler),
e.g., EOD batch sync, and served from the database — no WebSocket/streaming layer is built
in MVP. The system displays an explicit "as of" timestamp on all price/fundamental data
(NFR-D1) so users always know data freshness.

The WebSocket layer is **reserved, not built**: [System Architecture](../../04-architecture/system-architecture.md)
notes where it would slot in (e.g., Laravel Reverb) once a push-capable data provider is
contracted, so adding it later doesn't require re-architecting ingestion.

## Consequences

- Simpler MVP: no WebSocket server, no client-side subscription/reconnection logic, no
  need to solve provider push-format normalization yet.
- Users get delayed data (freshness bounded by ingestion job frequency), an accepted MVP
  limitation.
- Ingestion is designed as a swappable job (see System Architecture) — moving to a
  real-time provider later changes the ingestion job's data source, not the schema or API
  layer.

## Alternatives Considered

- **Build WebSocket infrastructure now, backfill real data later** — rejected: no real
  push source exists to validate against, high risk of building the wrong abstraction.
