# Business Process Flows

Scope: MVP modules only. Diagrams use Mermaid syntax.

## 1. Registration & Login

```mermaid
flowchart TD
    A[Guest visits site] --> B{Has account?}
    B -- No --> C[Register: email + password]
    C --> D[Verify email]
    D --> E[Log in via session auth]
    B -- Yes --> E
    E --> F[Access Registered Investor features]
```

## 2. Company Search & Research

```mermaid
flowchart TD
    A[Actor: Guest or Registered Investor] --> B[Search by name/stock code]
    B --> C[Select company from autocomplete]
    C --> D[View Company Profile]
    D --> E[View Fundamental Analysis]
    D --> F[View Basic Technical Chart]
    D --> G[View Shariah Status]
```

## 3. Watchlist Management

```mermaid
flowchart TD
    A[Registered Investor logs in] --> B[Create/select watchlist]
    B --> C[Search security]
    C --> D[Add to watchlist]
    D --> E[View watchlist: price, day change, Shariah status]
    E --> F{Manage item}
    F -- Add note --> E
    F -- Remove --> E
    B --> G[Delete watchlist]
```

## 4. Shariah Status Check (Investor)

```mermaid
flowchart TD
    A[Actor views Company Profile or Shariah screen] --> B[System shows current status + source date]
    B --> C{View history?}
    C -- Yes --> D[Show historical status changes]
    C -- No --> E[End]
```

## 5. Shariah List Import (Admin)

```mermaid
flowchart TD
    A[Admin downloads latest SC Malaysia list] --> B[Upload CSV/Excel to platform]
    B --> C[System parses file]
    C --> D{Valid format?}
    D -- No --> E[Reject with error, no partial commit]
    D -- Yes --> F[System shows preview/diff of status changes]
    F --> G[Admin reviews and confirms]
    G --> H[System commits new ShariahStatus records with publication date]
    H --> I[Updated status visible on Dashboard, Watchlist, Company Profile, Shariah screen]
```

## Deferred Flows (Roadmap)

Alert configuration, portfolio management, stock screening workflows, and AI research
assistant conversations are not modeled here — see
[`future-enhancements-roadmap.md`](../07-roadmap/future-enhancements-roadmap.md).
