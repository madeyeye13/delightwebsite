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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('selling_method_id')->nullable()->constrained('selling_methods')->nullOnDelete();

            // Basic info
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable();
            $table->enum('collection', ['men', 'women', 'both'])->default('both');
            $table->text('description')->nullable();
            $table->text('description_html')->nullable();
            $table->json('tags')->nullable();

            // Unit configuration (driven by config_type of selling_method)
            $table->string('unit_label')->nullable();
            $table->unsignedSmallInteger('units_per_order')->default(1);
            $table->unsignedSmallInteger('min_quantity')->default(1);
            $table->unsignedSmallInteger('quantity_step')->default(1);
            $table->string('length_unit')->nullable();          // yards | meters
            $table->string('loom_size')->nullable();
            $table->json('set_contents')->nullable();
            $table->json('bundle_yield')->nullable();

            // Composition
            $table->json('included_items')->nullable();
            $table->text('excludes_text')->nullable();

            // Pricing (stored as integer NGN)
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('compare_price')->nullable();
            $table->enum('discount_type', ['percent', 'fixed'])->nullable();
            $table->unsignedInteger('discount_value')->nullable();
            $table->unsignedBigInteger('cost')->nullable();

            // Inventory
            $table->boolean('track_inventory')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->string('stock_unit')->nullable();
            $table->unsignedSmallInteger('low_stock_threshold')->default(5);

            // Add-ons display settings
            $table->boolean('show_add_ons_after_checkout')->default(false);
            $table->boolean('show_add_ons_in_cart')->default(false);
            $table->boolean('show_add_ons_on_page')->default(false);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Status & visibility
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->boolean('featured')->default(false);
            $table->boolean('is_new_arrival')->default(false);
            $table->date('new_arrival_expiry')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'featured']);
            $table->index('category_id');
            $table->index('selling_method_id');
        });

        // Self-referential pivot for add-ons (product recommends other products)
        Schema::create('product_add_ons', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('add_on_product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->primary(['product_id', 'add_on_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_add_ons');
        Schema::dropIfExists('products');
    }
};
