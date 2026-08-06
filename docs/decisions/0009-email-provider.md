# ADR-0009: Email Provider (Watchlist Alerts)

## Status

Accepted — needed for the Watchlist Alerts module.

## Context

Watchlist Alerts (FRS Module 8) needs to send digest emails. The user's other Laravel
projects on the same infrastructure (e.g. "Portal Staff Prokhas") already use Resend with
a verified `rcaquacycle.com` domain — reusing that avoids a second email vendor and a
second domain-verification process.

## Decision

**Resend**, via Laravel's built-in `resend` mail transport (the `resend/resend-php`
package) — not literal SMTP. Same pattern as the sibling project: `MAIL_MAILER=resend`,
`RESEND_API_KEY` in `.env`, standard Laravel `Mailable` classes queued via the existing
database queue connection ([ADR](../04-architecture/system-architecture.md) already has a
Redis-backed cache/queue in the target architecture; MVP/current deployment uses the
`database` queue driver, matching [`.env.production.example`](../../.env.production.example)).

Local dev keeps `MAIL_MAILER=log` (Laravel's default) — alert digest content can be
verified in `storage/logs/laravel.log` without sending real email or needing an API key.

## Consequences

- Production `.env` needs `RESEND_API_KEY` (from the Resend dashboard) and
  `MAIL_FROM_ADDRESS` set to an address on a domain already verified in Resend — the
  sibling project already verified `rcaquacycle.com`, so `alerts@rcaquacycle.com` works
  without additional DNS setup, per Resend's per-domain (not per-subdomain) verification.
- Alert digest emails run through the same queue worker Forge already manages for this
  site — no new infrastructure beyond the one `RESEND_API_KEY` env var.
- If a second/failover mail provider is ever needed, Laravel's `failover` mailer
  (already stubbed in `config/mail.php`) is the natural next step — not built now.

## Alternatives Considered

Not evaluated — reusing the already-verified provider from a sibling project on the same
infrastructure was the deciding factor, not a feature comparison.
