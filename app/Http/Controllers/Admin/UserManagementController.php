<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    // GET /admin/usuarios/novo
    public function create()
    {
        return view('admin.users.create');
    }

    // POST /admin/usuarios
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required','string','max:255'],
            'email'                 => ['required','email','max:255','unique:users,email'],
            'password'              => ['required', Password::defaults(), 'confirmed'],
            'is_admin'              => ['nullable','boolean'],
        ]);

        User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'is_admin'  => (bool)($data['is_admin'] ?? false),
        ]);

        return redirect()
            ->route('admin.users.create')
            ->with('success', 'Usuário criado com sucesso!');
    }
}
