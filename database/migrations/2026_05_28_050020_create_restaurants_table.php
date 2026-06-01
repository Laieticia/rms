<?php
// database/migrations/2024_01_01_000000_create_restaurants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('country')->default('FR');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone');
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('opening_hours')->nullable();
            $table->json('special_hours')->nullable();
            $table->decimal('minimum_order', 8, 2)->default(0);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(20.00);
            $table->string('currency', 3)->default('EUR');
            $table->integer('estimated_delivery_time')->default(30); // en minutes
            $table->boolean('is_active')->default(true);
            $table->boolean('accepts_delivery')->default(true);
            $table->boolean('accepts_takeaway')->default(true);
            $table->boolean('accepts_dine_in')->default(true);
            $table->text('delivery_terms')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot pour les administrateurs de restaurant
        Schema::create('restaurant_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('staff'); // admin, manager, staff
            $table->json('permissions')->nullable();
            $table->timestamps();
            
            $table->unique(['restaurant_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('restaurant_user');
        Schema::dropIfExists('restaurants');
    }
};
