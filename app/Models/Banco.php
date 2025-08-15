<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banco extends Model
{
    use SoftDeletes;

    // tabela padrão já é "bancos"
    protected $fillable = [
        'user_id','nome','agencia','conta','tipo','saldo_inicial','is_ativo','observacao'
    ];

    protected function casts(): array
    {
        return [
            'is_ativo' => 'boolean',
            'saldo_inicial' => 'decimal:2',
        ];
    }
}
