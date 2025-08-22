<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Dashboard</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Filtro de período --}}
            <form method="GET" class="mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-input-label value="Início" />
                        <x-text-input
                            type="date"
                            name="inicio"
                            class="mt-1 w-full h-10 text-sm"
                            value="{{ request('inicio') }}"
                        />
                    </div>
                    <div>
                        <x-input-label value="Fim" />
                        <x-text-input
                            type="date"
                            name="fim"
                            class="mt-1 w-full h-10 text-sm"
                            value="{{ request('fim') }}"
                        />
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-end gap-2">
                    <x-secondary-button class="h-9 px-3 text-sm"
                        onclick="this.closest('form').reset(); this.closest('form').submit();">
                        Limpar
                    </x-secondary-button>
                    <x-primary-button class="h-9 px-4 text-sm">Aplicar</x-primary-button>
                </div>

                @php
                    $i = request('inicio'); $f = request('fim');
                    $periodoLabel = $i && $f
                        ? 'Período: '.\Carbon\Carbon::parse($i)->format('d/m/Y').' até '.\Carbon\Carbon::parse($f)->format('d/m/Y')
                        : ($i
                            ? 'A partir de '.\Carbon\Carbon::parse($i)->format('d/m/Y')
                            : ($f
                                ? 'Até '.\Carbon\Carbon::parse($f)->format('d/m/Y')
                                : 'Período completo'));
                @endphp
                <p class="mt-2 text-xs text-gray-500">{{ $periodoLabel }}</p>
            </form>

            {{-- CARDS / KPIs (4 na mesma linha) --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Receitas --}}
                <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200">
                    <div class="text-sm text-emerald-700 font-medium">Receitas</div>
                    <div class="text-2xl font-semibold text-emerald-900">
                        R$ {{ number_format($receitas ?? 0, 2, ',', '.') }}
                    </div>
                </div>

                {{-- Despesas --}}
                <div class="rounded-xl p-4 bg-rose-50 border border-rose-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-rose-700 font-medium">Despesas</div>
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-white border border-rose-200 text-rose-600">todas</span>
                    </div>
                    <div class="text-2xl font-semibold text-rose-900">
                        R$ {{ number_format($despesas ?? 0, 2, ',', '.') }}
                    </div>
                    <div class="mt-1 text-[12px] text-rose-700">
                        (Pagas: R$ {{ number_format($despesasPagas ?? 0, 2, ',', '.') }})
                    </div>
                </div>

                {{-- Bancos (detalhes) --}}
                <div x-data="{ open: false }" class="rounded-xl p-4 bg-sky-50 border border-sky-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-sky-700 font-medium">Bancos</div>
                            <div class="text-2xl font-semibold text-sky-900">
                                R$ {{ number_format($saldoBancos ?? 0, 2, ',', '.') }}
                            </div>
                        </div>

                        @if(($bancosDetalhe ?? collect())->count())
                            <button type="button" @click="open = !open"
                                class="text-[11px] px-2 py-0.5 rounded-full bg-white border border-sky-200 text-sky-700">
                                <span x-show="!open">Detalhes</span>
                                <span x-show="open">Ocultar</span>
                            </button>
                        @endif
                    </div>

                    @if(($bancosDetalhe ?? collect())->count())
                        <ul x-show="open" x-transition class="mt-3 space-y-1 text-sm text-sky-900">
                            @foreach($bancosDetalhe as $bk)
                                <li class="flex items-center justify-between">
                                    <span>{{ $bk->nome }}</span>
                                    <span class="font-medium">R$ {{ number_format($bk->saldo, 2, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div x-show="open" class="mt-2">
                            <a href="{{ route('bancos.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 underline">
                                Ver todos os bancos
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Total em bancos (Saldo) --}}
                @php $saldoBancosPos = ($saldoBancos ?? 0) >= 0; @endphp
                <div class="rounded-xl p-4 bg-sky-50 border border-sky-200">
                    <div class="text-sm text-sky-700 font-medium">Saldo</div>
                    <div class="text-2xl font-semibold {{ $saldoBancosPos ? 'text-sky-900' : 'text-rose-700' }}">
                        R$ {{ number_format($saldoBancos ?? 0, 2, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- Gráficos principais --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white shadow rounded-2xl p-4">
                    <h3 class="font-semibold text-gray-800">Receitas x Despesas</h3>
                    <div class="mt-3 h-48">
                        <canvas id="chartReceitaDespesa"></canvas>
                    </div>
                </div>

                <div class="bg-white shadow rounded-2xl p-4">
                    <h3 class="font-semibold text-gray-800">Saldo</h3>
                    <div class="mt-3 h-48">
                        <canvas id="chartSaldo"></canvas>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ ($saldo ?? 0) >= 0 ? 'Saldo positivo' : 'Saldo negativo' }} considerando todas as transações.
                    </p>
                </div>
            </div>

            {{-- Gráficos por categoria --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white shadow rounded-2xl p-4">
                    <h3 class="text-sm font-medium text-gray-700">Despesas por categoria</h3>
                    @if(collect($dataDespesasCat ?? [])->sum() > 0)
                        <div class="mt-3 h-48">
                            <canvas id="chartDespesasCat"></canvas>
                        </div>
                    @else
                        <p class="mt-3 text-sm text-gray-500">Sem dados de despesas.</p>
                    @endif
                </div>

                <div class="bg-white shadow rounded-2xl p-4">
                    <h3 class="text-sm font-medium text-gray-700">Receitas por categoria</h3>
                    @if(collect($dataReceitasCat ?? [])->sum() > 0)
                        <div class="mt-3 h-48">
                            <canvas id="chartReceitasCat"></canvas>
                        </div>
                    @else
                        <p class="mt-3 text-sm text-gray-500">Sem dados de receitas.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
    <script>
        // Dados vindos do backend
        const receitas        = @json($receitas ?? 0);
        const despesas        = @json($despesas ?? 0);
        const saldo           = @json($saldo ?? 0); // usamos este para derivar saldoPos/saldoNeg
        const labelsDespesas  = @json($labelsDespesas ?? []);
        const dataDespesasCat = @json($dataDespesasCat ?? []);
        const labelsReceitas  = @json($labelsReceitas ?? []);
        const dataReceitasCat = @json($dataReceitasCat ?? []);

        // Derivados inline (sem precisar de variáveis PHP)
        const saldoPos = saldo > 0 ? saldo : 0;
        const saldoNeg = saldo < 0 ? -saldo : 0;

        // Paleta
        const palette = [
            '#6366F1', '#22C55E', '#F43F5E', '#F59E0B', '#06B6D4',
            '#8B5CF6', '#84CC16', '#EF4444', '#10B981', '#3B82F6',
            '#EAB308', '#14B8A6', '#D946EF', '#0EA5E9'
        ];

        // Opções compactas
        const donutOpts = {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8 } },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.label}: R$ ${Number(ctx.raw ?? ctx.parsed ?? 0).toLocaleString('pt-BR',{minimumFractionDigits:2})}`
                    }
                }
            }
        };

        // 1) Receitas x Despesas
        if (document.getElementById('chartReceitaDespesa')) {
            new Chart(document.getElementById('chartReceitaDespesa'), {
                type: 'doughnut',
                data: {
                    labels: ['Receitas', 'Despesas'],
                    datasets: [{ data: [receitas, despesas], backgroundColor: ['#10B981', '#EF4444'], borderWidth: 0 }]
                },
                options: donutOpts
            });
        }

        // 2) Saldo (derivado inline)
        if (document.getElementById('chartSaldo')) {
            new Chart(document.getElementById('chartSaldo'), {
                type: 'doughnut',
                data: {
                    labels: ['Positivo', 'Negativo'],
                    datasets: [{ data: [saldoPos, saldoNeg], backgroundColor: ['#4F46E5', '#F43F5E'], borderWidth: 0 }]
                },
                options: donutOpts
            });
        }

        // 3) Despesas por categoria
        if (labelsDespesas.length && document.getElementById('chartDespesasCat')) {
            new Chart(document.getElementById('chartDespesasCat'), {
                type: 'doughnut',
                data: {
                    labels: labelsDespesas,
                    datasets: [{
                        data: dataDespesasCat,
                        backgroundColor: labelsDespesas.map((_, i) => palette[i % palette.length]),
                        borderWidth: 0
                    }]
                },
                options: donutOpts
            });
        }

        // 4) Receitas por categoria
        if (labelsReceitas.length && document.getElementById('chartReceitasCat')) {
            new Chart(document.getElementById('chartReceitasCat'), {
                type: 'doughnut',
                data: {
                    labels: labelsReceitas,
                    datasets: [{
                        data: dataReceitasCat,
                        backgroundColor: labelsReceitas.map((_, i) => palette[(i + 3) % palette.length]),
                        borderWidth: 0
                    }]
                },
                options: donutOpts
            });
        }
    </script>
</x-app-layout>
