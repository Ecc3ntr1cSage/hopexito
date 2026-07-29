<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_products', function (Blueprint $table) {
            $table->string('custom_image_back')->nullable()->change();
            $table->string('custom_product_image_2')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('custom_products', function (Blueprint $table) {
            $table->string('custom_image_back')->nullable(false)->change();
            $table->string('custom_product_image_2')->nullable(false)->change();
        });
    }
};
