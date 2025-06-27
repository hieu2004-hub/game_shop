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
        // database/migrations/YYYY_MM_DD_create_product_instances_table.php
        Schema::create('product_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Sản phẩm gốc
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->onDelete('set null'); // Liên kết với OrderItem đã bán
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Người mua/sở hữu hiện tại
            $table->string('serial_number')->unique(); // Số serial duy nhất của sản phẩm
            $table->date('purchase_date'); // Ngày sản phẩm được bán ra
            $table->integer('warranty_duration_months')->default(12); // Thời gian bảo hành (ví dụ: 12 tháng)
            $table->date('warranty_start_date')->nullable(); // Ngày bắt đầu bảo hành (thường là purchase_date)
            $table->date('warranty_end_date')->nullable(); // Ngày hết hạn bảo hành
            $table->string('warranty_status')->default('active'); // active, expired, claimed, etc.
            $table->text('notes')->nullable(); // Ghi chú thêm về sản phẩm này
            $table->string('proof_of_purchase_path')->nullable(); // Đường dẫn đến ảnh/file hóa đơn
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_instances');
    }
};
