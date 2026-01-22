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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('package')->constrained('packages')->onDelete('cascade');
            $table->foreignId('user')->constrained('users')->onDelete('cascade');
            $table->foreignId('billing')->constrained('billings')->onDelete('cascade');

            // Subscription
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            // Post-grace period
            $table->timestamp('post_grace_at')->nullable();

            // Billing
            $table->decimal('price', 20, 2)->nullable()->default(10);
            $table->foreignId('currency')->nullable()->constrained('hierarchy')->onDelete('cascade')->default(null);

            $table->string('description', 500)->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
};
