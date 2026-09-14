<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('design_settings', function (Blueprint $table) {
            $table->string('text_color', 7)->default('#ffffff');
            $table->string('box_color', 7)->default('#64748b');
        });
    }

    public function down(): void
    {
        Schema::table('design_settings', function (Blueprint $table) {
            $table->dropColumn(['text_color', 'box_color']);
        });
    }
};
