<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('billing_address_property_id')->nullable();
            $table->foreign('billing_address_property_id')->references('id')->on('properties');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['billing_address_property_id']);
            $table->dropColumn(['billing_address_property_id']);
        });
    }
};
