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
        Schema::create('chptertxns', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('donation')->nullable()->constrained('donations')->onDelete('cascade');
            // $table->foreignId('billing_id')->onDelete('restrict')->constrained('billings');
            $table->foreignId('billing_id')->nullable()->default(null)->constrained('billings')->onDelete('restrict');
            $table->string('reference', 255)->nullable();
            $table->string('type', 20)->nullable()->default('cashout')->comment('cashin, cashout');

            //method
            $table->string('payment_method', 255)->nullable();
            $table->string('payment_message', 255)->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable()->default(0.0);
            $table->decimal('billing_amount', 10, 2)->nullable()->default(0.0);

            // status transaction  paid
            $table->string('payment_status', 255)->nullable();
            $table->string('transaction', 255)->nullable();
            $table->integer('paid')->default(0);
            $table->timestamp('paid_at')->nullable();

            $table->integer('flag')->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chptertxns');
    }
};
