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
        Schema::table('products', function (Blueprint $table) {
            // Change integer columns to bigInteger to handle larger values
            $table->bigInteger('quantity')->change();
            $table->bigInteger('buying_price')->change();
            $table->bigInteger('selling_price')->change();
            $table->bigInteger('quantity_alert')->change();
            $table->bigInteger('tax')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert back to integer columns
            $table->integer('quantity')->change();
            $table->integer('buying_price')->change();
            $table->integer('selling_price')->change();
            $table->integer('quantity_alert')->change();
            $table->integer('tax')->nullable()->change();
        });
    }
};
