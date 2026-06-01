<?php
// database/migrations/2024_01_01_000020_create_reviews_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->integer('rating'); // 1-5
            $table->integer('food_rating')->nullable(); // Note nourriture
            $table->integer('delivery_rating')->nullable(); // Note livraison
            $table->integer('service_rating')->nullable(); // Note service
            $table->text('comment')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->integer('unhelpful_count')->default(0);
            $table->timestamps();
            
            $table->index(['restaurant_id', 'is_approved', 'created_at']);
            $table->index(['product_id', 'is_approved']);
        });

        // Pour le vote "utile" sur les avis
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['helpful', 'unhelpful']);
            $table->timestamps();
            
            $table->unique(['review_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_votes');
        Schema::dropIfExists('reviews');
    }
};
