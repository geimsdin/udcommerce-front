<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_list', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Display name (e.g., "Product", "Category")
            $table->string('fqcn'); // Fully Qualified Class Name
            $table->string('type')->default('front_controller'); // front_controller, payment_gateway, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('fqcn');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_list');
    }
};
