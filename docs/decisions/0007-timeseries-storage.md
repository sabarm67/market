# ADR-0007: Time-Series Storage

## Status

Accepted (MVP), revisit at scale.

## Context

`price_data` (see [database-schema.md](../03-data-design/database-schema.md)) stores daily
EOD price/volume records. At MVP scale this is roughly 1,800 Bursa securities × 1 row/day
≈ 1,800 rows/day, ~650K rows/year — small by database standards.

## Decision

**Plain MySQL/MariaDB** (InnoDB), no dedicated time-series database (TimescaleDB,
InfluxDB) for MVP. The `(security_id, trade_date)` unique index and a `trade_date` index
are sufficient for both per-security chart queries and per-date dashboard aggregation at
this scale.

## Consequences

- No additional database technology to operate/learn for MVP.
- If the platform later adds intraday/tick-level data or expands to many more markets
  (roadmap), row counts could grow by orders of magnitude, at which point a dedicated
  time-series store should be re-evaluated — flagged here explicitly so it isn't
  forgotten.

## Alternatives Considered

- **TimescaleDB/InfluxDB from day one** — rejected for MVP: operational overhead not
  justified at ~650K rows/year; revisit when intraday data or multi-market scale is added.
