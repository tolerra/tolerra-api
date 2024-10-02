<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disability_verifications', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->boolean('is_verified')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('disability_verifications', function (Blueprint $table) {
            $table->dropColumn('file_path');
            $table->dropColumn('is_verified');
        });
    }
};