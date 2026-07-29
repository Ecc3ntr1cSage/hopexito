<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id');
            $table->string('shopname');
            $table->string('collection_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('tags');
            $table->double('price');
            $table->double('discount')->default(1);
            $table->double('commission');
            $table->string('color');
            $table->string('category');
            $table->string('image_front');
            $table->string('image_front_path')->nullable();
            $table->string('image_back')->nullable();
            $table->string('image_back_path')->nullable();
            $table->integer('status')->default(1);
            $table->integer('sold')->default(0);
            $table->text('product_image')->nullable();
            $table->string('product_image_path')->nullable();
            $table->text('product_image_2')->nullable();
            $table->string('product_image_2_path')->nullable();
            $table->boolean('preview')->default(false);
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
        Schema::dropIfExists('products');
    }
    
};
