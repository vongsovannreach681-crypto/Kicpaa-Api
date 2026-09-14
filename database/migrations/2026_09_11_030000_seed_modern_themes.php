<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $themes = [
            ['themeName' => 'Aurora Glass', 'themeImage' => 'themes/modern-aurora.svg'],
            ['themeName' => 'Coral Bloom', 'themeImage' => 'themes/modern-coral.svg'],
            ['themeName' => 'Cobalt Grid', 'themeImage' => 'themes/modern-cobalt.svg'],
            ['themeName' => 'Emerald Flow', 'themeImage' => 'themes/modern-emerald.svg'],
            ['themeName' => 'Mono Studio', 'themeImage' => 'themes/modern-mono.svg'],
        ];

        foreach ($themes as $theme) {
            DB::table('add_themes')->updateOrInsert(
                ['themeName' => $theme['themeName']],
                [
                    'themeImage' => $theme['themeImage'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('add_themes')
            ->whereIn('themeName', [
                'Aurora Glass',
                'Coral Bloom',
                'Cobalt Grid',
                'Emerald Flow',
                'Mono Studio',
            ])
            ->delete();
    }
};
