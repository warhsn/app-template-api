<?php

use App\Models\Radio;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->foreignIdFor(Radio::class)->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->dropForeignIdFor(Radio::class);
            $table->dropColumn('radio_id');
        });
    }
};
