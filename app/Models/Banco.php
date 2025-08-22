<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banco extends Model
{
    use HasFactory;

    protected $table = 'bancos';

    protected $fillable = [
        'user_id',
        'nome',
        'agencia',
        'conta',
        'tipo',
        'saldo_inicial',
        'is_ativo',
        'observacao',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'is_ativo'      => 'boolean',
    ];

    /** Relacionamentos **/
    public function transacoes()
    {
        return $this->hasMany(\App\Models\Transacao::class, 'banco_id');
    }

    /**
     * Saldo atual calculado em tempo real:
     * saldo_inicial + receitas pagas - despesas pagas
     */
    public function getSaldoAtualAttribute()
    {
        $receitas = $this->transacoes()
            ->whereHas('categoria', fn($c) => $c->where('tipo','receita'))
            ->where('status','pago')
            ->sum('valor');

        $despesas = $this->transacoes()
            ->whereHas('categoria', fn($c) => $c->where('tipo','despesa'))
            ->where('status','pago')
            ->sum('valor');

        return (float)$this->saldo_inicial + (float)$receitas - (float)$despesas;
    }
}
