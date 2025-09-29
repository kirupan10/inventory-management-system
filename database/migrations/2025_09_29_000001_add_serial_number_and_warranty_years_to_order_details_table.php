<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('serial_number')->nullable()->after('product_id');
            $table->unsignedTinyInteger('warranty_years')->nullable()->after('serial_number');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['serial_number', 'warranty_years']);
        });
    }
};


