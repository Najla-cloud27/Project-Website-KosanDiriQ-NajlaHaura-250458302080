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
        // Migration ini dibuat untuk update status complaints
        // Tetapi kita skip karena sudah menggunakan bahasa Indonesia dari awal
        // Tidak ada perubahan yang diperlukan
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada perubahan untuk di-rollback
    }
};
