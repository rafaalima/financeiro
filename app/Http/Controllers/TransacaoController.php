<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use App\Models\Banco;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class TransacaoController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $inicio        = $request->input('inicio'); // yyyy-mm-dd
        $fim           = $request->input('fim');
        $categoria_id  = $request->input('categoria_id');
        $status        = $request->input('status');
        $banco_id      = $request->input('banco_id');
        $fornecedor_id = $request->input('fornecedor_id');

        $table = (new Transacao)->getTable(); // "transacoes"

        // LISTA (qualificando colunas)
        $q = Transacao::with(['categoria', 'fornecedor', 'banco'])
            ->where("$table.user_id", $userId)
            ->when($inicio && $fim,      fn($qq) => $qq->whereBetween("$table.data", [$inicio, $fim]))
            ->when($inicio && !$fim,     fn($qq) => $qq->where("$table.data", '>=', $inicio))
            ->when(!$inicio && $fim,     fn($qq) => $qq->where("$table.data", '<=', $fim))
            ->when($categoria_id,        fn($qq) => $qq->where("$table.categoria_id", $categoria_id))
            ->when($status,              fn($qq) => $qq->where("$table.status", $status))
            ->when($banco_id,            fn($qq) => $qq->where("$table.banco_id", $banco_id))
            ->when($fornecedor_id,       fn($qq) => $qq->where("$table.fornecedor_id", $fornecedor_id))
            ->orderBy("$table.data", 'desc');

        // TOTAIS (mesmos filtros + ignorar soft-delete)
        $agg = DB::table($table)
            ->where("$table.user_id", $userId)
            ->whereNull("$table.deleted_at")
            ->when($inicio && $fim,      fn($qq) => $qq->whereBetween("$table.data", [$inicio, $fim]))
            ->when($inicio && !$fim,     fn($qq) => $qq->where("$table.data", '>=', $inicio))
            ->when(!$inicio && $fim,     fn($qq) => $qq->where("$table.data", '<=', $fim))
            ->when($categoria_id,        fn($qq) => $qq->where("$table.categoria_id", $categoria_id))
            ->when($status,              fn($qq) => $qq->where("$table.status", $status))
            ->when($banco_id,            fn($qq) => $qq->where("$table.banco_id", $banco_id))
            ->when($fornecedor_id,       fn($qq) => $qq->where("$table.fornecedor_id", $fornecedor_id))
            ->join('categorias', "$table.categoria_id", '=', 'categorias.id')
            ->selectRaw("
                COALESCE(SUM(CASE WHEN LOWER(categorias.tipo) = 'receita' THEN $table.valor ELSE 0 END), 0) AS receitas,
                COALESCE(SUM(CASE WHEN LOWER(categorias.tipo) = 'despesa' THEN $table.valor ELSE 0 END), 0) AS despesas
            ")
            ->first();

        $receitas = (float) ($agg->receitas ?? 0);
        $despesas = (float) ($agg->despesas ?? 0);
        $saldo    = $receitas - $despesas;

        $transacoes  = $q->paginate(12)->withQueryString();
        $categorias  = \App\Models\Categoria::orderBy('nome')->get();
        $bancos      = \App\Models\Banco::orderBy('nome')->get();
        $fornecedores= \App\Models\Fornecedor::orderBy('nome')->get();

        return view('transacoes.index', compact(
            'transacoes','categorias','bancos','fornecedores',
            'receitas','despesas','saldo'
        ));
    }

    public function create()
    {
        $categorias   = \App\Models\Categoria::orderBy('nome')->get();
        $bancos       = \App\Models\Banco::orderBy('nome')->get();
        $fornecedores = \App\Models\Fornecedor::orderBy('nome')->get();

        return view('transacoes.create', compact('categorias', 'bancos', 'fornecedores'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'descricao'     => ['required','string','max:255'],
        'valor'         => ['required','numeric'],
        'data'          => ['required','date'],
        'categoria_id'  => ['required','exists:categorias,id'],
        'banco_id'      => ['nullable','exists:bancos,id'],
        'fornecedor_id' => ['nullable','exists:fornecedores,id'],
        'status'        => ['required','in:pendente,pago'],
        'observacao'    => ['nullable','string'],
        'parcelar'      => ['nullable','boolean'],
        'parcelas'      => ['nullable','integer','min:1','max:240'],
    ]);

    $parcelas = (int)($data['parcelas'] ?? 1);
    if (!($data['parcelar'] ?? false)) $parcelas = 1;

    $userId = auth()->id();

    DB::transaction(function () use ($data, $parcelas, $userId) {
        $grupo        = $parcelas > 1 ? (string) Str::uuid() : null;
        $valorTotal   = (float) $data['valor'];
        $valorParcela = round($valorTotal / $parcelas, 2);
        $acumulado    = 0.0;

        // tipo da categoria (receita|despesa)
        $tipo = $this->tipoCategoria((int)$data['categoria_id']);

        for ($i = 1; $i <= $parcelas; $i++) {
            $valorAtual = ($i < $parcelas) ? $valorParcela : round($valorTotal - $acumulado, 2);
            $acumulado += $valorAtual;

            $tx = Transacao::create([
                'user_id'       => $userId,
                'descricao'     => $data['descricao'] . ($parcelas > 1 ? " ({$i}/{$parcelas})" : ''),
                'valor'         => $valorAtual,
                'data'          => Carbon::parse($data['data'])->addMonthsNoOverflow($i - 1),
                'categoria_id'  => $data['categoria_id'],
                'banco_id'      => $data['banco_id'] ?? null,
                'fornecedor_id' => $data['fornecedor_id'] ?? null,
                'status'        => $data['status'],
                'observacao'    => $data['observacao'] ?? null,
                'parcela_num'   => $i,
                'parcela_total' => $parcelas,
                'grupo_uuid'    => $grupo,
            ]);

            // aplica no saldo do banco se já veio como "pago"
            if ($tx->status === 'pago' && $tx->banco_id) {
                $this->aplicarEfeitoNoBanco((int)$tx->banco_id, $tipo, (float)$tx->valor, +1);
            }
        }
    });

    return redirect()->route('transacoes.index')
        ->with('success', 'Transação(ões) salva(s) com sucesso!');
}


    public function edit(Transacao $transacao)
    {
        // segurança: só o dono pode editar
       // abort_unless((int)$transacao->user_id === (int)auth()->id(), 403);

        $categorias   = \App\Models\Categoria::orderBy('nome')->get();
        $bancos       = \App\Models\Banco::orderBy('nome')->get();
        $fornecedores = \App\Models\Fornecedor::orderBy('nome')->get();

        return view('transacoes.edit', compact('transacao','categorias','bancos','fornecedores'));
    }

    public function update(Request $request, Transacao $transacao)
    {
        // opcional: segurança
        // abort_unless((int)$transacao->user_id === (int)auth()->id(), 403);
    
        $data = $request->validate([
            'descricao'     => ['required','string','max:255'],
            'valor'         => ['required','numeric'],
            'data'          => ['required','date'],
            'categoria_id'  => ['required','exists:categorias,id'],
            'banco_id'      => ['nullable','exists:bancos,id'],
            'fornecedor_id' => ['nullable','exists:fornecedores,id'],
            'status'        => ['required','in:pendente,pago'],
            'observacao'    => ['nullable','string'],
        ]);
    
        DB::transaction(function () use ($transacao, $data) {
            // estado antigo
            $transacao->load('categoria');
            $oldBankId = $transacao->banco_id;
            $oldStatus = $transacao->status;
            $oldValor  = (float)$transacao->valor;
            $oldTipo   = $transacao->categoria?->tipo ?? 'despesa';
    
            // atualiza
            $transacao->update($data);
    
            // estorna efeito antigo (se era pago e tinha banco)
            if ($oldStatus === 'pago' && $oldBankId) {
                $this->aplicarEfeitoNoBanco((int)$oldBankId, $oldTipo, $oldValor, -1);
            }
    
            // aplica efeito novo (se agora é pago e tem banco)
            if ($transacao->status === 'pago' && $transacao->banco_id) {
                $newTipo = $this->tipoCategoria((int)$transacao->categoria_id);
                $this->aplicarEfeitoNoBanco((int)$transacao->banco_id, $newTipo, (float)$transacao->valor, +1);
            }
        });
    
        return redirect()->route('transacoes.index')->with('success', 'Transação atualizada!');
    }
    

    public function destroy(Transacao $transacao)
{
    // opcional: segurança
    // abort_unless((int)$transacao->user_id === (int)auth()->id(), 403);

    DB::transaction(function () use ($transacao) {
        $transacao->refresh()->load('categoria');

        if ($transacao->status === 'pago' && $transacao->banco_id) {
            $tipo = $transacao->categoria?->tipo ?? 'despesa';
            $this->aplicarEfeitoNoBanco((int)$transacao->banco_id, $tipo, (float)$transacao->valor, -1);
        }

        $transacao->delete(); // Soft delete (ou forceDelete() se quiser hard delete)
    });

    return redirect()->route('transacoes.index')->with('success', 'Transação excluída!');
}

    

    /* ============================
       Helpers de saldo do banco
       ============================ */

       private function aplicarEfeitoNoBanco(int $bancoId, string $tipoCategoria, float $valor, int $sinal = +1): void
       {
           // $sinal = +1 aplica; $sinal = -1 estorna
           $delta = ($tipoCategoria === 'receita' ? +1 : -1) * $valor * $sinal;
       
           Banco::where('id', $bancoId)
               ->lockForUpdate()                 // evita “corrida”
               ->increment('saldo_inicial', $delta);
       }
       
       private function tipoCategoria(int $categoriaId): string
       {
           return (string) Categoria::where('id', $categoriaId)->value('tipo'); // 'receita' | 'despesa'
       }
           
}
