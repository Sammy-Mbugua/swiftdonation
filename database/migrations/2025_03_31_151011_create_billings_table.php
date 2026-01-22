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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            // Billing Code
            $table->string('uid', 255)->nullable();

            // User
            $table->foreignId('user')->nullable()->constrained('users')->onDelete('set null')->default(null);

            // Billing Type
            $table->string('type', 50)->default('subscription');
            // Billing Type Id
            $table->integer('type_id')->nullable()->default(null); //Primary key of the table referred above ['type'] :

            // Billing
            $table->foreignId('currency')->nullable()->constrained('hierarchy')->onDelete('cascade')->default(null);
            $table->decimal('total', 10, 2)->nullable()->default(0.0)->comment('Total');

            // status transaction  paid
            $table->integer('status')->default(0); // 0: pending, 1: paid, 2: failed

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
        Schema::dropIfExists('billings');
    }
};
