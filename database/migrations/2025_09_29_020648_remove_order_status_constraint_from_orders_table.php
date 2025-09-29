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
            // Remove the restrictive order_status constraint that only allows value 1
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS chk_order_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Re-add the constraint allowing both PENDING (0) and COMPLETE (1)
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_status CHECK (order_status IN (0, 1))');
        });
    }
};
