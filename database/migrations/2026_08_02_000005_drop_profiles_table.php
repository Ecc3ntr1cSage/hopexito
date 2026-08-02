<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('profiles');
    }

    public function down(): void
    {
        // Profile data is intentionally no longer part of the application schema.
    }
};
