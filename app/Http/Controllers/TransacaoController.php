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
        $filtroTipo        = $request->string('tipo')->toString();       // 'receita' | 'despesa' (via categoria)
        $filtroStatus      = $request->string('status')->toString();     // 'pendente' | 'pago'
        $filtroBanco       = $request->integer('banco_id');
        $filtroCategoria   = $request->integer('categoria_id');
        $filtroFornecedor  = $request->integer('fornecedor_id');
        $dataIni           = $request->date('data_ini');
        $dataFim           = $request->date('data_fim');

        /** LISTAGEM **/
        $q = Transacao::query()
            ->with(['categoria', 'banco', 'fornecedor'])
            ->doPeriodo($dataIni, $dataFim)
            ->doBanco($filtroBanco)
            ->daCategoria($filtroCategoria)
            ->doFornecedor($filtroFornecedor)
            ->busca($filtroDescricao);

        // Filtro por "tipo" AGORA via CATEGORIA (não existe coluna 'tipo' em transacoes)
        if ($filtroTipo === 'receita') {
            $q->receita();
        } elseif ($filtroTipo === 'despesa') {
            $q->despesa();
        }

        if ($filtroStatus) {
            $q->where('status', $filtroStatus);
        }

        $transacoes = $q->orderByDesc('data')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        /** TOTAIS (mesmos filtros-base, exceto status) **/
        $base = Transacao::query()
            ->doPeriodo($dataIni, $dataFim)
            ->doBanco($filtroBanco)
            ->daCategoria($filtroCategoria)
            ->doFornecedor($filtroFornecedor)
            ->busca($filtroDescricao);

        // Totais por TIPO via CATEGORIA
        $receitas      = (clone $base)->receita()->sum('valor');
        $despesas      = (clone $base)->despesa()->sum('valor');               // todas as despesas
        $despesasPagas = (clone $base)->despesa()->paga()->sum('valor');       // apenas pagas

        // SALDO = receitas - despesas PAGAS
        $saldo = $receitas - $despesasPagas;

        // Listas para filtros
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
            'saldo'
        ));
    }

    public function create()
    {
        // Se a view create precisar de listas para selects, passe aqui também
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
            // campo de parcelamento vindo do form; se não existir, trate como 1
            'parcela_total' => ['nullable', 'integer', 'min:1'],
        ]);

        $userId        = auth()->id();
        $totalParcelas = max(1, (int)($data['parcela_total'] ?? 1));
        $totalValor    = (float)$data['valor'];
        $dataPrimeira  = Carbon::parse($data['data']);
        $grupo         = Str::uuid()->toString();

        // Converte para centavos para dividir sem erro de ponto flutuante
        $centavosTotal = (int) round($totalValor * 100);
        $centavosBase  = intdiv($centavosTotal, $totalParcelas);
        $resto         = $centavosTotal - ($centavosBase * $totalParcelas);

        DB::transaction(function () use (
            $data,
            $userId,
            $totalParcelas,
            $dataPrimeira,
            $grupo,
            $centavosBase,
            $resto
        ) {
            for ($i = 1; $i <= $totalParcelas; $i++) {
                // Distribui o resto (+1 centavo nas primeiras N parcelas)
                $valorParcelaCent = $centavosBase + ($i <= $resto ? 1 : 0);
                $valorParcela     = $valorParcelaCent / 100;

                Transacao::create([
                    'user_id'        => $userId,
                    'descricao'      => $data['descricao'] . ($totalParcelas > 1 ? " ({$i}/{$totalParcelas})" : ''),
                    'status'         => $data['status'],              // normalmente 'pendente'
                    'valor'          => $valorParcela,
                    'data'           => $dataPrimeira->copy()->addMonths($i - 1),
                    'categoria_id'   => $data['categoria_id'],
                    'banco_id'       => $data['banco_id'] ?? null,
                    'fornecedor_id'  => $data['fornecedor_id'] ?? null,
                    'parcela_num'    => $totalParcelas > 1 ? $i : null,
                    'parcela_total'  => $totalParcelas > 1 ? $totalParcelas : null,
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
        // REMOVIDO 'tipo' da validação (não existe em transacoes)
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

    public function marcarPago(Transacao $transacao)
    {
        // (opcional) garante que a transação é do usuário logado
        if ($transacao->user_id !== auth()->id()) {
            abort(403);
        }

        // já está pago? não faça nada
        if ($transacao->status === 'pago') {
            return back()->with('success', 'Transação já está marcada como paga.');
        }

        $transacao->update(['status' => 'pago']);

        return back()->with('success', 'Transação marcada como paga.');
    }
}
