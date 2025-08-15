<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;

class CategoriaPolicy
{
    /**
     * Determine se o usuário pode atualizar a categoria.
     */
    public function update(User $user, Categoria $categoria)
    {
        return $user->id === $categoria->user_id;
    }

    /**
     * Determine se o usuário pode deletar a categoria.
     */
    public function delete(User $user, Categoria $categoria)
    {
        return $user->id === $categoria->user_id;
    }
}
