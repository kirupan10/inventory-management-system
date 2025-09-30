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
        Schema::table('orders', function (Blueprint $table) {
            // Change integer columns to bigInteger to handle larger monetary values
            $table->bigInteger('total_products')->change();
            $table->bigInteger('sub_total')->change();
            $table->bigInteger('discount_amount')->change();
            $table->bigInteger('service_charges')->change();
            $table->bigInteger('vat')->change();
            $table->bigInteger('total')->change();
            $table->bigInteger('pay')->change();
            $table->bigInteger('due')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert back to integer columns
            $table->integer('total_products')->change();
            $table->integer('sub_total')->change();
            $table->integer('discount_amount')->change();
            $table->integer('service_charges')->change();
            $table->integer('vat')->change();
            $table->integer('total')->change();
            $table->integer('pay')->change();
            $table->integer('due')->change();
        });
    }
};
