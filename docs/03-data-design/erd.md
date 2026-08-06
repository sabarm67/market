# Entity Relationship Diagram (ERD)

Scope: MVP entities, per
[`glossary-data-dictionary.md`](../00-overview/glossary-data-dictionary.md#mvp-data-entities-see-erd-for-full-detail),
plus post-MVP entities added for the Stock Screener, Watchlist Alerts, and Portfolio
Management modules (see [§ Post-MVP Additions](#post-mvp-additions) below). Remaining
deferred entities (Organization, NewsArticle, Embedding/VectorStore) are listed in the
appendix at the bottom, not modeled here.

## Diagram

```mermaid
erDiagram
    MARKET ||--o{ COMPANY : lists
    SECTOR ||--o{ COMPANY : classifies
    COMPANY ||--|| SECURITY : "has (1:1 in MVP)"
    SECURITY ||--o{ PRICE_DATA : "has daily"
    COMPANY ||--o{ FUNDAMENTAL_DATA : "reports"
    SECURITY ||--o{ SHARIAH_STATUS : "has history of"
    USER ||--o{ WATCHLIST : owns
    WATCHLIST ||--o{ WATCHLIST_ITEM : contains
    SECURITY ||--o{ WATCHLIST_ITEM : "referenced by"

    MARKET {
        bigint id PK
        string name "Bursa Malaysia"
        string sub_market "Main, ACE, LEAP"
    }

    SECTOR {
        bigint id PK
        string name
        string industry
    }

    COMPANY {
        bigint id PK
        bigint market_id FK
        bigint sector_id FK
        string name
        string stock_code UK
        text overview
        text business_segments
        date listing_date
        timestamp updated_at
    }

    SECURITY {
        bigint id PK
        bigint company_id FK
        string ticker UK
        string type "ordinary shares (MVP)"
    }

    PRICE_DATA {
        bigint id PK
        bigint security_id FK
        date trade_date
        decimal open
        decimal high
        decimal low
        decimal close
        bigint volume
        timestamp ingested_at
    }

    FUNDAMENTAL_DATA {
        bigint id PK
        bigint company_id FK
        string period_type "quarterly, annual"
        date period_end
        decimal revenue
        decimal net_profit
        decimal eps
        decimal book_value_per_share
        decimal roe
        decimal roa
        decimal debt_equity
        decimal current_ratio
        decimal dividend_per_share
        bigint shares_outstanding "added for Stock Screener market cap"
        timestamp ingested_at
    }

    SHARIAH_STATUS {
        bigint id PK
        bigint security_id FK
        string status "compliant, non_compliant"
        date source_publication_date
        timestamp imported_at
        bigint imported_by_user_id FK
    }

    USER {
        bigint id PK
        string email UK
        string password_hash
        string role "guest_n_a, registered, admin"
        timestamp created_at
    }

    WATCHLIST {
        bigint id PK
        bigint user_id FK
        string name
        timestamp created_at
    }

    WATCHLIST_ITEM {
        bigint id PK
        bigint watchlist_id FK
        bigint security_id FK
        text note
        timestamp added_at
    }
```

## Post-MVP Additions

Stock Screener (FRS Module 7) needed no new entities — only `shares_outstanding` on
`FUNDAMENTAL_DATA`, shown above. Watchlist Alerts and Portfolio Management added these:

```mermaid
erDiagram
    WATCHLIST_ITEM ||--o{ ALERT_RULE : "has"
    ALERT_RULE ||--o{ ALERT_TRIGGER : "produces"
    USER ||--o{ PORTFOLIO : owns
    PORTFOLIO ||--o{ PORTFOLIO_TRANSACTION : "has"
    SECURITY ||--o{ PORTFOLIO_TRANSACTION : "referenced by"

    ALERT_RULE {
        bigint id PK
        bigint watchlist_item_id FK
        string type "price_change_pct, volume_spike, new_52w_high, new_52w_low, shariah_status_change"
        string direction "up, down, either — price_change_pct only"
        decimal threshold "nullable — n/a for high/low/shariah types"
        boolean active
        timestamp created_at
        timestamp updated_at
    }

    ALERT_TRIGGER {
        bigint id PK
        bigint alert_rule_id FK
        date trigger_date "trading day evaluated; unique with alert_rule_id"
        text message
        timestamp notified_at "set once included in a sent digest"
        timestamp read_at
    }

    PORTFOLIO {
        bigint id PK
        bigint user_id FK
        string name
        timestamp created_at
        timestamp updated_at
    }

    PORTFOLIO_TRANSACTION {
        bigint id PK
        bigint portfolio_id FK
        bigint security_id FK
        string type "buy, sell"
        decimal quantity
        decimal price "per share at transaction"
        date transaction_date
        text notes
        timestamp created_at
    }
```

Notes:

- **AlertRule attaches to WatchlistItem, not Security directly** — alerts are explicitly
  "Watchlist Alerts" (FR-ALT-1); a security must already be on a watchlist to alert on it.
- **AlertTrigger is append-only with a `(alert_rule_id, trigger_date)` unique constraint**
  — prevents duplicate triggers if the daily evaluation command reruns (FR-ALT-5).
- **Portfolio holdings/cost-basis/gain-loss are computed, not stored** — derived from
  `PORTFOLIO_TRANSACTION` on every read via average cost method
  ([ADR-0010](../decisions/0010-portfolio-cost-method.md)), so there's no `PortfolioHolding`
  table.

## Notes

- **Company : Security is 1:1 in MVP** — Bursa-listed companies in scope have a single
  ordinary-share listing. Modeled as a separate entity (not merged into Company) so
  multiple security types (e.g., preferred shares, warrants) can be added later without
  restructuring Company.
- **ShariahStatus is append-only** — each SC Malaysia list import creates new rows rather
  than updating in place, preserving history (supports FR-SHR-3) and traceability to
  `imported_by_user_id` (Admin accountability for FR-SHR-5).
- **User.role** is a simple enum (`registered`, `admin`) rather than a separate roles
  table — matches the small, fixed role set in
  [role-access-tier-matrix.md](../01-requirements/role-access-tier-matrix.md); Guest is
  unauthenticated and has no User row.
- **PriceData granularity is daily (EOD)** per [ADR-0002](../decisions/0002-realtime-delivery-mechanism.md)
  — no intraday/tick table in MVP.

## Appendix: Deferred Entities (Roadmap, Not Modeled Here)

Organization/Tenant (see [ADR-0001](../decisions/0001-tenancy-model.md)), AlertChannel
(SMS/Telegram/WhatsApp — email-only for now, see [ADR-0009](../decisions/0009-email-provider.md)),
NewsArticle, Embedding/VectorStore (for AI/RAG), Tier/Subscription. These will require
schema additions when their owning modules are built.
