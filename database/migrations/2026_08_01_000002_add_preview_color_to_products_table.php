<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'preview_color')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('preview_color')->default('White')->after('preview');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'preview_color')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('preview_color');
            });
        }
    }
};
