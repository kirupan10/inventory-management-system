<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Change integer columns to bigInteger to handle larger monetary values
            $table->bigInteger('quantity')->change();
            $table->bigInteger('unitcost')->change();
            $table->bigInteger('total')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Revert back to integer columns
            $table->integer('quantity')->change();
            $table->integer('unitcost')->change();
            $table->integer('total')->change();
        });
    }
};
