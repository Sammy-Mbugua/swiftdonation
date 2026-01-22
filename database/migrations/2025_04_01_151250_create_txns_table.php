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
        Schema::create('txns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing')->nullable()->default(null)->constrained('billings')->onDelete('cascade');
            // Billing Reference
            $table->string('gateway', 255)->nullable()->default(null);

            // Payment
            $table->decimal('cr', 20, 2)->nullable()->default(0.00);
            $table->decimal('dr', 20, 2)->nullable()->default(0.00);
            $table->string('type')->nullable()->default('purchase');
            $table->integer('reverse')->nullable()->default(0)->comment('1: reverse');

            // ? info
            $table->string('ref', 500)->nullable()->default(null)->comment('reference');
            $table->string('note', 1000)->nullable()->default(null)->comment('note');

            $table->integer('flag')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('txns');
    }
};
