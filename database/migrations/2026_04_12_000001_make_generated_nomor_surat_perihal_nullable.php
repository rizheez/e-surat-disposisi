<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generated_nomor_surats', function (Blueprint $table): void {
            $table->text('perihal')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('generated_nomor_surats', function (Blueprint $table): void {
            $table->text('perihal')->nullable(false)->change();
        });
    }
};
