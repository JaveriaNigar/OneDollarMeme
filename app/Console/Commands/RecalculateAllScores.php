<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RecalculateAllScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all meme scores based on total interactions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $memes = \App\Models\Meme::where('is_contest', 1)->get();
        $this->info("Recalculating scores for " . $memes->count() . " contest memes...");

        $bar = $this->output->createProgressBar($memes->count());
        $bar->start();

        foreach ($memes as $meme) {
            $meme->recalculateScore();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("All scores synchronized successfully!");
    }
}
