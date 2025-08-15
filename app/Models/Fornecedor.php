<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fornecedor extends Model
{
    use SoftDeletes;

    protected $table = 'fornecedores';   

    protected $fillable = [
        'nome','documento','email','telefone','endereco','cidade','uf','is_ativo','observacao'
    ];

    protected function casts(): array
    {
        return ['is_ativo' => 'boolean'];
    }
}
