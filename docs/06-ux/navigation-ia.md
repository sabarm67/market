# Navigation / Information Architecture

Scope: MVP — 6 modules. Low-fidelity: describes structure and layout intent, not a
pixel-level design (high-fidelity mockups are a roadmap deliverable).

## Top-Level Navigation

```
┌─────────────────────────────────────────────────────────┐
│ Logo   Dashboard   Companies   Shariah   Watchlist   ⚙ Login/Account │
└─────────────────────────────────────────────────────────┘
```

- **Dashboard** — landing page (Guest + Registered).
- **Companies** — search entry point → Company Profile.
- **Shariah** — dedicated browse/filter screen.
- **Watchlist** — Registered Investors only; prompts login for Guests.
- **Account menu** — Login/Register (Guest) or Profile/Logout (Registered); Admin sees an
  additional **Admin** entry (Shariah Import, User Management).

## Site Map

```
Dashboard
├── Companies (search)
│   └── Company Profile [:stockCode]
│       ├── Overview (default tab)
│       ├── Fundamentals (tab)
│       ├── Technicals (tab)
│       └── Shariah (tab)
├── Shariah (browse/filter all securities)
├── Watchlist (auth required)
│   └── Watchlist Detail [:id]
├── Login / Register
├── Account (auth required)
└── Admin (admin required)
    ├── Shariah Import
    └── User Management
```

## Responsive Behavior

- Desktop: persistent top nav bar as above.
- Mobile (< 768px): top nav collapses into a hamburger menu; company profile tabs become
  a horizontally scrollable tab strip; charts remain interactive but default to a more
  compact indicator set.

## Theming

- Dark mode and light mode both supported (NFR-U1/U2); theme toggle in the account menu,
  persisted per user (localStorage for Guests, user preference for Registered).

## Data Freshness Indicator

Per BR3/NFR-D1, every screen showing price or fundamental data displays a small "as of
[timestamp]" label near the relevant data — this is a recurring UI pattern referenced in
each wireframe below rather than repeated in full each time.
