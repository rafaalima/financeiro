<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use Illuminate\Http\Request;

class BancoController extends Controller
{
    public function index()
    {
        $bancos = Banco::orderBy('nome')->paginate(10);
        return view('bancos.index', compact('bancos'));
    }

    public function create()
    {
        return view('bancos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'          => ['required','string','max:255'],
            'agencia'       => ['nullable','string','max:50'],
            'conta'         => ['nullable','string','max:50'],
            'tipo'          => ['required','in:corrente,poupanca,outro'],
            'saldo_inicial' => ['nullable','numeric'],
            'is_ativo'      => ['nullable','boolean'],
            'observacao'    => ['nullable','string'],
        ]);

        $data['user_id'] = auth()->id();
        $data['is_ativo'] = (bool)($data['is_ativo'] ?? false);
        $data['saldo_inicial'] = (float)($data['saldo_inicial'] ?? 0);
       
        \App\Models\Banco::create($data);
        return redirect()->route('bancos.index')->with('success', 'Banco criado com sucesso!');
    }

    public function edit(Banco $banco)
    {
        return view('bancos.edit', compact('banco'));
    }

    public function update(Request $request, Banco $banco)
    {
        $data = $request->validate([
            'nome'          => ['required','string','max:255'],
            'agencia'       => ['nullable','string','max:50'],
            'conta'         => ['nullable','string','max:50'],
            'tipo'          => ['required','in:corrente,poupanca,outro'],
            'saldo_inicial' => ['nullable','numeric'],
            'is_ativo'      => ['nullable','boolean'],
            'observacao'    => ['nullable','string'],
        ]);
        $data['is_ativo'] = (bool)($data['is_ativo'] ?? false);
        $data['saldo_inicial'] = (float)($data['saldo_inicial'] ?? 0);

        $banco->update($data);
        return redirect()->route('bancos.index')->with('success', 'Banco atualizado!');
    }

    public function destroy(Banco $banco)
    {
        $banco->delete();
        return redirect()->route('bancos.index')->with('success', 'Banco excluído!');
    }
}
