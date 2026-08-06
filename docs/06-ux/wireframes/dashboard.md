# Wireframe: Market Dashboard

Related: FR-DASH-1 to FR-DASH-6.

```
┌───────────────────────────────────────────────────────────────┐
│ [Top Nav]                                                      │
├───────────────────────────────────────────────────────────────┤
│ Filters: [Sub-market ▾] [Sector ▾]         as of: 6 Aug 2026   │
├───────────────────┬───────────────────┬─────────────────────┤
│ Top Gainers        │ Top Losers         │ Top Volume           │
│ 1. ABC  +8.2%       │ 1. XYZ  -6.1%      │ 1. DEF  12.4M         │
│ 2. ...              │ 2. ...             │ 2. ...                │
├───────────────────┴───────────────────┴─────────────────────┤
│ Sector Performance (bar chart, % change by sector)             │
├──────────────────────────────────────────────────────────────┤
│ Market Breadth                                                 │
│  Advancers: 412   Decliners: 298   52W Highs: 18   52W Lows: 7 │
└───────────────────────────────────────────────────────────────┘
```

## Notes

- Three-column "movers" layout collapses to a stacked single column on mobile.
- Clicking any stock code in the movers lists navigates to that Company Profile.
- Sector performance bar chart clicking a sector applies it as the dashboard filter.
- "as of" timestamp per BR3/NFR-D1, top-right of the filter row.
