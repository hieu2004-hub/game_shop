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
        // database/migrations/YYYY_MM_DD_create_warranty_claims_table.php
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_instance_id')->constrained('product_instances')->onDelete('cascade');
            $table->foreignId('claimed_by_user_id')->constrained('users')->onDelete('cascade'); // Người yêu cầu bảo hành
            $table->string('claim_type')->nullable(); // Ví dụ: 'repair', 'replacement', 'refund'
            $table->text('issue_description'); // Mô tả vấn đề
            $table->string('status')->default('pending'); // pending, approved, rejected, in_progress, resolved
            $table->text('resolution_details')->nullable(); // Chi tiết giải quyết vấn đề
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Người xử lý yêu cầu
            $table->timestamp('resolved_at')->nullable(); // Thời điểm yêu cầu được giải quyết
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
    }
};
