<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transacao;
use App\Models\Banco;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    // Período (opcional)
    $inicio = $request->date('data_ini');
    $fim    = $request->date('data_fim');

    // ===== KPIs do PERÍODO (apenas PAGAS) =====
    $basePeriodo = \App\Models\Transacao::query()
        ->when($inicio, fn($q) => $q->whereDate('data', '>=', $inicio))
        ->when($fim,    fn($q) => $q->whereDate('data', '<=', $fim))
        ->where('status', 'pago'); // << só pagas

    $receitasPeriodo = (clone $basePeriodo)
        ->whereHas('categoria', fn($c) => $c->where('tipo','receita'))
        ->sum('valor');

    $despesasPeriodo = (clone $basePeriodo)
        ->whereHas('categoria', fn($c) => $c->where('tipo','despesa'))
        ->sum('valor');

    $resultadoPeriodo = $receitasPeriodo - $despesasPeriodo;

    // ===== KPIs gerais (apenas PAGAS) =====
    $receitas = \App\Models\Transacao::where('status','pago')
        ->whereHas('categoria', fn($c) => $c->where('tipo','receita'))
        ->sum('valor');

    $despesas = \App\Models\Transacao::where('status','pago')
        ->whereHas('categoria', fn($c) => $c->where('tipo','despesa'))
        ->sum('valor');

    $despesasPagas = $despesas;           // compatibilidade com a blade
    $saldo         = $receitas - $despesas;

    // ===== Bancos (dinâmico via accessor => já considera só pagas) =====
    $saldoBancos = \App\Models\Banco::all()->sum->saldo_atual;
    $bancosDetalhe = \App\Models\Banco::orderBy('nome')->get()->map(function ($b) {
        return (object)[
            'id'    => $b->id,
            'nome'  => $b->nome,
            'saldo' => $b->saldo_atual,
        ];
    });

    // Rótulo do período (opcional)
    if ($inicio && $fim) {
        $periodoLabel = 'Período: '.$inicio->format('d/m/Y').' até '.$fim->format('d/m/Y');
    } elseif ($inicio) {
        $periodoLabel = 'A partir de '.$inicio->format('d/m/Y');
    } elseif ($fim) {
        $periodoLabel = 'Até '.$fim->format('d/m/Y');
    } else {
        $periodoLabel = 'Período completo';
    }

    return view('dashboard', compact(
        'inicio','fim','periodoLabel',
        'receitasPeriodo','despesasPeriodo','resultadoPeriodo',
        'saldoBancos','bancosDetalhe',
        'receitas','despesas','despesasPagas','saldo'
    ));
}

}
