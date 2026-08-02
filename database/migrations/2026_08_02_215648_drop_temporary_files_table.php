<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('temporary_files');
    }

    public function down(): void
    {
        // The temporary upload endpoint and model were removed from the active application.
    }
};
