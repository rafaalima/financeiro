<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use App\Models\Banco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $t = (new Transacao)->getTable(); // "transacaos" no seu schema
        $b = (new Banco)->getTable();     // "bancos"

        // Período: padrão = mês atual
        $inicio = $request->input('inicio') ?: now()->startOfMonth()->toDateString();
        $fim    = $request->input('fim')    ?: now()->toDateString();

        // ---- Total dos bancos (do usuário; ignora soft-deletes; ativos/NULL) ----
        $saldoBancos = Banco::query()
            ->when(Schema::hasColumn($b, 'user_id'),   fn($q) => $q->where('user_id', $userId))
            ->when(Schema::hasColumn($b, 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->where(fn($q) => $q->where('is_ativo', 1)->orWhereNull('is_ativo'))
            ->sum(DB::raw('COALESCE(saldo_inicial, 0)'));

        // ---- Totais de receitas e despesas no período ----
        $agg = DB::table($t)
            ->where("$t.user_id", $userId)
            ->when(Schema::hasColumn($t, 'deleted_at'), fn($q) => $q->whereNull("$t.deleted_at"))
            ->when($inicio && $fim, fn($q) => $q->whereBetween("$t.data", [$inicio, $fim]))
            ->join('categorias as c', "$t.categoria_id", '=', 'c.id')
            ->selectRaw("
                COALESCE(SUM(CASE WHEN LOWER(c.tipo) = 'receita' THEN $t.valor ELSE 0 END), 0) AS receitas,
                COALESCE(SUM(CASE WHEN LOWER(c.tipo) = 'despesa' THEN $t.valor ELSE 0 END), 0) AS despesas
            ")
            ->first();

        $receitas = (float) ($agg->receitas ?? 0);
        $despesas = (float) ($agg->despesas ?? 0);

        // DEPOIS (saldo real = total em bancos; período é só informativo)
        $saldo = $saldoBancos;                 // 👈 evita a dupla contagem
        $saldoPeriodo = $receitas - $despesas; // opcional: mostrar como KPI

        $saldoPos = max($saldo, 0);
        $saldoNeg = max(-$saldo, 0);

        // ---- Despesas por categoria (no período) ----
        $catsDesp = DB::table($t)
            ->where("$t.user_id", $userId)
            ->when(Schema::hasColumn($t, 'deleted_at'), fn($q) => $q->whereNull("$t.deleted_at"))
            ->when($inicio && $fim, fn($q) => $q->whereBetween("$t.data", [$inicio, $fim]))
            ->join('categorias as c', "$t.categoria_id", '=', 'c.id')
            ->whereRaw("LOWER(c.tipo) = 'despesa'")
            ->select('c.nome', DB::raw("SUM($t.valor) AS total"))
            ->groupBy('c.id', 'c.nome')
            ->orderByDesc('total')
            ->get();

        $labelsDespesas  = $catsDesp->pluck('nome');
        $dataDespesasCat = $catsDesp->pluck('total')->map(fn($v) => (float) $v);

        // ---- Receitas por categoria (no período) ----
        $catsRec = DB::table($t)
            ->where("$t.user_id", $userId)
            ->when(Schema::hasColumn($t, 'deleted_at'), fn($q) => $q->whereNull("$t.deleted_at"))
            ->when($inicio && $fim, fn($q) => $q->whereBetween("$t.data", [$inicio, $fim]))
            ->join('categorias as c', "$t.categoria_id", '=', 'c.id')
            ->whereRaw("LOWER(c.tipo) = 'receita'")
            ->select('c.nome', DB::raw("SUM($t.valor) AS total"))
            ->groupBy('c.id', 'c.nome')
            ->orderByDesc('total')
            ->get();

        $labelsReceitas  = $catsRec->pluck('nome');
        $dataReceitasCat = $catsRec->pluck('total')->map(fn($v) => (float) $v);

        // ---- Detalhe por banco (lista) ----
        $bancosDetalhe = Banco::query()
            ->when(Schema::hasColumn($b, 'user_id'),   fn($q) => $q->where('user_id', $userId))
            ->when(Schema::hasColumn($b, 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->where(fn($q) => $q->where('is_ativo', 1)->orWhereNull('is_ativo'))
            ->select('id', 'nome', DB::raw('COALESCE(saldo_inicial, 0) AS saldo'))
            ->orderByDesc('saldo')
            ->get();

        $periodoLabel = sprintf(
            '%s a %s',
            \Carbon\Carbon::parse($inicio)->format('d/m/Y'),
            \Carbon\Carbon::parse($fim)->format('d/m/Y')
        );

        return view('dashboard', compact(
            'inicio',
            'fim',
            'periodoLabel',
            'receitas',
            'despesas',
            'saldo',
            'saldoBancos',
            'saldoPos',
            'saldoNeg',
            'labelsDespesas',
            'dataDespesasCat',
            'labelsReceitas',
            'dataReceitasCat',
            'bancosDetalhe',
            'saldoPeriodo'
        ));
    }
}
