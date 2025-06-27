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
            $table->decimal('import_price_at_sale', 10, 2)->nullable()->after('price');
            $table->string('batch_identifier_at_sale')->nullable()->after('import_price_at_sale');
            $table->foreignId('warehouse_id_at_sale')->nullable()->constrained('warehouses')->onDelete('set null')->after('batch_identifier_at_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('warehouse_id_at_sale');
            $table->dropColumn(['import_price_at_sale', 'batch_identifier_at_sale']);
        });
    }
};
