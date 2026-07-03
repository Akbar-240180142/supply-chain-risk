<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PositiveWord;
use App\Models\NegativeWord;

class DictionarySeeder extends Seeder
{
    public function run()
    {
        $positiveWords = [
            'growth', 'increase', 'profit', 'stable', 'improve', 'improvement',
            'success', 'successful', 'gain', 'positive', 'opportunity', 'opportunities',
            'advance', 'advanced', 'progress', 'recover', 'recovery', 'boom',
            'strong', 'stronger', 'rise', 'rising', 'expansion', 'expand',
            'benefit', 'beneficial', 'efficient', 'efficiency', 'optimize', 'optimized',
            'partnership', 'agreement', 'deal', 'investment', 'invest', 'innovative'
        ];

        $negativeWords = [
            'war', 'crisis', 'inflation', 'delay', 'disaster', 'disasters',
            'decrease', 'decline', 'declining', 'fall', 'falling', 'drop',
            'loss', 'losing', 'negative', 'risk', 'risky', 'threat', 'threats',
            'conflict', 'tension', 'sanction', 'sanctions', 'recession', 'recessions',
            'collapse', 'collapsed', 'crash', 'crashed', 'failure', 'fail',
            'disrupt', 'disruption', 'disrupted', 'shortage', 'shortages',
            'unstable', 'instability', 'uncertainty', 'uncertain', 'volatile',
            'bankrupt', 'bankruptcy', 'default', 'debt', 'emergency'
        ];

        foreach ($positiveWords as $word) {
            PositiveWord::firstOrCreate(['word' => $word], ['weight' => 1]);
        }

        foreach ($negativeWords as $word) {
            NegativeWord::firstOrCreate(['word' => $word], ['weight' => 1]);
        }

        $this->command->info('✅ Dictionary seeded successfully!');
        $this->command->info('   Positive words: ' . count($positiveWords));
        $this->command->info('   Negative words: ' . count($negativeWords));
    }
}