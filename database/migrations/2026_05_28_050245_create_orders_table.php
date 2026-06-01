 <?php
// database/migrations/2024_01_01_000010_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('delivery_person_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('order_number')->unique();
            $table->enum('type', ['dine_in', 'takeaway', 'delivery']);
            $table->enum('status', [
                'pending', 'confirmed', 'preparing', 'ready', 
                'in_delivery', 'delivered', 'completed', 'cancelled'
            ])->default('pending');
            
            $table->string('table_number')->nullable(); // Pour dine_in
            
            // Informations de livraison
            $table->string('delivery_address')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_postal_code')->nullable();
            $table->decimal('delivery_latitude', 10, 8)->nullable();
            $table->decimal('delivery_longitude', 11, 8)->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->integer('estimated_delivery_time')->nullable();
            $table->dateTime('delivered_at')->nullable();
            
            // Finances
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tip_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Paiement
            $table->enum('payment_method', ['cash', 'card', 'online', 'wallet'])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->string('payment_id')->nullable(); // ID transaction Stripe/PayPal
            $table->string('payment_gateway')->nullable();
            $table->json('payment_details')->nullable();
            $table->dateTime('paid_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('kitchen_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            
            // Source de la commande
            $table->enum('source', ['web', 'mobile', 'pos', 'phone'])->default('web');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['restaurant_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index('order_number');
            $table->index('created_at');
        });
         // Articles de la commande
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('product_name'); // Copie au cas où le produit change
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->json('product_data')->nullable(); // Snapshot du produit
            
            $table->timestamps();
            
            $table->index(['order_id', 'product_id']);
        });

        // Options choisies pour chaque article
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->string('option_name');
            $table->string('item_name');
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // Historique des statuts de commande
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status');
            $table->text('comment')->nullable();
            $table->timestamps();
            
            $table->index(['order_id', 'created_at']);
        });

        // Suivi de livraison
        Schema::create('delivery_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('speed', 8, 2)->nullable();
            $table->string('status')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            $table->index(['order_id', 'recorded_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_tracking');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_item_options');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};