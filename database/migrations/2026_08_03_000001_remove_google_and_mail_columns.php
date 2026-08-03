<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn(['google_id', 'email_verified_at']);
            });
        }

        Schema::dropIfExists('password_resets');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->timestamp('email_verified_at')->nullable();
            $table->string('google_id')->nullable();
        });

        Schema::create('password_resets', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }
};
