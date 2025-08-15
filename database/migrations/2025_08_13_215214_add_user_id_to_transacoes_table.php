<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('transacoes', function (Blueprint $table) {
            if (!Schema::hasColumn('transacoes', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }
        });
    }

    public function down(): void {
        Schema::table('transacoes', function (Blueprint $table) {
            if (Schema::hasColumn('transacoes', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
