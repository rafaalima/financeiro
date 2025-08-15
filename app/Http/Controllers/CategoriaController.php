<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Importa a trait
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class CategoriaController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        $categorias = Categoria::where('user_id', Auth::id())->latest()->get();
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:receita,despesa'
        ]);

        Categoria::create([
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Categoria $categoria)
    {
        $this->authorize('update', $categoria);
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $this->authorize('update', $categoria);

        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:receita,despesa'
        ]);

        $categoria->update($request->only(['nome', 'tipo']));

        return redirect()->route('categorias.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Categoria $categoria)
    {
        $this->authorize('delete', $categoria);
        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoria excluída com sucesso!');
    }
}
