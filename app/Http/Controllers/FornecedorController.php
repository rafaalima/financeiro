<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use Illuminate\Http\Request;

class FornecedorController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::orderBy('nome')->paginate(10);
        return view('fornecedores.index', compact('fornecedores'));
    }

    public function create()
    {
        return view('fornecedores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'       => ['required','string','max:255'],
            'documento'  => ['nullable','string','max:30'],
            'email'      => ['nullable','email','max:255'],
            'telefone'   => ['nullable','string','max:30'],
            'endereco'   => ['nullable','string','max:255'],
            'cidade'     => ['nullable','string','max:120'],
            'uf'         => ['nullable','string','size:2'],
            'is_ativo'   => ['nullable','boolean'],
            'observacao' => ['nullable','string'],
        ]);

        $data['is_ativo'] = (bool)($data['is_ativo'] ?? false);

        Fornecedor::create($data);
        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor criado com sucesso!');
    }

    public function edit(\App\Models\Fornecedor $fornecedor)
    {
        $fornecedor = $fornecedor;
        return view('fornecedores.edit', compact('fornecedor'));
    }

    public function update(Request $request, \App\Models\Fornecedor $fornecedor)
    {
        $fornecedor = $fornecedor;

        $data = $request->validate([
            'nome'       => ['required','string','max:255'],
            'documento'  => ['nullable','string','max:30'],
            'email'      => ['nullable','email','max:255'],
            'telefone'   => ['nullable','string','max:30'],
            'endereco'   => ['nullable','string','max:255'],
            'cidade'     => ['nullable','string','max:120'],
            'uf'         => ['nullable','string','size:2'],
            'is_ativo'   => ['nullable','boolean'],
            'observacao' => ['nullable','string'],
        ]);

        $data['is_ativo'] = (bool)($data['is_ativo'] ?? false);

        $fornecedor->update($data);
        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor atualizado!');
    }

    public function destroy(\App\Models\Fornecedor $fornecedor)
    {
        $fornecedor = $fornecedor;
        $fornecedor->delete();
        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor excluído!');
    }
}
