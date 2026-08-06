<?php

namespace App\Console\Commands;

use App\Mail\AlertDigestMail;
use App\Services\AlertEvaluationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EvaluateAlerts extends Command
{
    protected $signature = 'alerts:evaluate';

    protected $description = 'Evaluate active watchlist alert rules against latest data and email digests for new triggers';

    public function handle(AlertEvaluationService $evaluator): int
    {
        $newTriggers = $evaluator->evaluateAll();

        $this->info("Evaluated rules — {$newTriggers->count()} new trigger(s).");

        $byUser = $newTriggers->groupBy(fn ($trigger) => $trigger->alertRule->watchlistItem->watchlist->user_id);

        foreach ($byUser as $userId => $triggers) {
            $user = $triggers->first()->alertRule->watchlistItem->watchlist->user;

            Mail::to($user->email)->send(new AlertDigestMail($user, $triggers));

            $triggers->each(fn ($t) => $t->update(['notified_at' => now()]));

            $this->info("Digest queued for {$user->email} ({$triggers->count()} alert(s)).");
        }

        return self::SUCCESS;
    }
}
