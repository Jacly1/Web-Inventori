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
        Schema::table('masuk', function (Blueprint $table) {
            // Menambahkan kolom original_quantity dengan nilai default 0
            $table->integer('original_quantity')->default(0)->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('masuk', function (Blueprint $table) {
            $table->dropColumn('original_quantity');
        });
    }
};
