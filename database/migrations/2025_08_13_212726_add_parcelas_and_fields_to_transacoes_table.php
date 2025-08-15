<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transacoes', function (Blueprint $table) {
            // Campos base
            if (!Schema::hasColumn('transacoes', 'descricao'))     $table->string('descricao')->after('id');
            if (!Schema::hasColumn('transacoes', 'valor'))         $table->decimal('valor', 12, 2)->after('descricao');
            if (!Schema::hasColumn('transacoes', 'data'))          $table->date('data')->after('valor');

            // Relacionamentos
            if (!Schema::hasColumn('transacoes', 'categoria_id'))  $table->foreignId('categoria_id')->after('data')->constrained('categorias')->cascadeOnDelete();
            if (!Schema::hasColumn('transacoes', 'fornecedor_id')) $table->foreignId('fornecedor_id')->nullable()->after('categoria_id')->constrained('fornecedores')->nullOnDelete();
            if (!Schema::hasColumn('transacoes', 'banco_id'))      $table->foreignId('banco_id')->nullable()->after('fornecedor_id')->constrained('bancos')->nullOnDelete();

            // Status
            if (!Schema::hasColumn('transacoes', 'status'))        $table->enum('status', ['pendente','pago'])->default('pendente')->after('banco_id');

            // Parcelas
            if (!Schema::hasColumn('transacoes', 'parcela_num'))    $table->unsignedInteger('parcela_num')->default(1)->after('status');
            if (!Schema::hasColumn('transacoes', 'parcela_total'))  $table->unsignedInteger('parcela_total')->default(1)->after('parcela_num');
            if (!Schema::hasColumn('transacoes', 'grupo_uuid'))     $table->uuid('grupo_uuid')->nullable()->after('parcela_total');

            // Observação
            if (!Schema::hasColumn('transacoes', 'observacao'))     $table->text('observacao')->nullable()->after('grupo_uuid');

            // Soft delete e timestamps (se faltarem)
            if (!Schema::hasColumn('transacoes', 'deleted_at'))     $table->softDeletes();
            if (!Schema::hasColumn('transacoes', 'created_at') && !Schema::hasColumn('transacoes', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transacoes', function (Blueprint $table) {
            // Remover FKs com segurança
            if (Schema::hasColumn('transacoes', 'categoria_id'))  $table->dropConstrainedForeignId('categoria_id');
            if (Schema::hasColumn('transacoes', 'fornecedor_id')) $table->dropConstrainedForeignId('fornecedor_id');
            if (Schema::hasColumn('transacoes', 'banco_id'))      $table->dropConstrainedForeignId('banco_id');

            // Remover colunas adicionadas
            foreach (['descricao','valor','data','status','parcela_num','parcela_total','grupo_uuid','observacao'] as $col) {
                if (Schema::hasColumn('transacoes', $col)) $table->dropColumn($col);
            }

            if (Schema::hasColumn('transacoes', 'deleted_at')) $table->dropSoftDeletes();

            // cuidado: só remova timestamps se você tiver certeza que foram criados por esta migration
            // if (Schema::hasColumn('transacoes','created_at') && Schema::hasColumn('transacoes','updated_at')) $table->dropTimestamps();
        });
    }
};
