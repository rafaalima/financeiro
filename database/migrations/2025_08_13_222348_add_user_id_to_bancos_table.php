<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void {
        Schema::table('bancos', function (Blueprint $table) {
            if (!Schema::hasColumn('bancos', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }
        });
    }
    public function down(): void {
        Schema::table('bancos', function (Blueprint $table) {
            if (Schema::hasColumn('bancos', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
