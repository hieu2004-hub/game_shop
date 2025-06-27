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
        Schema::create('product_warehouse_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->integer('quantity')->default(0); // Số lượng sản phẩm trong lô này
            $table->decimal('import_price', 10, 2); // Giá nhập của lô hàng này (quan trọng cho lợi nhuận)
            $table->string('batch_identifier')->nullable(); // Mã định danh lô hàng (ví dụ: PO-2023-001, Batch-A)
            $table->date('received_date')->nullable(); // Ngày nhập lô hàng
            $table->text('notes')->nullable(); // Ghi chú về lô hàng (ví dụ: lô hàng bị lỗi)
            $table->timestamps();

            // Đảm bảo mỗi lô hàng của một sản phẩm tại một kho là duy nhất
            $table->unique(['product_id', 'warehouse_id', 'batch_identifier'], 'product_warehouse_batch_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse_stock');
    }
};
