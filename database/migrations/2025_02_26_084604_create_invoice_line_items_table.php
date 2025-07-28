<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_line_items', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('vat');
            $table->unsignedInteger('discount')->nullable()->default(0);
            $table->unsignedInteger('total');
            $table->foreignId('invoice_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('price_id')->constrained()->nullable();
            $table->string('invoiceable_id');
            $table->unsignedBigInteger('invoiceable_type');
            $table->timestamps();

            $table->index([
                'invoiceable_id',
                'invoiceable_type',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_line_items');
    }
};
