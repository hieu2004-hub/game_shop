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
        Schema::table('order_items', function (Blueprint $table) {
            // Thêm cột 'quantity' kiểu integer, không được null và có giá trị mặc định là 1 (hoặc 0 tùy logic của bạn)
            $table->integer('quantity')->after('productID')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Khi rollback migration, xóa cột 'quantity'
            $table->dropColumn('quantity');
        });
    }
};
