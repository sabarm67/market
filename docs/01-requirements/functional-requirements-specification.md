# Functional Requirements Specification (FRS)

Scope: Phase 1 (MVP) — Bursa Malaysia only, 6 core modules. Full detail is given only for
these modules; everything else is a one-paragraph pointer to
[`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md).

## Module 1: Market Dashboard

| ID | Requirement |
|---|---|
| FR-DASH-1 | Display top gainers, top losers, and top volume for Bursa Malaysia (configurable count, e.g. top 10/20). |
| FR-DASH-2 | Display sector performance (aggregate % change by Bursa sector classification). |
| FR-DASH-3 | Display market breadth: advance/decline ratio, count of new 52-week highs/lows. |
| FR-DASH-4 | Display an "as of" timestamp for all dashboard data (per NFR-D1). |
| FR-DASH-5 | Allow filtering the dashboard by sub-market (Main/ACE/LEAP). |
| FR-DASH-6 | Allow filtering by sector/industry. |

*Global heat map, institutional/foreign fund flows, Fear & Greed index, and cross-market
comparisons are roadmap items (require multi-market data and are excluded per
[mvp-scope-definition.md](../00-overview/mvp-scope-definition.md)).*

## Module 2: Company Profile

| ID | Requirement |
|---|---|
| FR-COMP-1 | Display company overview: name, stock code, sub-market, sector/industry, listing date, brief description. |
| FR-COMP-2 | Display business segments (as disclosed in company filings, if structured data is available). |
| FR-COMP-3 | Display key management (Board of Directors, C-suite) names and titles. |
| FR-COMP-4 | Display major/substantial shareholders and their holding percentages. |
| FR-COMP-5 | Provide a company search (by name or stock code) with autocomplete. |
| FR-COMP-6 | Link from Company Profile to Fundamental Analysis, Basic Technical Analysis, and Shariah Compliance for the same company. |

*SWOT analysis, competitor comparison, corporate structure diagrams, and interactive
milestone timelines are roadmap items (require either AI generation or significant manual
content curation beyond MVP scope).*

## Module 3: Fundamental Analysis

| ID | Requirement |
|---|---|
| FR-FUND-1 | Display latest reported Revenue, Net Profit, Net Margin, Gross Margin, Operating Margin. |
| FR-FUND-2 | Display EPS, Book Value (NAV per share). |
| FR-FUND-3 | Display ROE, ROA, Debt/Equity, Current Ratio. |
| FR-FUND-4 | Display Dividend Yield and Dividend History (per-period dividends declared). |
| FR-FUND-5 | Display PE, PB (Price/Book) ratios, computed from latest price and latest reported fundamentals. |
| FR-FUND-6 | Display historical quarterly/annual figures for the above metrics (trend view, minimum 3 years where data available). |
| FR-FUND-7 | Show the source period ("as of FY2025 Q2") for every fundamental figure. |

*DCF valuation, intrinsic value, Piotroski/Altman Z/Beneish M scores, and AI-generated
investment scores (Quality/Value/Growth/Overall) are roadmap items — they require either
AI generation or a level of modeling sophistication deferred to Phase 2+.*

## Module 4: Basic Technical Analysis

| ID | Requirement |
|---|---|
| FR-TECH-1 | Display candlestick chart for a selected security, with timeframe selection (daily minimum; weekly/monthly if data supports). |
| FR-TECH-2 | Overlay Moving Average (simple, configurable period) on the chart. |
| FR-TECH-3 | Overlay Bollinger Bands. |
| FR-TECH-4 | Display RSI as a sub-chart indicator. |
| FR-TECH-5 | Display MACD as a sub-chart indicator. |
| FR-TECH-6 | Display volume as a sub-chart alongside price. |
| FR-TECH-7 | Allow basic chart interactions: zoom, pan, crosshair with value tooltip. |

*Multi-timeframe intraday/tick charts, Renko/Point&Figure/Market Profile, drawing tools
(trendlines/Fibonacci/Gann), auto pattern recognition, and AI technical analysis
(breakout/pattern detection, entry/target/stop-loss generation) are roadmap items — they
require either real-time/tick-level data or AI capabilities not available in MVP.*

## Module 5: Watchlist

| ID | Requirement |
|---|---|
| FR-WL-1 | A registered user can create multiple named watchlists. |
| FR-WL-2 | A user can add/remove Bursa-listed securities to/from a watchlist. |
| FR-WL-3 | A watchlist view shows current price, day change %, and Shariah status per security. |
| FR-WL-4 | A user can add a free-text note to a watchlist item. |
| FR-WL-5 | A user can delete a watchlist (with confirmation). |

*Colour coding, custom tags, alerting, AI monitoring/daily-weekly summaries are roadmap
items (alerting and AI summaries require the Alert Engine and AI modules, both out of MVP
scope).*

## Module 6: Shariah Compliance

| ID | Requirement |
|---|---|
| FR-SHR-1 | Display current Shariah status (Compliant / Non-Compliant) for any Bursa-listed security, sourced per [ADR-0003](../decisions/0003-shariah-data-sourcing.md). |
| FR-SHR-2 | Display the SC Malaysia source publication date for the current status. |
| FR-SHR-3 | Display historical Shariah status changes (status + effective publication date) where available. |
| FR-SHR-4 | Provide a dedicated Shariah screen: filter/browse all Bursa-listed securities by current Shariah status. |
| FR-SHR-5 | Admin-only: import a new SC Malaysia Shariah list (CSV/Excel upload), review parsed changes before committing, and commit the update. |
| FR-SHR-6 | Show Shariah status inline on Watchlist and Dashboard views. |

*Shariah screening ratio breakdowns (debt ratio, cash ratio, business activity detail),
dividend purification calculator, zakat estimation, and automatic change notifications are
roadmap items (notifications require the Alert Engine; ratio breakdowns require sourcing
SC's underlying screening data, not just the pass/fail list, which is a data-availability
question beyond the current manual-import approach).*

## Roadmap Modules (Not Specified Here)

AI Recommendation Engine, AI News Intelligence, AI Research Assistant, Stock Screener,
Portfolio Management, Alert Engine, advanced/TradingView-level charting, professional
report generation/export, and all non-Bursa-Malaysia markets. See
[`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md).
