# Data Flow Diagrams (DFD)

## Level 0 — Context Diagram

Shows the full long-term ecosystem for orientation. Items in *italics* are roadmap/Phase 2+
and not built in MVP.

```mermaid
flowchart LR
    User((Guest / Registered Investor))
    Admin((Admin))
    Provider[/Market Data Provider\n**pending — Dependency D1**/]
    SC[/SC Malaysia Shariah List\nmanual download/]
    AI[/*AI Provider\n(Claude/OpenAI) — pending D2*/]
    Platform[[Share Monitoring Platform]]

    Provider -. future automated feed .-> Platform
    SC -->|manual CSV/Excel import| Platform
    User <-->|browse, search, watchlist| Platform
    Admin -->|import Shariah data, manage users/companies| Platform
    Platform -. *future AI queries* .-> AI
```

## Level 1 — MVP Data Flows

```mermaid
flowchart TD
    subgraph Ingestion
        A1[Company/Price/Fundamental data\nmanually seeded or batch-imported for MVP\npending Dependency D1/D5]
        A2[Admin: Shariah CSV/Excel upload]
    end

    subgraph Storage
        B1[(Company / Security / Market data)]
        B2[(PriceData)]
        B3[(FundamentalData)]
        B4[(ShariahStatus)]
        B5[(User / Watchlist / WatchlistItem)]
    end

    subgraph API [Laravel API Layer]
        C1[Dashboard endpoints]
        C2[Company/Fundamentals/Technicals endpoints]
        C3[Watchlist endpoints]
        C4[Shariah endpoints]
    end

    subgraph Frontend [Vue 3 SPA]
        D1[Dashboard view]
        D2[Company Profile / Fundamentals / Technicals views]
        D3[Watchlist view]
        D4[Shariah screen]
    end

    A1 --> B1
    A1 --> B2
    A1 --> B3
    A2 --> B4

    B1 --> C2
    B2 --> C1
    B2 --> C2
    B3 --> C2
    B4 --> C4
    B5 --> C3

    C1 --> D1
    C2 --> D2
    C3 --> D3
    C4 --> D4
    B4 -.-> C3
    C3 --> D3
```

**Note:** the ingestion side (`A1`) is drawn generically because no market data provider is
contracted yet (Dependency D1). For MVP without a live provider, this may initially be a
manually seeded/imported dataset for demonstration purposes — see
[System Architecture](../04-architecture/system-architecture.md) for how ingestion is
architected to slot in a real provider later without redesign.

## Deferred Flows (Roadmap)

News ingestion, AI processing pipelines, alert dispatch (email/SMS/push/Telegram/
WhatsApp), and multi-market data flows are not modeled here — see
[`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md).
