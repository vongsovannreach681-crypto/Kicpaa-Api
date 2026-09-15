<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('my_settings', function (Blueprint $table) {
            $table->string('short_title')->nullable()->change();
            $table->string('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('my_settings', function (Blueprint $table) {
            $table->string('short_title')->nullable(false)->change();
            $table->string('image')->nullable(false)->change();
        });
    }
};
