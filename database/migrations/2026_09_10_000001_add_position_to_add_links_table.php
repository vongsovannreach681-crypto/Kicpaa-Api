<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('add_links', function (Blueprint $table): void {
            $table->unsignedInteger('position')->nullable()->after('image');
        });

        $links = DB::table('add_links')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->pluck('id');

        foreach ($links as $position => $id) {
            DB::table('add_links')
                ->where('id', $id)
                ->update(['position' => $position]);
        }
    }

    public function down(): void
    {
        Schema::table('add_links', function (Blueprint $table): void {
            $table->dropColumn('position');
        });
    }
};
