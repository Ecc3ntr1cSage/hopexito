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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('collection_id');
            $table->string('email');
            $table->string('name');
            $table->string('description');
            $table->double('delivery');
            $table->integer('status');
            $table->double('amount');
            $table->string('tracking_number')->nullable();
            $table->string('paid');
            $table->string('paid_at');
            $table->string('address');
            $table->string('postcode');
            $table->string('state');
            $table->string('phone')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
