<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('add_themes')->updateOrInsert(
            ['themeName' => 'Kbach Khmer'],
            [
                'themeImage' => 'themes/kbach-khmer.svg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        DB::table('add_themes')->where('themeName', 'Kbach Khmer')->delete();
    }
};
