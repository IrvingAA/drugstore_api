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
            $table->string('barcode');
            $table->string('name');
            $table->foreignId('cat_brand_id')->constrained();
            $table->foreignId('cat_product_type_id')->constrained();
            $table->foreignId('tag_id')->constrained();
            $table->text('description');
            $table->string('brand');
            $table->string('batch');
            $table->date('expiration');
            $table->decimal('purchase_price',8,2);
            $table->decimal('sale_price',8,2);
            $table->integer('discount');
            $table->decimal('discount_price',8,2);
            $table->decimal('gain',8,2);
            $table->integer('stock');
            $table->integer('min_stock');
            $table->integer('ieps');
            $table->integer('iva');
            $table->string('compound');
            $table->string('pharmaceutical_form');
            $table->string('concentration');
            $table->string('fraction');
            $table->boolean('antibiotic')->default(false);
            $table->string('therapeutic_indication');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
