<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'image_front_path')) {
                $table->string('image_front_path')->nullable()->after('image_front');
            }

            if (! Schema::hasColumn('products', 'image_back_path')) {
                $table->string('image_back_path')->nullable()->after('image_back');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'image_front_path')) {
                $table->dropColumn('image_front_path');
            }

            if (Schema::hasColumn('products', 'image_back_path')) {
                $table->dropColumn('image_back_path');
            }
        });
    }
};
