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
        Schema::create('referral', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_by')->nullable()->constrained('users')->onDelete('cascade')->default(null);
            $table->foreignId('billing')->nullable()->default(null)->constrained('billings')->onDelete('cascade');
            $table->integer('status')->default(0);
            $table->integer('flag')->default(1);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral');
    }
};
