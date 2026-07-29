<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->double('price');
            $table->string('color');
            $table->string('size');
            $table->integer('quantity');
            $table->string('custom_image_front');
            $table->string('custom_image_back')->nullable();
            $table->string('custom_product_image');
            $table->string('custom_product_image_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_products');
    }
};
