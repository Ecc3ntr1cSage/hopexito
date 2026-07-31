<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'collection_id')) {
                    $table->dropColumn('collection_id');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'collection_id')) {
                    $table->dropColumn('collection_id');
                }
            });
        }

        Schema::dropIfExists('products_collection');
    }

    public function down(): void
    {
        // Collection support was intentionally retired and is not restored on rollback.
    }
};
