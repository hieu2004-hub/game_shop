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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Tên kho (ví dụ: Kho chính, Kho Hà Nội)
            $table->string('address')->nullable(); // Địa chỉ kho
            $table->string('contact_person')->nullable(); // Người liên hệ
            $table->string('phone')->nullable(); // Số điện thoại liên hệ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
