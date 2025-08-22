<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use Illuminate\Http\Request;

class BancoController extends Controller
{


    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $bancos = Banco::query()
            ->when($q, fn($qq) => $qq->where('nome','like',"%{$q}%"))
            ->orderBy('nome')
            ->get();

        return view('bancos.index', compact('bancos','q'));
    }

    public function create()
    {
        return view('bancos.create'); // se não tiver, pode criar depois
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'          => ['required','string','max:100'],
            'agencia'       => ['nullable','string','max:50'],
            'conta'         => ['nullable','string','max:50'],
            'tipo'          => ['nullable','string','max:50'],
            'saldo_inicial' => ['required','numeric'],
            'is_ativo'      => ['nullable','boolean'],
            'observacao'    => ['nullable','string','max:500'],
        ]);

        $data['user_id'] = auth()->id();
        $data['is_ativo'] = (bool)($data['is_ativo'] ?? true);

        Banco::create($data);

        return redirect()->route('bancos.index')->with('success','Banco criado com sucesso.');
    }

    public function edit(Banco $banco)
    {
        return view('bancos.edit', compact('banco'));
    }

    public function update(Request $request, Banco $banco)
    {
        $data = $request->validate([
            'nome'          => ['required','string','max:100'],
            'agencia'       => ['nullable','string','max:50'],
            'conta'         => ['nullable','string','max:50'],
            'tipo'          => ['nullable','string','max:50'],
            'saldo_inicial' => ['required','numeric'],
            'is_ativo'      => ['nullable','boolean'],
            'observacao'    => ['nullable','string','max:500'],
        ]);

        $data['is_ativo'] = (bool)($data['is_ativo'] ?? false);

        $banco->update($data);

        return redirect()->route('bancos.index')->with('success','Banco atualizado com sucesso.');
    }

    public function destroy(Banco $banco)
    {
        $banco->delete();
        return back()->with('success','Banco removido.');
    }

    /**
     * Opcional: Ajustar saldo_inicial = saldo_atual (congelar o saldo)
     */
    public function reconciliar(Banco $banco)
    {
        $banco->update(['saldo_inicial' => $banco->saldo_atual]);
        return back()->with('success','Saldo inicial atualizado para o saldo atual.');
    }
}
