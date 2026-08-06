# Wireframe: Fundamental Analysis (Company Profile → Fundamentals tab)

Related: FR-FUND-1 to FR-FUND-7.

```
┌───────────────────────────────────────────────────────────────┐
│ [Overview] [Fundamentals*] [Technicals] [Shariah]               │
├───────────────────────────────────────────────────────────────┤
│ Period: [Annual ▾]                    as of: FY2025            │
├───────────────────┬───────────────────┬─────────────────────┤
│ Revenue: RM 450M    │ Net Profit: RM 62M │ EPS: RM 0.34          │
│ Net Margin: 13.8%   │ ROE: 15.2%         │ ROA: 8.1%             │
│ Debt/Equity: 0.42   │ Current Ratio: 1.8 │ Div Yield: 3.2%       │
│ PE: 12.4            │ PB: 1.9            │ Book Value: RM 1.82   │
├───────────────────┴───────────────────┴─────────────────────┤
│ Historical Trend (3+ years, line/bar chart toggle)              │
│  [Revenue] [Net Profit] [EPS] [ROE] [Dividend]  ← metric picker │
│  (chart area)                                                   │
└───────────────────────────────────────────────────────────────┘
```

## Notes

- Metric tiles show latest reported period; "as of FYxxxx" label ties to FR-FUND-7.
- Historical trend section lets the user pick one metric at a time to chart across
  available periods (min. 3 years where data exists).
- Quarterly/Annual toggle re-fetches from `/companies/{stockCode}/fundamentals?period_type=`.
