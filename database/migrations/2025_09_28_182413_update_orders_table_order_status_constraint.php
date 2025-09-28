<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Update any remaining invalid order_status values to COMPLETE (1)
            DB::statement('UPDATE orders SET order_status = 1 WHERE order_status NOT IN (1)');

            // Add constraint to ensure only valid enum values (only COMPLETE = 1 now)
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_status CHECK (order_status = 1)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove the constraint
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS chk_order_status');
        });
    }
};
