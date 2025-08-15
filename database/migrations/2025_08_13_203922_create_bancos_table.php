<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');                 // Ex.: Nubank, Itaú…
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->enum('tipo', ['corrente', 'poupanca', 'outro'])->default('corrente');
            $table->decimal('saldo_inicial', 12, 2)->default(0);
            $table->boolean('is_ativo')->default(true);
            $table->text('observacao')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bancos');
    }
};
