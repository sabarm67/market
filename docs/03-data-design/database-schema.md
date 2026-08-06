# Database Schema

Documented reference DDL for MySQL/MariaDB, matching [`erd.md`](erd.md). This is a design
reference, not live Laravel migration files (those are produced in the build phase).

## `markets`

```sql
CREATE TABLE markets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,               -- 'Bursa Malaysia'
    sub_market VARCHAR(20) NOT NULL,          -- 'Main', 'ACE', 'LEAP'
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_markets_submarket (name, sub_market)
);
```

## `sectors`

```sql
CREATE TABLE sectors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    industry VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## `companies`

```sql
CREATE TABLE companies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    market_id BIGINT UNSIGNED NOT NULL,
    sector_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    stock_code VARCHAR(20) NOT NULL,
    overview TEXT NULL,
    business_segments TEXT NULL,
    listing_date DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_companies_stock_code (stock_code),
    KEY idx_companies_market (market_id),
    KEY idx_companies_sector (sector_id),
    CONSTRAINT fk_companies_market FOREIGN KEY (market_id) REFERENCES markets(id),
    CONSTRAINT fk_companies_sector FOREIGN KEY (sector_id) REFERENCES sectors(id)
);
```

## `securities`

```sql
CREATE TABLE securities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    ticker VARCHAR(20) NOT NULL,
    type VARCHAR(30) NOT NULL DEFAULT 'ordinary_shares',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_securities_ticker (ticker),
    KEY idx_securities_company (company_id),
    CONSTRAINT fk_securities_company FOREIGN KEY (company_id) REFERENCES companies(id)
);
```

## `price_data`

```sql
CREATE TABLE price_data (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    security_id BIGINT UNSIGNED NOT NULL,
    trade_date DATE NOT NULL,
    open DECIMAL(12,4) NOT NULL,
    high DECIMAL(12,4) NOT NULL,
    low DECIMAL(12,4) NOT NULL,
    close DECIMAL(12,4) NOT NULL,
    volume BIGINT UNSIGNED NOT NULL DEFAULT 0,
    ingested_at TIMESTAMP NULL,
    UNIQUE KEY uq_price_security_date (security_id, trade_date),
    KEY idx_price_date (trade_date),
    CONSTRAINT fk_price_security FOREIGN KEY (security_id) REFERENCES securities(id)
);
```

*Indexing note: `(security_id, trade_date)` unique index supports both "latest N days for
a security" (chart rendering) and "all securities on a given date" (dashboard aggregation)
query patterns. See [ADR-0007](../decisions/0007-timeseries-storage.md) for whether plain
MySQL suffices at MVP scale.*

## `fundamental_data`

```sql
CREATE TABLE fundamental_data (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    period_type VARCHAR(10) NOT NULL,          -- 'quarterly', 'annual'
    period_end DATE NOT NULL,
    revenue DECIMAL(18,2) NULL,
    net_profit DECIMAL(18,2) NULL,
    eps DECIMAL(10,4) NULL,
    book_value_per_share DECIMAL(10,4) NULL,
    roe DECIMAL(6,3) NULL,
    roa DECIMAL(6,3) NULL,
    debt_equity DECIMAL(6,3) NULL,
    current_ratio DECIMAL(6,3) NULL,
    dividend_per_share DECIMAL(10,4) NULL,
    shares_outstanding BIGINT UNSIGNED NULL,   -- added post-MVP for Stock Screener market cap
    ingested_at TIMESTAMP NULL,
    UNIQUE KEY uq_fundamental_company_period (company_id, period_type, period_end),
    CONSTRAINT fk_fundamental_company FOREIGN KEY (company_id) REFERENCES companies(id)
);
```

## `shariah_statuses`

```sql
CREATE TABLE shariah_statuses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    security_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(20) NOT NULL,               -- 'compliant', 'non_compliant'
    source_publication_date DATE NOT NULL,
    imported_at TIMESTAMP NOT NULL,
    imported_by_user_id BIGINT UNSIGNED NOT NULL,
    KEY idx_shariah_security (security_id, source_publication_date),
    CONSTRAINT fk_shariah_security FOREIGN KEY (security_id) REFERENCES securities(id),
    CONSTRAINT fk_shariah_admin FOREIGN KEY (imported_by_user_id) REFERENCES users(id)
);
```

*Append-only per [erd.md](erd.md) notes — "current status" is derived as the row with the
latest `source_publication_date` per `security_id`, not updated in place.*

## `users`

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'registered',  -- 'registered', 'admin'
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_users_email (email)
);
```

## `watchlists`

```sql
CREATE TABLE watchlists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    KEY idx_watchlists_user (user_id),
    CONSTRAINT fk_watchlists_user FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## `watchlist_items`

```sql
CREATE TABLE watchlist_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    watchlist_id BIGINT UNSIGNED NOT NULL,
    security_id BIGINT UNSIGNED NOT NULL,
    note TEXT NULL,
    added_at TIMESTAMP NOT NULL,
    UNIQUE KEY uq_watchlist_security (watchlist_id, security_id),
    CONSTRAINT fk_wi_watchlist FOREIGN KEY (watchlist_id) REFERENCES watchlists(id) ON DELETE CASCADE,
    CONSTRAINT fk_wi_security FOREIGN KEY (security_id) REFERENCES securities(id)
);
```

## `alert_rules`

```sql
CREATE TABLE alert_rules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    watchlist_item_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(30) NOT NULL,          -- 'price_change_pct', 'volume_spike', 'new_52w_high', 'new_52w_low', 'shariah_status_change'
    direction VARCHAR(10) NULL,         -- 'up', 'down', 'either' — price_change_pct only
    threshold DECIMAL(10,4) NULL,       -- % for price_change_pct, multiplier for volume_spike
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_alert_rules_item FOREIGN KEY (watchlist_item_id) REFERENCES watchlist_items(id) ON DELETE CASCADE
);
```

## `alert_triggers`

```sql
CREATE TABLE alert_triggers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alert_rule_id BIGINT UNSIGNED NOT NULL,
    trigger_date DATE NOT NULL,         -- trading day evaluated
    message TEXT NOT NULL,
    notified_at TIMESTAMP NULL,         -- set once included in a sent digest email
    read_at TIMESTAMP NULL,
    UNIQUE KEY uq_alert_trigger_date (alert_rule_id, trigger_date),
    CONSTRAINT fk_alert_triggers_rule FOREIGN KEY (alert_rule_id) REFERENCES alert_rules(id) ON DELETE CASCADE
);
```

*Unique key on `(alert_rule_id, trigger_date)` prevents duplicate triggers if the daily
evaluation command reruns for the same trading day (FR-ALT-5).*

## `portfolios`

```sql
CREATE TABLE portfolios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_portfolios_user FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## `portfolio_transactions`

```sql
CREATE TABLE portfolio_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    portfolio_id BIGINT UNSIGNED NOT NULL,
    security_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(10) NOT NULL,          -- 'buy', 'sell'
    quantity DECIMAL(15,4) NOT NULL,
    price DECIMAL(12,4) NOT NULL,       -- per share at transaction
    transaction_date DATE NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    KEY idx_portfolio_txn (portfolio_id, security_id),
    CONSTRAINT fk_pt_portfolio FOREIGN KEY (portfolio_id) REFERENCES portfolios(id) ON DELETE CASCADE,
    CONSTRAINT fk_pt_security FOREIGN KEY (security_id) REFERENCES securities(id)
);
```

*No `portfolio_holdings` table — holdings, average cost, and gain/loss are computed on
read from this ledger via the average cost method
([ADR-0010](../decisions/0010-portfolio-cost-method.md)), not stored.*

## Deferred Tables (Roadmap Appendix)

`organizations`, `alert_channels` (SMS/Telegram/WhatsApp — email-only for now, see
[ADR-0009](../decisions/0009-email-provider.md)), `news_articles`, `embeddings` (vector
store for RAG), `subscriptions`/`tiers`. Not created in this phase — see
[`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md).
