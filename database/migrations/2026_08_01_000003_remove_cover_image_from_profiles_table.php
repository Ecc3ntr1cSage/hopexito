<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['profiles', 'artists'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'cover_image')) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropColumn('cover_image'));
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('profiles') && ! Schema::hasColumn('profiles', 'cover_image')) {
            Schema::table('profiles', fn (Blueprint $table) => $table->string('cover_image')->nullable());
        }

        if (Schema::hasTable('artists') && ! Schema::hasColumn('artists', 'cover_image')) {
            Schema::table('artists', fn (Blueprint $table) => $table->string('cover_image')->nullable());
        }
    }
};
