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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_one');
            $table->unsignedBigInteger('customer_two');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
    
            $table->foreign('customer_one')->references('id')->on('customers');
            $table->foreign('customer_two')->references('id')->on('customers');
            
            $table->unique(['customer_one', 'customer_two']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
