<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CloseWeeklyChallenge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-weekly-challenge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Closes finished weekly challenges and selects winners.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $challenges = \App\Models\WeeklyChallenge::where('status', 'active')
            ->where('end_at', '<=', now())
            ->get();

        if ($challenges->isEmpty()) {
            $this->info("No active challenges found to close.");
            return;
        }

        foreach ($challenges as $challenge) {
            $this->info("Processing closure for challenge: {$challenge->challenge_id}");
            $challenge->closeAndPickWinner();
            $this->info("Challenge {$challenge->challenge_id} closed.");
        }

        $this->info("Done.");
    }

    private function success($message)
    {
        $this->info("[SUCCESS] $message");
    }
}
