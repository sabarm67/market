# Use Cases

Scope: MVP modules only. Actors: **Guest**, **Registered Investor**, **Admin**.

## UC-1: Browse Market Dashboard

- **Actor:** Guest, Registered Investor
- **Precondition:** None
- **Flow:** Actor opens the dashboard → system displays top gainers/losers/volume, sector
  performance, market breadth, with "as of" timestamp → actor may filter by sub-market or
  sector.
- **Postcondition:** Actor has a current market overview.
- **Related FRs:** FR-DASH-1 to FR-DASH-6

## UC-2: Search and View Company Profile

- **Actor:** Guest, Registered Investor
- **Flow:** Actor searches by name/stock code → system shows autocomplete matches → actor
  selects a company → system displays overview, segments, management, major shareholders,
  with links to Fundamentals/Technicals/Shariah for that company.
- **Related FRs:** FR-COMP-1 to FR-COMP-6

## UC-3: View Fundamental Analysis

- **Actor:** Guest, Registered Investor
- **Precondition:** A company is selected (from UC-2 or direct navigation).
- **Flow:** Actor views current-period ratios (revenue, margins, EPS, ROE, PE, etc.) →
  actor optionally switches to historical trend view (3+ years).
- **Related FRs:** FR-FUND-1 to FR-FUND-7

## UC-4: View Basic Technical Chart

- **Actor:** Guest, Registered Investor
- **Precondition:** A company/security is selected.
- **Flow:** Actor views candlestick chart with default indicators (MA, Volume) → actor
  toggles Bollinger Bands / RSI / MACD → actor changes timeframe.
- **Related FRs:** FR-TECH-1 to FR-TECH-7

## UC-5: Manage Watchlist

- **Actor:** Registered Investor
- **Precondition:** Actor is logged in.
- **Flow:** Actor creates a watchlist → actor searches and adds securities → system shows
  current price, day change, Shariah status per item → actor may add notes or remove
  items or delete the watchlist.
- **Related FRs:** FR-WL-1 to FR-WL-5

## UC-6: Check Shariah Status

- **Actor:** Guest, Registered Investor
- **Flow:** Actor views a company's Shariah status (via Company Profile or the dedicated
  Shariah screen) → system shows current status + source publication date → actor may
  view historical status changes → actor may browse/filter the full Shariah screen by
  status.
- **Related FRs:** FR-SHR-1 to FR-SHR-4, FR-SHR-6

## UC-7: Import Shariah List Update (Admin)

- **Actor:** Admin
- **Precondition:** Admin is logged in with Admin role; has downloaded the latest SC
  Malaysia list file.
- **Flow:** Admin uploads the CSV/Excel file → system parses and shows a diff/preview of
  status changes → Admin reviews and confirms → system commits new ShariahStatus records
  with the source publication date → changes reflected across Company Profile, Watchlist,
  Dashboard, and Shariah screen.
- **Exception:** Malformed file → system rejects with a clear error, no partial commit.
- **Related FRs:** FR-SHR-5

## UC-8: Register and Log In

- **Actor:** Guest → becomes Registered Investor
- **Flow:** Guest registers with email/password → verifies email (if required) → logs in
  via Sanctum session auth.
- **Related:** [ADR-0004](../decisions/0004-auth-approach.md)

## Deferred Use Cases (Roadmap)

Configuring alerts, running the stock screener, managing a portfolio, chatting with the AI
research assistant, and reading AI-generated recommendations are not modeled here — see
[`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md).
