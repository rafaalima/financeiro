<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Relatórios</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filtros --}}
            <form method="GET" class="bg-white shadow rounded-2xl p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <x-input-label value="Início" />
                        <x-text-input type="date" name="inicio" class="mt-1 w-full"
                            :value="$inicio" />
                    </div>
                    <div>
                        <x-input-label value="Fim" />
                        <x-text-input type="date" name="fim" class="mt-1 w-full"
                            :value="$fim" />
                    </div>
                    <div>
                        <x-input-label value="Status" />
                        <select name="status" class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">—</option>
                            <option value="pago" {{ $status==='pago'?'selected':'' }}>Pago</option>
                            <option value="pendente" {{ $status==='pendente'?'selected':'' }}>Pendente</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label value="Categoria" />
                        <select name="categoria_id" class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">—</option>
                            @foreach($categorias as $c)
                            <option value="{{ $c->id }}" @selected($categoria_id==$c->id)>{{ $c->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Banco" />
                        <select name="banco_id" class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">—</option>
                            @foreach($bancos as $b)
                            <option value="{{ $b->id }}" @selected($banco_id==$b->id)>{{ $b->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Fornecedor" />
                        <select name="fornecedor_id" class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">—</option>
                            @foreach($fornecedores as $f)
                            <option value="{{ $f->id }}" @selected($fornecedor_id==$f->id)>{{ $f->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end gap-2">
                    <a href="{{ route('relatorios.index') }}"
                        class="px-3 py-2 text-sm rounded-lg border">Limpar</a>
                    <x-primary-button>Aplicar</x-primary-button>

                    <a href="{{ route('relatorios.pdf', request()->query()) }}"
                        class="px-3 py-2 text-sm rounded-lg bg-rose-600 text-white hover:bg-rose-700">
                        Exportar PDF
                    </a>


                    <a href="{{ route('relatorios.export', request()->query()) }}"
                        class="px-3 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                        Exportar CSV
                    </a>
                </div>
            </form>

            {{-- KPIs --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white shadow rounded-2xl p-5">
                    <div class="text-sm text-gray-500">Receitas (período)</div>
                    <div class="mt-1 text-2xl font-bold text-emerald-600">
                        R$ {{ number_format($receitasPeriodo,2,',','.') }}
                    </div>
                </div>
                <div class="bg-white shadow rounded-2xl p-5">
                    <div class="text-sm text-gray-500">Despesas (período)</div>
                    <div class="mt-1 text-2xl font-bold text-rose-600">
                        R$ {{ number_format($despesasPeriodo,2,',','.') }}
                    </div>
                </div>
                <div class="bg-white shadow rounded-2xl p-5">
                    <div class="text-sm text-gray-500">Resultado (período)</div>
                    <div class="mt-1 text-2xl font-bold {{ $resultadoPeriodo>=0 ? 'text-emerald-600':'text-rose-600' }}">
                        R$ {{ number_format($resultadoPeriodo,2,',','.') }}
                    </div>
                </div>
            </div>

            {{-- Gráficos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">


                <div class="bg-white shadow rounded-2xl p-4">
                    <h3 class="font-semibold text-gray-800">Despesas por categoria</h3>
                    <div class="mt-3 h-56">
                        <canvas id="chartDespCat"></canvas>
                    </div>
                </div>

                <div class="bg-white shadow rounded-2xl p-4">
                    <h3 class="font-semibold text-gray-800">Receitas por categoria</h3>
                    <div class="mt-3 h-56">
                        <canvas id="chartRecCat"></canvas>
                    </div>
                </div>


            </div>

            {{-- Tabelas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white shadow rounded-2xl p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Por Banco</h3>
                    <table class="w-full text-sm">
                        <thead class="text-gray-500">
                            <tr>
                                <th class="py-2 text-left">Banco</th>
                                <th class="text-right">Receitas</th>
                                <th class="text-right">Despesas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($porBanco as $row)
                            <tr class="border-t">
                                <td class="py-2">{{ $row->banco }}</td>
                                <td class="text-right text-emerald-600">R$ {{ number_format($row->receitas,2,',','.') }}</td>
                                <td class="text-right text-rose-600">R$ {{ number_format($row->despesas,2,',','.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-3 text-center text-gray-500">Sem dados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow rounded-2xl p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Por Fornecedor</h3>
                    <table class="w-full text-sm">
                        <thead class="text-gray-500">
                            <tr>
                                <th class="py-2 text-left">Fornecedor</th>
                                <th class="text-right">Receitas</th>
                                <th class="text-right">Despesas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($porFornecedor as $row)
                            <tr class="border-t">
                                <td class="py-2">{{ $row->fornecedor }}</td>
                                <td class="text-right text-emerald-600">R$ {{ number_format($row->receitas,2,',','.') }}</td>
                                <td class="text-right text-rose-600">R$ {{ number_format($row->despesas,2,',','.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-3 text-center text-gray-500">Sem dados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-white shadow rounded-2xl p-4">
                <h3 class="font-semibold text-gray-800">Fluxo diário</h3>
                <div class="mt-3 h-56">
                    <canvas id="chartFluxo"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
    <script>
        const labelsDias = @json($labelsDias);
        const dataRecDia = @json($dataRecDia);
        const dataDespDia = @json($dataDespDia);

        const labelsDespCat = @json($labelsDespCat);
        const dataDespCat = @json($dataDespCat);

        const labelsRecCat = @json($labelsRecCat);
        const dataRecCat = @json($dataRecCat);

        // Linha: Fluxo diário
        new Chart(document.getElementById('chartFluxo'), {
            type: 'line',
            data: {
                labels: labelsDias,
                datasets: [{
                        label: 'Receitas',
                        data: dataRecDia,
                        borderWidth: 2,
                        tension: .2
                    },
                    {
                        label: 'Despesas',
                        data: dataDespDia,
                        borderWidth: 2,
                        tension: .2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        const donutOpts = {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        };

        // Donut: Despesas por categoria
        new Chart(document.getElementById('chartDespCat'), {
            type: 'doughnut',
            data: {
                labels: labelsDespCat,
                datasets: [{
                    data: dataDespCat
                }]
            },
            options: donutOpts
        });

        // Donut: Receitas por categoria
        new Chart(document.getElementById('chartRecCat'), {
            type: 'doughnut',
            data: {
                labels: labelsRecCat,
                datasets: [{
                    data: dataRecCat
                }]
            },
            options: donutOpts
        });
    </script>
</x-app-layout>