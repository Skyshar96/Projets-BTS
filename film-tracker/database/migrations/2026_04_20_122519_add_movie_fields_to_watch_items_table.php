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
        Schema::table('watch_items', function (Blueprint $table) {
            $table->string('imdb_id')->unique()->after('id');
            $table->string('title')->after('imdb_id');
            $table->string('poster')->nullable()->after('title');
            $table->string('year', 20)->nullable()->after('poster');
            $table->string('status', 20)->default('a_voir')->after('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('watch_items', function (Blueprint $table) {
            $table->dropUnique(['imdb_id']);
            $table->dropColumn(['imdb_id', 'title', 'poster', 'year', 'status']);
        });
    }
};
