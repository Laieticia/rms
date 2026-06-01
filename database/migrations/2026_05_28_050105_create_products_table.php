<?php
// database/migrations/2024_01_01_000002_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable(); // Prix barré pour promo
            $table->decimal('cost_price', 10, 2)->nullable(); // Prix de revient
            $table->string('sku')->nullable(); // Code produit
            $table->string('barcode')->nullable();
            $table->integer('preparation_time')->default(15); // en minutes
            $table->integer('calories')->nullable();
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_gluten_free')->default(false);
            $table->boolean('is_spicy')->default(false);
            $table->json('allergens')->nullable(); // ['gluten', 'lactose', 'fruits_à_coque', etc.]
            $table->json('nutritional_info')->nullable(); // {'protein': '20g', 'carbs': '45g', etc.}
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->integer('stock_quantity')->nullable();
            $table->boolean('track_inventory')->default(false);
            $table->integer('low_stock_threshold')->nullable();
            $table->decimal('weight', 8, 2)->nullable(); // en grammes
            $table->string('unit')->nullable(); // pièce, kg, portion, etc.
            $table->integer('max_per_order')->nullable();
            $table->integer('min_per_order')->default(1);
            $table->json('tags')->nullable(); // ['nouveau', 'promo', 'best-seller']
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('orders_count')->default(0); // Compteur de commandes
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['restaurant_id', 'slug']);
            $table->index(['restaurant_id', 'category_id', 'is_available']);
            $table->index(['restaurant_id', 'is_featured']);
            $table->fullText(['name', 'description']);
        });

        // Table pour les images des produits
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // Table pour les variantes de produits (tailles, options)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // ex: "Petite", "Moyenne", "Grande"
            $table->string('sku')->nullable();
            $table->decimal('price_adjustment', 8, 2)->default(0); // Ajustement de prix
            $table->integer('stock_quantity')->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        // Table pour les suppléments/options
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['single', 'multiple'])->default('single');
            $table->boolean('is_required')->default(false);
            $table->integer('max_choices')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_option_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_option_items');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
