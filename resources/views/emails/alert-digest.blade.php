<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; color: #1e293b; margin: 0; padding: 0; background: #f8fafc; }
        .container { max-width: 560px; margin: 0 auto; padding: 24px; }
        .header { font-size: 18px; font-weight: 600; margin-bottom: 4px; }
        .subheader { color: #64748b; font-size: 13px; margin-bottom: 20px; }
        .alert-item { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; margin-bottom: 8px; font-size: 14px; }
        .footer { color: #94a3b8; font-size: 12px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Watchlist Alerts</div>
        <div class="subheader">
            Hi {{ $user->name }}, {{ $triggers->count() }}
            {{ Str::plural('alert', $triggers->count()) }} triggered on your watchlists as of
            {{ $triggers->first()->trigger_date->toFormattedDateString() }}.
        </div>

        @foreach ($triggers as $trigger)
            <div class="alert-item">{{ $trigger->message }}</div>
        @endforeach

        <div class="footer">
            This is an automated daily digest based on end-of-day data — see your watchlist
            for full detail and to manage alert rules. Share Monitoring System is an
            education and research tool; nothing here is investment advice.
        </div>
    </div>
</body>
</html>
