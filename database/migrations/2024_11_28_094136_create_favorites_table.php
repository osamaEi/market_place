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
        Schema::create('favorites', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('customer_id');
        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        
        $table->unsignedBigInteger('ad_id');
        $table->enum('ad_type', ['normal', 'commercial']);
        
        $table->timestamps();
        
        // Add a composite unique index to prevent duplicate favorites
        $table->unique(['customer_id', 'ad_id', 'ad_type']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
