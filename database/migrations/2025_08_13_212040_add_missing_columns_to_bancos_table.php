<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bancos', function (Blueprint $table) {
            // adiciona apenas se não existir (evita erro em ambientes já corretos)
            if (!Schema::hasColumn('bancos', 'nome'))          $table->string('nome')->after('id');
            if (!Schema::hasColumn('bancos', 'agencia'))       $table->string('agencia')->nullable()->after('nome');
            if (!Schema::hasColumn('bancos', 'conta'))         $table->string('conta')->nullable()->after('agencia');
            if (!Schema::hasColumn('bancos', 'tipo'))          $table->enum('tipo', ['corrente','poupanca','outro'])->default('corrente')->after('conta');
            if (!Schema::hasColumn('bancos', 'saldo_inicial')) $table->decimal('saldo_inicial', 12, 2)->default(0)->after('tipo');
            if (!Schema::hasColumn('bancos', 'is_ativo'))      $table->boolean('is_ativo')->default(true)->after('saldo_inicial');
            if (!Schema::hasColumn('bancos', 'observacao'))    $table->text('observacao')->nullable()->after('is_ativo');

            // caso ainda não tenha soft deletes:
            if (!Schema::hasColumn('bancos', 'deleted_at'))    $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('bancos', function (Blueprint $table) {
            // reverte apenas o que foi adicionado acima
            if (Schema::hasColumn('bancos', 'observacao'))    $table->dropColumn('observacao');
            if (Schema::hasColumn('bancos', 'is_ativo'))      $table->dropColumn('is_ativo');
            if (Schema::hasColumn('bancos', 'saldo_inicial')) $table->dropColumn('saldo_inicial');
            if (Schema::hasColumn('bancos', 'tipo'))          $table->dropColumn('tipo');
            if (Schema::hasColumn('bancos', 'conta'))         $table->dropColumn('conta');
            if (Schema::hasColumn('bancos', 'agencia'))       $table->dropColumn('agencia');
            if (Schema::hasColumn('bancos', 'nome'))          $table->dropColumn('nome');
            if (Schema::hasColumn('bancos', 'deleted_at'))    $table->dropSoftDeletes();
        });
    }
};
