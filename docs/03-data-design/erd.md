# Entity Relationship Diagram (ERD)

Scope: MVP entities only, per
[`glossary-data-dictionary.md`](../00-overview/glossary-data-dictionary.md#mvp-data-entities-see-erd-for-full-detail).
Deferred entities (Organization, Portfolio, Transaction, Alert, AlertChannel, NewsArticle,
Embedding/VectorStore) are listed in the appendix at the bottom, not modeled here.

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

Organization/Tenant (see [ADR-0001](../decisions/0001-tenancy-model.md)), Portfolio,
PortfolioHolding, Transaction, Alert, AlertChannel, NewsArticle, Embedding/VectorStore
(for AI/RAG), ScreenerQuery, Tier/Subscription. These will require schema additions when
their owning modules are built.
