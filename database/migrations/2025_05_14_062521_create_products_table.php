<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('productName');
            $table->string('productImage')->nullable();
            $table->string('productBrand')->nullable();
            $table->string('productCategory')->nullable();
            $table->text('productDescription')->nullable();
            $table->decimal('productPrice', 10, 2);
            $table->decimal('importPrice', 10, 2)->nullable();
            $table->integer('availableQuantity')->nullable();
            $table->integer('stockQuantity')->nullable();
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
