<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('statusnya', function (Blueprint $table) {
            $table->unsignedSmallInteger('upload_interval')->default(300)
                  ->after('push_interval')
                  ->comment('Interval background upload device (detik), min 15');
        });
    }

    public function down(): void
    {
        Schema::table('statusnya', function (Blueprint $table) {
            $table->dropColumn('upload_interval');
        });
    }
};
