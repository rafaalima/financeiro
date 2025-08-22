<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use App\Models\Banco;
use App\Models\Categoria;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransacaoController extends Controller
{
    public function index(Request $request)
    {
        $filtroDescricao   = $request->string('q')->toString();
        $filtroTipo        = $request->string('tipo')->toString();      // 'receita' | 'despesa' (via categoria)
        $filtroStatus      = $request->string('status')->toString();    // 'pendente' | 'pago'
        $filtroBanco       = $request->integer('banco_id');
        $filtroCategoria   = $request->integer('categoria_id');
        $filtroFornecedor  = $request->integer('fornecedor_id');
        $dataIni           = $request->date('data_ini');
        $dataFim           = $request->date('data_fim');

        /** LISTAGEM **/
        $q = Transacao::query()
            ->with(['categoria', 'banco', 'fornecedor'])
            ->when($dataIni, fn($qq) => $qq->whereDate('data', '>=', $dataIni))
            ->when($dataFim, fn($qq) => $qq->whereDate('data', '<=', $dataFim))
            ->when($filtroBanco, fn($qq) => $qq->where('banco_id', $filtroBanco))
            ->when($filtroCategoria, fn($qq) => $qq->where('categoria_id', $filtroCategoria))
            ->when($filtroFornecedor, fn($qq) => $qq->where('fornecedor_id', $filtroFornecedor))
            ->when($filtroDescricao, fn($qq) => $qq->where('descricao', 'like', "%{$filtroDescricao}%"));

        if ($filtroTipo === 'receita') {
            $q->whereHas('categoria', fn($c) => $c->where('tipo', 'receita'));
        } elseif ($filtroTipo === 'despesa') {
            $q->whereHas('categoria', fn($c) => $c->where('tipo', 'despesa'));
        }
        if ($filtroStatus) {
            $q->where('status', $filtroStatus);
        }

        $transacoes = $q->orderByDesc('data')->orderBy('id', 'desc')
            ->paginate(12)->withQueryString();

        /** TOTAIS PARA OS CARDS (APENAS PAGAS) **/
        $base = Transacao::query()
            ->when($dataIni, fn($qq) => $qq->whereDate('data', '>=', $dataIni))
            ->when($dataFim, fn($qq) => $qq->whereDate('data', '<=', $dataFim))
            ->when($filtroBanco, fn($qq) => $qq->where('banco_id', $filtroBanco))
            ->when($filtroCategoria, fn($qq) => $qq->where('categoria_id', $filtroCategoria))
            ->when($filtroFornecedor, fn($qq) => $qq->where('fornecedor_id', $filtroFornecedor))
            ->when($filtroDescricao, fn($qq) => $qq->where('descricao', 'like', "%{$filtroDescricao}%"))
            ->where('status', 'pago'); // << só pagas

        $receitasPagas = (clone $base)
            ->whereHas('categoria', fn($c) => $c->where('tipo', 'receita'))
            ->sum('valor');

        $despesasPagas = (clone $base)
            ->whereHas('categoria', fn($c) => $c->where('tipo', 'despesa'))
            ->sum('valor');

        // Para compatibilidade com a Blade
        $receitas = $receitasPagas;
        $despesas = $despesasPagas;

        // SALDO = receitas pagas - despesas pagas
        $saldo = $receitasPagas - $despesasPagas;

        /** BANCOS (dinâmico via accessor) **/
        $saldoBancos = Banco::all()->sum->saldo_atual;
        $bancosDetalhe = Banco::orderBy('nome')->get()->map(function ($b) {
            return (object)[
                'id'    => $b->id,
                'nome'  => $b->nome,
                'saldo' => $b->saldo_atual,
            ];
        });

        /** Lists para filtros **/
        $categorias   = Categoria::orderBy('nome')->get();
        $bancos       = Banco::orderBy('nome')->get();
        $fornecedores = Fornecedor::orderBy('nome')->get();

        return view('transacoes.index', compact(
            'transacoes',
            'categorias',
            'bancos',
            'fornecedores',
            'receitas',
            'despesas',
            'despesasPagas',
            'saldo',
            'saldoBancos',
            'bancosDetalhe'
        ));
    }


    public function create()
    {
        $categorias   = Categoria::orderBy('nome')->get();
        $bancos       = Banco::orderBy('nome')->get();
        $fornecedores = Fornecedor::orderBy('nome')->get();

        return view('transacoes.create', compact('categorias', 'bancos', 'fornecedores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'data'          => ['required', 'date'],
            'descricao'     => ['required', 'string', 'max:255'],
            'status'        => ['required', 'in:pendente,pago'],
            'valor'         => ['required', 'numeric', 'min:0'],
            'categoria_id'  => ['required', 'exists:categorias,id'],
            'banco_id'      => ['nullable', 'exists:bancos,id'],
            'fornecedor_id' => ['nullable', 'exists:fornecedores,id'],
            'parcela_total' => ['nullable', 'integer', 'min:1'],
        ]);

        $userId        = auth()->id();
        $totalParcelas = max(1, (int)($data['parcela_total'] ?? 1));
        $totalValor    = (float)$data['valor'];
        $dataPrimeira  = Carbon::parse($data['data']);
        $grupo         = Str::uuid()->toString();

        // divide em centavos para não perder arredondamento
        $centavosTotal = (int) round($totalValor * 100);
        $centavosBase  = intdiv($centavosTotal, $totalParcelas);
        $resto         = $centavosTotal - ($centavosBase * $totalParcelas);

        DB::transaction(function () use ($data, $userId, $totalParcelas, $dataPrimeira, $grupo, $centavosBase, $resto) {
            for ($i = 1; $i <= $totalParcelas; $i++) {
                $valorParcelaCent = $centavosBase + ($i <= $resto ? 1 : 0);
                $valorParcela     = $valorParcelaCent / 100;

                Transacao::create([
                    'user_id'        => $userId,
                    'descricao'      => $data['descricao'] . ($totalParcelas > 1 ? " ({$i}/{$totalParcelas})" : ''),
                    'status'         => $data['status'],
                    'valor'          => $valorParcela,
                    'data'           => $dataPrimeira->copy()->addMonths($i - 1),
                    'categoria_id'   => $data['categoria_id'],
                    'banco_id'       => $data['banco_id'] ?? null,
                    'fornecedor_id'  => $data['fornecedor_id'] ?? null,
                    'parcela_num'    => $i,
                    'parcela_total'  => $totalParcelas,
                    'grupo_uuid'     => $totalParcelas > 1 ? $grupo : null,
                ]);
            }
        });

        return redirect()->route('transacoes.index')
            ->with('success', 'Transação' . ($totalParcelas > 1 ? ' parcelada' : '') . ' criada com sucesso.');
    }

    public function edit(Transacao $transacao)
    {
        $categorias   = Categoria::orderBy('nome')->get();
        $bancos       = Banco::orderBy('nome')->get();
        $fornecedores = Fornecedor::orderBy('nome')->get();

        return view('transacoes.edit', compact('transacao', 'categorias', 'bancos', 'fornecedores'));
    }

    public function update(Request $request, Transacao $transacao)
    {
        $data = $request->validate([
            'data'          => ['required', 'date'],
            'descricao'     => ['required', 'string', 'max:255'],
            'status'        => ['required', 'in:pendente,pago'],
            'valor'         => ['required', 'numeric', 'min:0'],
            'categoria_id'  => ['required', 'exists:categorias,id'],
            'banco_id'      => ['nullable', 'exists:bancos,id'],
            'fornecedor_id' => ['nullable', 'exists:fornecedores,id'],
        ]);

        $transacao->update($data);

        return redirect()->route('transacoes.index')->with('success', 'Transação atualizada com sucesso.');
    }

    public function destroy(Transacao $transacao)
    {
        $transacao->delete();
        return back()->with('success', 'Transação excluída.');
    }

    // Atalho na lista
    public function marcarPago(Transacao $transacao)
    {
        if ($transacao->user_id !== auth()->id()) {
            abort(403);
        }
        if ($transacao->status === 'pago') {
            return back()->with('success', 'Transação já está marcada como paga.');
        }
        $transacao->update(['status' => 'pago']);
        return back()->with('success', 'Transação marcada como paga.');
    }
}
