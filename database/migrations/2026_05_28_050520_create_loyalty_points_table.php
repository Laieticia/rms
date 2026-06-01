<?php
// database/migrations/2024_01_01_000021_create_loyalty_points_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('points');
            $table->enum('type', ['earned', 'spent', 'expired', 'adjusted']);
            $table->string('description')->nullable();
            $table->integer('balance_before')->default(0);
            $table->integer('balance_after')->default(0);
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points_cost');
            $table->enum('reward_type', ['discount', 'free_product', 'free_delivery']);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->foreignId('free_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->integer('stock')->nullable(); // Stock limité
            $table->integer('redeemed_count')->default(0);
            $table->timestamps();
        });

        Schema::create('loyalty_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_id')->constrained('loyalty_rewards')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('points_spent');
            $table->string('code')->unique();
            $table->enum('status', ['active', 'used', 'expired', 'cancelled']);
            $table->dateTime('expires_at');
            $table->dateTime('redeemed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loyalty_redemptions');
        Schema::dropIfExists('loyalty_rewards');
        Schema::dropIfExists('loyalty_points');
    }
};