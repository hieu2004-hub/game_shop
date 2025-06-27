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
            $table->string('momo_order_id')->nullable()->unique()->after('id');
            $table->string('momo_trans_id')->nullable()->after('payment_method'); // Thêm luôn cột này để lưu mã giao dịch Momo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['momo_order_id', 'momo_trans_id']);
        });
    }
};
