<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->decimal('keuntungan', 12, 2)->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->dropColumn('keuntungan');
        });
    }
};