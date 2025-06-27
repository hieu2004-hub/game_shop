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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['importPrice', 'availableQuantity', 'stockQuantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Nếu bạn muốn rollback, hãy thêm lại các cột này với kiểu dữ liệu ban đầu
            $table->decimal('importPrice', 10, 2)->nullable();
            $table->integer('availableQuantity')->nullable();
            $table->integer('stockQuantity')->nullable();
        });
    }
};
