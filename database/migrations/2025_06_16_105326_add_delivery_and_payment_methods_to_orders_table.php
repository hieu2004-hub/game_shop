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
        Schema::table('orders', function (Blueprint $table) {
            // Thêm cột phương thức nhận hàng, mặc định là 'Giao tận nơi'
            $table->string('delivery_method')->default('Giao tận nơi')->after('address');
            // Thêm cột phương thức thanh toán, mặc định là 'Tiền mặt'
            $table->string('payment_method')->default('Tiền mặt')->after('delivery_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Xóa cột phương thức nhận hàng
            $table->dropColumn('delivery_method');
            // Xóa cột phương thức thanh toán
            $table->dropColumn('payment_method');
        });
    }
};
