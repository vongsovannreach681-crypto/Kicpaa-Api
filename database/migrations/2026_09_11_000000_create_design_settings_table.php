<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_settings', function (Blueprint $table) {
            $table->id();
            $table->string('theme', 80)->default('midnight');
            $table->string('background', 80)->default('deep-space');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_settings');
    }
};
