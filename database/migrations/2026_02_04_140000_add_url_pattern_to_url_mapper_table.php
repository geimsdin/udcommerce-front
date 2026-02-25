<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('url_mapper', function (Blueprint $table) {
            $table->string('url_pattern')->nullable()->after('controller');
        });
    }

    public function down(): void
    {
        Schema::table('url_mapper', function (Blueprint $table) {
            $table->dropColumn('url_pattern');
        });
    }
};
