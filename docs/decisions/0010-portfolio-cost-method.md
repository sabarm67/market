# ADR-0010: Portfolio Cost Basis Method

## Status

Accepted — needed for the Portfolio Management module.

## Context

Portfolio Management (FRS Module 9) needs to compute holdings, cost basis, and gain/loss
from a buy/sell transaction ledger. The standard approaches are FIFO, LIFO, and average
cost — each gives a different cost basis (and therefore a different realized/unrealized
gain split) when multiple purchases at different prices exist for the same security.

## Decision

**Average cost method.** Each security's cost basis is a single running weighted-average
cost per share across all buy transactions; a sell realizes gain/loss against that average
and reduces the remaining cost basis proportionally. Implemented in
[`PortfolioCalculationService`](../../app/Services/PortfolioCalculationService.php),
computed on read (not stored) from the transaction ledger.

## Consequences

- Simpler to implement and explain than FIFO/LIFO lot tracking — no need to track
  individual purchase lots or match sells against specific lots.
- Matches how most Malaysian retail brokerage platforms display average cost, so the
  numbers should look familiar rather than surprising.
- Not tax-accurate for jurisdictions that mandate FIFO for capital gains reporting —
  acceptable since this platform is explicitly education/research, not a tax tool (tax
  estimation remains a roadmap item, and would need its own jurisdiction-specific method
  when built, independent of this display calculation).
- Computed on every read rather than cached — fine at current scale (small transaction
  counts per user); revisit if a portfolio's transaction history grows large enough for
  this to matter.

## Alternatives Considered

- **FIFO** — more common for formal tax reporting, but adds lot-tracking complexity not
  justified for an MVP-adjacent display feature.
- **LIFO** — rarely used for equities in this region; rejected for the same reason as FIFO
  plus being a less familiar default.
