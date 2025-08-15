<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transacao extends Model
{
    use SoftDeletes;                 // 👈 habilita soft delete
    protected $table = 'transacoes'; // 👈 força o nome correto

    protected $fillable = [
        'user_id','descricao','valor','data','categoria_id','fornecedor_id',
        'banco_id','status','parcela_num','parcela_total','grupo_uuid','observacao',
    ];

    protected function casts(): array
    {
        return ['data' => 'date', 'valor' => 'decimal:2'];
    }

    public function categoria(){ return $this->belongsTo(\App\Models\Categoria::class); }
    public function fornecedor(){ return $this->belongsTo(\App\Models\Fornecedor::class); }
    public function banco(){ return $this->belongsTo(\App\Models\Banco::class); }
}
