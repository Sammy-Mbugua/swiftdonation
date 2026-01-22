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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('name', 30)->nullable()->comment('Package Short Name');
            $table->string('code', 10)->nullable()->default(null)->unique();

            $table->decimal('price', 20, 2)->nullable()->default(10);
            $table->decimal('discount', 20, 2)->nullable()->default(0);
            $table->foreignId('currency')->nullable()->constrained('hierarchy')->onDelete('cascade')->default(null);

            $table->integer('duration')->nullable()->default(7)->comment('In Days');

            // Post-grace period
            $table->integer('post_grace')->nullable()->default(0)->comment('No of days before deleting the package if not renewed');

            $table->string('description', 500)->nullable();
            $table->string('thumbnail', 800)->nullable();

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
        Schema::dropIfExists('packages');
    }
};
