<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use App\Models\Categoria;
use App\Models\Banco;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Nomes reais das tabelas
        $t = (new Transacao)->getTable();
        $c = (new Categoria)->getTable();
        $b = (new Banco)->getTable();
        $f = (new Fornecedor)->getTable();

        // Filtros (padrão: mês atual)
        $inicio        = $request->input('inicio') ?: now()->startOfMonth()->toDateString();
        $fim           = $request->input('fim')    ?: now()->toDateString();
        $status        = trim((string) $request->input('status')); // '', 'pago', 'pendente', 'todos'
        $categoria_id  = $request->input('categoria_id');
        $banco_id      = $request->input('banco_id');
        $fornecedor_id = $request->input('fornecedor_id');

        // Base (Query Builder)
        $base = DB::table($t)
            ->where("$t.user_id", $userId)
            ->when(Schema::hasColumn($t, 'deleted_at'), fn($q) => $q->whereNull("$t.deleted_at"))
            ->when($inicio && $fim, fn($q) => $q->whereBetween("$t.data", [$inicio, $fim]))
            ->when($categoria_id, fn($q) => $q->where("$t.categoria_id", $categoria_id))
            ->when($banco_id, fn($q) => $q->where("$t.banco_id", $banco_id))
            ->when($fornecedor_id, fn($q) => $q->where("$t.fornecedor_id", $fornecedor_id));

        // >>> REGRA DE STATUS <<<
        // default = apenas PAGAS; se usuário escolher, respeitamos; se 'todos', sem filtro.
        if ($status === 'pago' || $status === 'pendente') {
            $base->where("$t.status", $status);
        } elseif ($status === 'todos') {
            // não filtra
        } else {
            $base->where("$t.status", 'pago');
        }

        // KPIs do período
        $agg = (clone $base)
            ->join("$c as c", "$t.categoria_id", '=', 'c.id')
            ->selectRaw("
            COALESCE(SUM(CASE WHEN LOWER(c.tipo) = 'receita' THEN $t.valor ELSE 0 END), 0) AS receitas,
            COALESCE(SUM(CASE WHEN LOWER(c.tipo) = 'despesa' THEN $t.valor ELSE 0 END), 0) AS despesas
        ")
            ->first();

        $receitasPeriodo  = (float)($agg->receitas ?? 0);
        $despesasPeriodo  = (float)($agg->despesas ?? 0);
        $resultadoPeriodo = $receitasPeriodo - $despesasPeriodo;

        // Fluxo diário (linhas)
        $fluxo = (clone $base)
            ->join("$c as c", "$t.categoria_id", '=', 'c.id')
            ->selectRaw("
            DATE($t.data) as dia,
            COALESCE(SUM(CASE WHEN LOWER(c.tipo)='receita' THEN $t.valor ELSE 0 END),0) as rec,
            COALESCE(SUM(CASE WHEN LOWER(c.tipo)='despesa' THEN $t.valor ELSE 0 END),0) as desp
        ")
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $labelsDias  = $fluxo->pluck('dia')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'));
        $dataRecDia  = $fluxo->pluck('rec')->map(fn($v) => (float)$v);
        $dataDespDia = $fluxo->pluck('desp')->map(fn($v) => (float)$v);

        // Donut: Despesas por categoria
        $despCat = (clone $base)
            ->join("$c as c", "$t.categoria_id", '=', 'c.id')
            ->whereRaw("LOWER(c.tipo)='despesa'")
            ->select('c.nome', DB::raw("SUM($t.valor) as total"))
            ->groupBy('c.id', 'c.nome')
            ->orderByDesc('total')
            ->get();

        $labelsDespCat = $despCat->pluck('nome');
        $dataDespCat   = $despCat->pluck('total')->map(fn($v) => (float)$v);

        // Donut: Receitas por categoria
        $recCat = (clone $base)
            ->join("$c as c", "$t.categoria_id", '=', 'c.id')
            ->whereRaw("LOWER(c.tipo)='receita'")
            ->select('c.nome', DB::raw("SUM($t.valor) as total"))
            ->groupBy('c.id', 'c.nome')
            ->orderByDesc('total')
            ->get();

        $labelsRecCat = $recCat->pluck('nome');
        $dataRecCat   = $recCat->pluck('total')->map(fn($v) => (float)$v);

        // Tabela: por banco
        $porBanco = (clone $base)
            ->leftJoin("$b as b", "$t.banco_id", '=', 'b.id')
            ->leftJoin("$c as c", "$t.categoria_id", '=', 'c.id')
            ->selectRaw("
            COALESCE(b.nome, '—') as banco,
            COALESCE(SUM(CASE WHEN LOWER(c.tipo)='receita' THEN $t.valor ELSE 0 END),0) as receitas,
            COALESCE(SUM(CASE WHEN LOWER(c.tipo)='despesa' THEN $t.valor ELSE 0 END),0) as despesas
        ")
            ->groupBy('banco')
            ->orderBy('banco')
            ->get();

        // Tabela: por fornecedor
        $porFornecedor = (clone $base)
            ->leftJoin("$f as forn", "$t.fornecedor_id", '=', 'forn.id')
            ->leftJoin("$c as c", "$t.categoria_id", '=', 'c.id')
            ->selectRaw("
            COALESCE(forn.nome, '—') as fornecedor,
            COALESCE(SUM(CASE WHEN LOWER(c.tipo)='receita' THEN $t.valor ELSE 0 END),0) as receitas,
            COALESCE(SUM(CASE WHEN LOWER(c.tipo)='despesa' THEN $t.valor ELSE 0 END),0) as despesas
        ")
            ->groupBy('fornecedor')
            ->orderBy('fornecedor')
            ->get();

        // Selects dos filtros
        $categorias   = Categoria::orderBy('nome')->get();
        $bancos       = Banco::orderBy('nome')->get();
        $fornecedores = Fornecedor::orderBy('nome')->get();

        return view('relatorios.index', compact(
            'inicio',
            'fim',
            'status',
            'categoria_id',
            'banco_id',
            'fornecedor_id',
            'categorias',
            'bancos',
            'fornecedores',
            'receitasPeriodo',
            'despesasPeriodo',
            'resultadoPeriodo',
            'labelsDias',
            'dataRecDia',
            'dataDespDia',
            'labelsDespCat',
            'dataDespCat',
            'labelsRecCat',
            'dataRecCat',
            'porBanco',
            'porFornecedor'
        ));
    }


    public function exportCsv(Request $request)
    {
        $userId = auth()->id();

        $t = (new Transacao)->getTable();
        $c = (new Categoria)->getTable();
        $b = (new Banco)->getTable();
        $f = (new Fornecedor)->getTable();

        $inicio        = $request->input('inicio');
        $fim           = $request->input('fim');
        $status        = $request->input('status');
        $categoria_id  = $request->input('categoria_id');
        $banco_id      = $request->input('banco_id');
        $fornecedor_id = $request->input('fornecedor_id');

        $rows = DB::table($t)
            ->where("$t.user_id", $userId)
            ->when(Schema::hasColumn($t, 'deleted_at'), fn($q) => $q->whereNull("$t.deleted_at"))
            ->when($inicio && $fim, fn($q) => $q->whereBetween("$t.data", [$inicio, $fim]))
            ->when($status, fn($q) => $q->where("$t.status", $status))
            ->when($categoria_id, fn($q) => $q->where("$t.categoria_id", $categoria_id))
            ->when($banco_id, fn($q) => $q->where("$t.banco_id", $banco_id))
            ->when($fornecedor_id, fn($q) => $q->where("$t.fornecedor_id", $fornecedor_id))
            ->leftJoin("$c as c", "$t.categoria_id", '=', 'c.id')
            ->leftJoin("$b as b", "$t.banco_id", '=', 'b.id')
            ->leftJoin("$f as forn", "$t.fornecedor_id", '=', 'forn.id') // único join de fornecedor
            ->orderBy("$t.data")
            ->get([
                "$t.data as data",
                "$t.descricao as descricao",
                "$t.valor as valor",
                "$t.status as status",
                "c.nome as categoria",
                DB::raw("LOWER(c.tipo) as tipo"),
                "b.nome as banco",
                "forn.nome as fornecedor",
            ]);

        $filename = 'transacoes_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Data', 'Descrição', 'Valor', 'Status', 'Categoria', 'Tipo', 'Banco', 'Fornecedor'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    \Carbon\Carbon::parse($r->data)->format('d/m/Y'),
                    $r->descricao,
                    number_format($r->valor, 2, ',', '.'),
                    $r->status,
                    $r->categoria,
                    $r->tipo,
                    $r->banco,
                    $r->fornecedor,
                ], ';');
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $userId = auth()->id();

        // Nomes reais das tabelas
        $t = (new Transacao)->getTable();   // ex.: transacaos
        $c = (new Categoria)->getTable();   // ex.: categorias
        $b = (new Banco)->getTable();       // ex.: bancos
        $f = (new Fornecedor)->getTable();  // ex.: fornecedores

        // Filtros
        $inicio        = $request->input('inicio') ?: now()->startOfMonth()->toDateString();
        $fim           = $request->input('fim')    ?: now()->toDateString();
        $status        = $request->input('status');
        $categoria_id  = $request->input('categoria_id');
        $banco_id      = $request->input('banco_id');
        $fornecedor_id = $request->input('fornecedor_id');

        // Base
        $base = DB::table($t)
            ->where("$t.user_id", $userId)
            ->when(Schema::hasColumn($t, 'deleted_at'), fn($q) => $q->whereNull("$t.deleted_at"))
            ->when($inicio && $fim, fn($q) => $q->whereBetween("$t.data", [$inicio, $fim]))
            ->when($status, fn($q) => $q->where("$t.status", $status))
            ->when($categoria_id, fn($q) => $q->where("$t.categoria_id", $categoria_id))
            ->when($banco_id, fn($q) => $q->where("$t.banco_id", $banco_id))
            ->when($fornecedor_id, fn($q) => $q->where("$t.fornecedor_id", $fornecedor_id));

        // KPIs
        $agg = (clone $base)
            ->join("$c as c", "$t.categoria_id", '=', 'c.id')
            ->selectRaw("
                COALESCE(SUM(CASE WHEN LOWER(c.tipo)='receita' THEN $t.valor ELSE 0 END),0) as receitas,
                COALESCE(SUM(CASE WHEN LOWER(c.tipo)='despesa' THEN $t.valor ELSE 0 END),0) as despesas
            ")
            ->first();

        $receitasPeriodo  = (float)($agg->receitas ?? 0);
        $despesasPeriodo  = (float)($agg->despesas ?? 0);
        $resultadoPeriodo = $receitasPeriodo - $despesasPeriodo;

        // Despesas por categoria
        $despCat = (clone $base)
            ->join("$c as c", "$t.categoria_id", '=', 'c.id')
            ->whereRaw("LOWER(c.tipo)='despesa'")
            ->select('c.nome', DB::raw("SUM($t.valor) as total"))
            ->groupBy('c.id', 'c.nome')
            ->orderByDesc('total')
            ->get();

        // Receitas por categoria
        $recCat = (clone $base)
            ->join("$c as c", "$t.categoria_id", '=', 'c.id')
            ->whereRaw("LOWER(c.tipo)='receita'")
            ->select('c.nome', DB::raw("SUM($t.valor) as total"))
            ->groupBy('c.id', 'c.nome')
            ->orderByDesc('total')
            ->get();

        // Por banco
        $porBanco = (clone $base)
            ->leftJoin("$b as b", "$t.banco_id", '=', 'b.id')
            ->leftJoin("$c as c", "$t.categoria_id", '=', 'c.id')
            ->selectRaw("
                COALESCE(b.nome, '—') as banco,
                COALESCE(SUM(CASE WHEN LOWER(c.tipo)='receita' THEN $t.valor ELSE 0 END),0) as receitas,
                COALESCE(SUM(CASE WHEN LOWER(c.tipo)='despesa' THEN $t.valor ELSE 0 END),0) as despesas
            ")
            ->groupBy('banco')
            ->orderBy('banco')
            ->get();

        // Por fornecedor
        $porFornecedor = (clone $base)
            ->leftJoin("$f as forn", "$t.fornecedor_id", '=', 'forn.id')
            ->leftJoin("$c as c", "$t.categoria_id", '=', 'c.id')
            ->selectRaw("
                COALESCE(forn.nome, '—') as fornecedor,
                COALESCE(SUM(CASE WHEN LOWER(c.tipo)='receita' THEN $t.valor ELSE 0 END),0) as receitas,
                COALESCE(SUM(CASE WHEN LOWER(c.tipo)='despesa' THEN $t.valor ELSE 0 END),0) as despesas
            ")
            ->groupBy('fornecedor')
            ->orderBy('fornecedor')
            ->get();

        // Lista de transações (detalhado)
        $transacoes = (clone $base)
            ->leftJoin("$c as c", "$t.categoria_id", '=', 'c.id')
            ->leftJoin("$b as b", "$t.banco_id", '=', 'b.id')
            ->leftJoin("$f as forn", "$t.fornecedor_id", '=', 'forn.id')
            ->orderBy("$t.data")
            ->get([
                "$t.data as data",
                "$t.descricao as descricao",
                "$t.valor as valor",
                "$t.status as status",
                "c.nome as categoria",
                DB::raw("LOWER(c.tipo) as tipo"),
                "b.nome as banco",
                "forn.nome as fornecedor",
            ]);

        $periodoLabel = sprintf(
            '%s a %s',
            \Carbon\Carbon::parse($inicio)->format('d/m/Y'),
            \Carbon\Carbon::parse($fim)->format('d/m/Y')
        );

        $pdf = Pdf::loadView('relatorios.pdf', [
            'inicio'            => $inicio,
            'fim'               => $fim,
            'periodoLabel'      => $periodoLabel,
            'receitasPeriodo'   => $receitasPeriodo,
            'despesasPeriodo'   => $despesasPeriodo,
            'resultadoPeriodo'  => $resultadoPeriodo,
            'despCat'           => $despCat,
            'recCat'            => $recCat,
            'porBanco'          => $porBanco,
            'porFornecedor'     => $porFornecedor,
            'transacoes'        => $transacoes,
        ])->setPaper('a4', 'portrait');

        $filename = 'relatorio_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
        // ou ->stream($filename) para abrir no navegador
    }
}
