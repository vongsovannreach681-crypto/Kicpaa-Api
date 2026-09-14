<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_options', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('slug', 80);
            $table->string('name', 100);
            $table->string('swatch', 80);
            $table->timestamps();
            $table->unique(['type', 'slug']);
        });

        $now = now();
        $options = [];

        foreach ([
            ['midnight', 'Midnight'], ['carbon', 'Carbon'], ['christmas', 'Christmas'],
            ['pride', 'Pride'], ['glitch', 'Glitch'], ['winter', 'Winter'],
            ['blush', 'Blush'], ['forest', 'Forest'], ['aurora', 'Aurora'],
            ['ocean', 'Ocean'], ['sunset', 'Sunset'], ['candy', 'Candy'],
            ['paper', 'Paper'], ['cobalt', 'Cobalt'], ['neon', 'Neon'],
        ] as [$slug, $name]) {
            $options[] = compact('slug', 'name') + ['type' => 'theme', 'swatch' => $slug, 'created_at' => $now, 'updated_at' => $now];
        }

        foreach ([
            ['deep-space', 'Deep Space'], ['soft-mesh', 'Soft Mesh'],
            ['sunrise', 'Sunrise'], ['blueprint', 'Blueprint'],
            ['tropical', 'Tropical'], ['pearl', 'Pearl'],
            ['violet-haze', 'Violet Haze'], ['citrus-wave', 'Citrus Wave'],
            ['paper-grid', 'Paper Grid'], ['night-grid', 'Night Grid'],
        ] as [$slug, $name]) {
            $options[] = compact('slug', 'name') + ['type' => 'background', 'swatch' => $slug, 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('design_options')->insert($options);
    }

    public function down(): void
    {
        Schema::dropIfExists('design_options');
    }
};
