# Wireframe: Basic Technical Analysis (Company Profile → Technicals tab)

Related: FR-TECH-1 to FR-TECH-7.

```
┌───────────────────────────────────────────────────────────────┐
│ [Overview] [Fundamentals] [Technicals*] [Shariah]                │
├───────────────────────────────────────────────────────────────┤
│ Timeframe: [1D][1W][1M][3M][1Y]    Indicators: [MA▾][BB][RSI][MACD] │
├───────────────────────────────────────────────────────────────┤
│                                                                   │
│           (candlestick chart with MA/BB overlay)                 │
│                                                                   │
├───────────────────────────────────────────────────────────────┤
│           (volume sub-chart)                                     │
├───────────────────────────────────────────────────────────────┤
│           (RSI sub-chart, if toggled)                            │
├───────────────────────────────────────────────────────────────┤
│           (MACD sub-chart, if toggled)                           │
└───────────────────────────────────────────────────────────────┘
```

## Notes

- Indicator toggles are independent checkboxes/buttons — MA and Bollinger Bands overlay
  directly on the price chart; RSI and MACD render as stacked sub-charts below volume.
- Crosshair on hover shows OHLCV + active indicator values in a tooltip (FR-TECH-7).
- No drawing tools, multi-chart layout, or pattern annotations in MVP — those are roadmap.
