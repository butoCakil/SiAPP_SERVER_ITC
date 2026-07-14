<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('statusnya', function (Blueprint $table) {
            $table->boolean('firmware_auto_cleanup')->default(1)
                ->after('upload_interval')
                ->comment('1 = otomatis hapus firmware lama (keep 5 terbaru), 0 = kontrol manual penuh');
        });
    }

    public function down(): void
    {
        Schema::table('statusnya', function (Blueprint $table) {
            $table->dropColumn('firmware_auto_cleanup');
        });
    }
};
