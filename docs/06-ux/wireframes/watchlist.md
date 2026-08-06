# Wireframe: Watchlist

Related: FR-WL-1 to FR-WL-5. Requires login (Registered Investor).

```
┌───────────────────────────────────────────────────────────────┐
│ [Top Nav]                                                      │
├───────────────────────────────────────────────────────────────┤
│ My Watchlists: [Tech Picks*] [Dividend Plays] [+ New]           │
├───────────────────────────────────────────────────────────────┤
│ 🔍 [Add security to "Tech Picks"..............]  [Add]          │
├───────────────────────────────────────────────────────────────┤
│ Stock      Price     Change    Shariah   Note            [x]    │
│ ABC (1234) RM 3.45    ▲1.2%     ✅        "watching Q3"   🗑     │
│ DEF (5678) RM 1.10    ▼0.5%     ❌        —                🗑     │
│ ...                                                              │
├───────────────────────────────────────────────────────────────┤
│ [Delete this watchlist]                                         │
└───────────────────────────────────────────────────────────────┘
```

## Notes

- Watchlist tabs across the top; active one bold/underlined ("Tech Picks*" above).
- Each row is clickable through to Company Profile; 🗑 removes the item (FR-WL-2); note
  field is inline-editable (FR-WL-4).
- Shariah column shows the same ✅/❌ badge used on Company Profile, per FR-WL-3/FR-SHR-6.
- "Delete this watchlist" requires a confirmation dialog (FR-WL-5).
