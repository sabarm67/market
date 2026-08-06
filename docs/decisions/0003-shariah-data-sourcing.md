# ADR-0003: Shariah Data Sourcing

## Status

Accepted (MVP) — confirmed with stakeholder.

## Context

Securities Commission Malaysia (SC) publishes the official list of Shariah-compliant
securities twice a year (May and November) as a PDF/Excel document. There is no public
API. The Shariah Compliance module is an MVP-scope module and needs a concrete data
sourcing plan, not just a future promise.

## Decision

**Manual curated import.** An Admin downloads the official SC Malaysia list at each
publication and imports it into the platform via a curated CSV/Excel import process
(admin-only, not user-facing). Each `ShariahStatus` record stores the source publication
date, so the UI can show "as of [date]" and historical status.

## Consequences

- No automation/scraping dependency — avoids fragile scraping of SC Malaysia's site and
  avoids a hard blocker on API access that doesn't exist.
- Status freshness is bounded by the admin's import cadence (target: within days of each
  SC publication) — this is an accepted MVP limitation, tracked as Risk R2 in
  [`assumptions-dependencies-risks.md`](../00-overview/assumptions-dependencies-risks.md).
- Requires an Admin-only import tool/workflow in FRS and an `ShariahStatus` entity with
  publication-date versioning in the ERD.
- Intra-period Shariah status changes (rare, but can occur via corporate exercises) are
  not captured until the next official list — documented as a known limitation, not solved
  in MVP.

## Alternatives Considered

- **Licensed data vendor with Shariah screening included** — better long-term but requires
  procurement/budget not currently available (see Dependency D1/D5); revisit for Phase 2.
- **Automated scraping of SC Malaysia's website** — rejected: fragile, and the source is a
  static document release rather than a queryable feed, so scraping offers little benefit
  over a manual download-and-import step.
