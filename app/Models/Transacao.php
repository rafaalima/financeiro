<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transacao extends Model
{
    use HasFactory;

    protected $table = 'transacoes';

    protected $fillable = [
        'user_id',
        'descricao',
        'valor',
        'data',
        'status',        // 'pendente' | 'pago'
        'categoria_id',
        'fornecedor_id',
        'banco_id',
        'parcela_num',
        'parcela_total',
        'grupo_uuid',
        'observacao',
    ];

    protected $casts = [
        'data'  => 'date',
        'valor' => 'decimal:2',
    ];

    /*** RELACIONAMENTOS ***/
    public function categoria()  { return $this->belongsTo(\App\Models\Categoria::class); }
    public function banco()      { return $this->belongsTo(\App\Models\Banco::class); }
    public function fornecedor() { return $this->belongsTo(\App\Models\Fornecedor::class); }
    public function user()       { return $this->belongsTo(\App\Models\User::class); }

    /*** SCOPES ***/
    public function scopeReceita($q)   { return $q->whereHas('categoria', fn($c) => $c->where('tipo', 'receita')); }
    public function scopeDespesa($q)   { return $q->whereHas('categoria', fn($c) => $c->where('tipo', 'despesa')); }
    public function scopePaga($q)      { return $q->where('status', 'pago'); }
    public function scopePendente($q)  { return $q->where('status', 'pendente'); }
    public function scopeDoPeriodo($q, $ini = null, $fim = null)
    {
        if ($ini) { $q->whereDate('data', '>=', $ini); }
        if ($fim) { $q->whereDate('data', '<=', $fim); }
        return $q;
    }
    public function scopeDoBanco($q, $bancoId = null)      { return $bancoId ? $q->where('banco_id', $bancoId) : $q; }
    public function scopeDaCategoria($q, $catId = null)    { return $catId ? $q->where('categoria_id', $catId) : $q; }
    public function scopeDoFornecedor($q, $fornId = null)  { return $fornId ? $q->where('fornecedor_id', $fornId) : $q; }
    public function scopeBusca($q, $termo = null)
    {
        return $termo ? $q->where('descricao', 'like', "%{$termo}%") : $q;
    }
}
