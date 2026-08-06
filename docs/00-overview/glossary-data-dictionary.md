# Glossary & Data Dictionary

Living document — updated as new terms/entities are introduced in later docs.

## Business / Domain Terms

| Term | Definition |
|---|---|
| Bursa Malaysia | Malaysia's stock exchange, comprising the Main Market, ACE Market, and LEAP Market. |
| Main Market | Bursa Malaysia's board for larger, established companies. |
| ACE Market | Bursa Malaysia's sponsor-driven board for growth companies (formerly MESDAQ). |
| LEAP Market | Bursa Malaysia's board for advanced/sophisticated investors only. |
| Shariah-compliant security | A security classified by the Securities Commission Malaysia (SC) as meeting Islamic finance screening criteria (business activity and financial ratio benchmarks). |
| SC Malaysia | Securities Commission Malaysia — the regulator that publishes the official Shariah-compliant securities list. |
| Counter | Colloquial Bursa Malaysia term for a listed security/stock. |
| EOD | End of Day — data as of market close, as opposed to intraday/real-time. |
| PDPA | Personal Data Protection Act 2010 (Malaysia) — the data-protection law governing personal data handling. |
| Watchlist | A user-curated list of securities to monitor. |
| Fundamental Analysis | Evaluation of a company based on financial statements and ratios (revenue, margins, ROE, valuation, etc.). |
| Technical Analysis | Evaluation of a security's price/volume chart patterns and indicators. |

## MVP Data Entities (see [ERD](../03-data-design/erd.md) for full detail)

| Entity | Definition |
|---|---|
| User | A person with an account (Registered Investor or Admin role). |
| Company | A Bursa Malaysia-listed issuer. |
| Security / Instrument | A tradeable listing tied to a Company (MVP: one security per company — ordinary shares). |
| Market / Exchange | Bursa Malaysia, with sub-market (Main/ACE/LEAP). |
| Sector / Industry | Bursa Malaysia's official sector/industry classification. |
| PriceData | Daily (EOD) price/volume records for a Security. |
| FundamentalData | Periodic (quarterly/annual) financial statement and ratio data for a Company. |
| ShariahStatus | A Security's Shariah classification as of a given SC Malaysia list publication date. |
| Watchlist | A named collection owned by a User. |
| WatchlistItem | A Security within a Watchlist, with optional notes/tags. |

## Abbreviations

| Abbreviation | Meaning |
|---|---|
| BRS | Business Requirements Specification |
| FRS | Functional Requirements Specification |
| NFR | Non-Functional Requirements |
| ERD | Entity Relationship Diagram |
| DFD | Data Flow Diagram |
| ADR | Architecture Decision Record |
| RBAC | Role-Based Access Control |
| EPS | Earnings Per Share |
| ROE | Return on Equity |
| ROA | Return on Assets |
| PE | Price-to-Earnings ratio |
| PB | Price-to-Book ratio |
| RSI | Relative Strength Index |
| MACD | Moving Average Convergence Divergence |
