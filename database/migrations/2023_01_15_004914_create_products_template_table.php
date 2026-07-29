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
        Schema::create('products_template', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->double('commission');
            $table->string('mockup_image');
            $table->string('mockup_image_2')->nullable();
            $table->integer('status');
            $table->double('min');
            $table->string('color');
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
        Schema::dropIfExists('products_template');
    }
};
