<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transacao extends Model
{
    use HasFactory;

    protected $table = 'transacoes';

    // app/Models/Transacao.php
protected $fillable = [
    'user_id',
    'descricao',
    'valor',
    'data',
    'status',
    'categoria_id',
    'fornecedor_id',
    'banco_id',
    'parcela_num',     // número da parcela (1, 2, 3…)
    'parcela_total',   // total de parcelas
    'grupo_uuid',      // agrupa as parcelas
    'observacao',
];


    protected $casts = [
        'data'  => 'date',
        'valor' => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(\App\Models\Categoria::class);
    }
    public function banco()
    {
        return $this->belongsTo(\App\Models\Banco::class);
    }
    public function fornecedor()
    {
        return $this->belongsTo(\App\Models\Fornecedor::class);
    }

    // AGORA: receita/despesa via CATEGORIA
    public function scopeReceita($q)
    {
        return $q->whereHas('categoria', fn($c) => $c->where('tipo', 'receita'));
    }

    public function scopeDespesa($q)
    {
        return $q->whereHas('categoria', fn($c) => $c->where('tipo', 'despesa'));
    }

    public function scopePaga($q)
    {
        return $q->where('status', 'pago');
    }
    public function scopePendente($q)
    {
        return $q->where('status', 'pendente');
    }

    public function scopeDoPeriodo($q, $ini = null, $fim = null)
    {
        if ($ini) {
            $q->whereDate('data', '>=', $ini);
        }
        if ($fim) {
            $q->whereDate('data', '<=', $fim);
        }
        return $q;
    }

    public function scopeDoBanco($q, $bancoId = null)
    {
        return $bancoId ? $q->where('banco_id', $bancoId) : $q;
    }

    public function scopeDaCategoria($q, $catId = null)
    {
        return $catId ? $q->where('categoria_id', $catId) : $q;
    }

    public function scopeDoFornecedor($q, $fornId = null)
    {
        return $fornId ? $q->where('fornecedor_id', $fornId) : $q;
    }

    public function scopeBusca($q, $termo = null)
    {
        return $termo
            ? $q->where(function ($qq) use ($termo) {
                $qq->where('descricao', 'like', "%{$termo}%");
            })
            : $q;
    }
}
