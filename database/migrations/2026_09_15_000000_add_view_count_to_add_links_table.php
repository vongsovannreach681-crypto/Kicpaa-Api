<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('add_links', function (Blueprint $table): void {
            $table->unsignedBigInteger('view_count')->default(0)->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('add_links', function (Blueprint $table): void {
            $table->dropColumn('view_count');
        });
    }
};
