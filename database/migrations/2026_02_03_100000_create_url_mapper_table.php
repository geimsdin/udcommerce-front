<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('url_mapper', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('friendly_url');
            $table->string('controller');
            $table->timestamps();

            $table->unique(['language_id', 'friendly_url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('url_mapper');
    }
};
