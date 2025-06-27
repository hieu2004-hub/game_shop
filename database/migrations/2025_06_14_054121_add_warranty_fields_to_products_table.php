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
            // Cột để xác định sản phẩm có được bảo hành hay không
            // Mặc định là true (có bảo hành)
            $table->boolean('is_warrantable')->default(true)->after('productBrand');

            // Cột để lưu trữ thời gian bảo hành mặc định cho sản phẩm (tính bằng tháng)
            // Cho phép null nếu sản phẩm không có thời gian bảo hành mặc định hoặc chưa xác định
            $table->integer('default_warranty_months')->nullable()->after('is_warrantable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Khi rollback migration, xóa các cột đã thêm
            $table->dropColumn('default_warranty_months');
            $table->dropColumn('is_warrantable');
        });
    }
};
