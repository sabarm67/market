# ADR-0005: REST vs GraphQL

## Status

Accepted (MVP)

## Context

The original spec lists both REST and GraphQL as backend capabilities. MVP has a small,
well-defined set of endpoints (6 modules) consumed by a single first-party Vue SPA client.

## Decision

**REST, documented via OpenAPI**, for MVP. See [`openapi.yaml`](../05-api/openapi.yaml).

## Consequences

- Simpler tooling (Laravel's native REST/resource controllers), simpler caching (HTTP
  cache semantics), easier for a single client to consume.
- No GraphQL server/schema/resolver layer to build and maintain for MVP's limited endpoint
  surface.
- If a future need emerges for flexible, client-driven queries (e.g., a third-party
  developer API, or a mobile app with different data needs), GraphQL can be added
  alongside REST without replacing it — not a one-way door.

## Alternatives Considered

- **GraphQL from day one** — rejected for MVP: the endpoint surface is small and stable
  enough that GraphQL's flexibility isn't needed yet, and it adds schema/resolver overhead
  the team would carry with no current multi-client requirement.
