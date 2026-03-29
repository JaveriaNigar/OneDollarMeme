<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CloseExpiredChallenges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-expired-challenges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired challenges...');
        \App\Models\WeeklyChallenge::current(); // This triggers the auto-close logic internally
        $this->info('Done.');
    }
}
