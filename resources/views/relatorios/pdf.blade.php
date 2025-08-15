<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório Financeiro</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        h2 { font-size: 14px; margin: 18px 0 8px; }
        .muted { color: #6B7280; }
        .kpis { display: flex; gap: 12px; }
        .kpi { flex: 1; border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px; }
        .kpi .label { font-size: 11px; color: #6B7280; }
        .kpi .value { font-size: 16px; font-weight: 700; margin-top: 4px; }
        .green { color: #059669; } .red { color: #DC2626; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #E5E7EB; }
        thead th { font-size: 11px; text-transform: uppercase; letter-spacing: .02em; color:#6B7280; text-align: left; }
        .section { margin-top: 16px; }
        .small { font-size: 11px; }
        .right { text-align: right; }
        .mt-8 { margin-top: 8px; }
        .mt-12 { margin-top: 12px; }
    </style>
</head>
<body>
    <h1>Relatório Financeiro</h1>
    <div class="muted small">Período: {{ $periodoLabel }}</div>

    {{-- KPIs --}}
    <div class="kpis mt-12">
        <div class="kpi">
            <div class="label">Receitas (período)</div>
            <div class="value green">R$ {{ number_format($receitasPeriodo, 2, ',', '.') }}</div>
        </div>
        <div class="kpi">
            <div class="label">Despesas (período)</div>
            <div class="value red">R$ {{ number_format($despesasPeriodo, 2, ',', '.') }}</div>
        </div>
        <div class="kpi">
            <div class="label">Resultado (período)</div>
            @php $cls = $resultadoPeriodo >= 0 ? 'green' : 'red'; @endphp
            <div class="value {{ $cls }}">R$ {{ number_format($resultadoPeriodo, 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- Donuts (versão tabela no PDF) --}}
    <div class="section">
        <h2>Despesas por categoria</h2>
        <table>
            <thead><tr><th>Categoria</th><th class="right">Total</th></tr></thead>
            <tbody>
            @forelse($despCat as $row)
                <tr>
                    <td>{{ $row->nome }}</td>
                    <td class="right">R$ {{ number_format($row->total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="small muted">Sem dados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Receitas por categoria</h2>
        <table>
            <thead><tr><th>Categoria</th><th class="right">Total</th></tr></thead>
            <tbody>
            @forelse($recCat as $row)
                <tr>
                    <td>{{ $row->nome }}</td>
                    <td class="right">R$ {{ number_format($row->total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="small muted">Sem dados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tabelas Resumo --}}
    <div class="section">
        <h2>Resumo por Banco</h2>
        <table>
            <thead><tr><th>Banco</th><th class="right">Receitas</th><th class="right">Despesas</th></tr></thead>
            <tbody>
            @forelse($porBanco as $row)
                <tr>
                    <td>{{ $row->banco }}</td>
                    <td class="right">R$ {{ number_format($row->receitas, 2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($row->despesas, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="small muted">Sem dados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Resumo por Fornecedor</h2>
        <table>
            <thead><tr><th>Fornecedor</th><th class="right">Receitas</th><th class="right">Despesas</th></tr></thead>
            <tbody>
            @forelse($porFornecedor as $row)
                <tr>
                    <td>{{ $row->fornecedor }}</td>
                    <td class="right">R$ {{ number_format($row->receitas, 2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($row->despesas, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="small muted">Sem dados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Detalhe de Transações --}}
    <div class="section">
        <h2>Transações detalhadas</h2>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Banco</th>
                    <th>Fornecedor</th>
                    <th class="right">Valor</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transacoes as $t)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($t->data)->format('d/m/Y') }}</td>
                    <td>{{ $t->descricao }}</td>
                    <td>{{ $t->categoria }} ({{ $t->tipo }})</td>
                    <td>{{ ucfirst($t->status) }}</td>
                    <td>{{ $t->banco }}</td>
                    <td>{{ $t->fornecedor }}</td>
                    <td class="right">R$ {{ number_format($t->valor, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="small muted">Sem transações no período selecionado.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
