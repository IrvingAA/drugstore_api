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
            $table->text('description')->nullable();
            $table->string('batch');
            $table->date('expiration');
            $table->decimal('purchase_price',8,2);
            $table->decimal('sale_price',8,2);
            $table->boolean('discount')->default(false);
            $table->decimal('gain',8,2)->nullable();
            $table->integer('stock')->nullable();
            $table->integer('min_stock')->nullable();
            $table->integer('ieps')->nullable();
            $table->integer('iva')->nullable();
            $table->string('compound')->nullable();
            $table->string('pharmaceutical_form')->nullable();
            $table->string('concentration')->nullable();
            $table->string('fraction')->nullable();
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
