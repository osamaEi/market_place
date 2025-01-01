<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('normal_ads', function (Blueprint $table) {
            
            $table->unsignedInteger('views_count')->default(0)->after('id'); // Add after id or any appropriate column

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('normal_ads', function (Blueprint $table) {
            //
        });
    }
};
