# Wireframe: Shariah Compliance

Related: FR-SHR-1 to FR-SHR-6. Two views: public browse screen, and the Company Profile
Shariah tab; plus an Admin-only import screen.

## Public Shariah Browse Screen

```
┌───────────────────────────────────────────────────────────────┐
│ [Top Nav]                                                      │
├───────────────────────────────────────────────────────────────┤
│ Shariah-Compliant Securities         Source: SC Malaysia list  │
│ Status: [Compliant ▾]                as of: 30 May 2026        │
├───────────────────────────────────────────────────────────────┤
│ Stock      Name              Sector       Status                │
│ 1234       ABC Berhad        Technology   ✅ Compliant           │
│ 5678       DEF Holdings      Consumer     ❌ Non-Compliant       │
│ ...                                                              │
└───────────────────────────────────────────────────────────────┘
```

## Company Profile → Shariah Tab

```
┌───────────────────────────────────────────────────────────────┐
│ [Overview] [Fundamentals] [Technicals] [Shariah*]                │
├───────────────────────────────────────────────────────────────┤
│ Current Status: ✅ Compliant                                    │
│ Source: SC Malaysia list, published 30 May 2026                 │
├───────────────────────────────────────────────────────────────┤
│ History                                                         │
│  30 May 2026 — Compliant                                        │
│  28 Nov 2025 — Compliant                                        │
│  29 May 2025 — Non-Compliant                                    │
└───────────────────────────────────────────────────────────────┘
```

## Admin: Shariah List Import (Admin role required)

```
┌───────────────────────────────────────────────────────────────┐
│ Admin > Shariah Import                                          │
├───────────────────────────────────────────────────────────────┤
│ [Choose File: sc-shariah-list-2026-05.xlsx]     [Upload & Preview] │
├───────────────────────────────────────────────────────────────┤
│ Preview — 1,842 securities parsed, 23 status changes detected   │
│  Stock   Name          Old Status      New Status                │
│  1234    ABC Berhad    Non-Compliant → Compliant                 │
│  ...                                                              │
├───────────────────────────────────────────────────────────────┤
│               [Cancel]              [Confirm & Commit]           │
└───────────────────────────────────────────────────────────────┘
```

## Notes

- Malformed file upload shows an inline error, no preview/commit possible (per UC-7
  exception flow).
- Commit is a distinct step after preview per FR-SHR-5 — no auto-commit on upload.
