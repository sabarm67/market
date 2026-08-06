# ADR-0008: Hosting Provider

## Status

**Deferred — not decided.**

## Context

No hosting/cloud provider account exists yet (Dependency D4). Candidates mentioned in the
original spec/context include major cloud providers (AWS/Azure/GCP) and local Malaysian
hosting options, the latter potentially relevant for PDPA data-residency considerations.

## Decision

Not made in this phase. [Infrastructure Architecture](../04-architecture/infrastructure-architecture.md)
is written **cloud-agnostic** — describing required capabilities (compute, MySQL-compatible
DB, Redis, object storage, scheduled jobs) rather than provider-specific services — so the
architecture doc remains valid regardless of which provider is eventually chosen.

## Consequences

- Infrastructure Architecture cannot specify exact managed-service names (e.g., RDS vs
  Cloud SQL vs Azure Database) until this is resolved.
- This decision should be revisited before the deployment/DevOps phase begins, factoring
  in cost, PDPA data-residency preferences, and team familiarity.

## Alternatives Considered

Not evaluated yet — explicitly out of scope for this documentation phase; tracked as
Dependency D4 in [assumptions-dependencies-risks.md](../00-overview/assumptions-dependencies-risks.md).
