<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('status');
            $table->integer('qr_position_x')->nullable()->after('qr_token');
            $table->integer('qr_position_y')->nullable()->after('qr_position_x');
            $table->timestamp('approved_at')->nullable()->after('qr_position_y');
        });
    }

    public function down(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_position_x', 'qr_position_y', 'approved_at']);
        });
    }
};
