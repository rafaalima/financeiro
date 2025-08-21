<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Transações</h2>
    </x-slot>

    @php
    $filtrosAtivos = request()->filled('q')
    || request()->filled('tipo')
    || request()->filled('status')
    || request()->filled('banco_id')
    || request()->filled('categoria_id')
    || request()->filled('fornecedor_id')
    || request()->filled('data_ini')
    || request()->filled('data_fim');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6 space-y-6">

                {{-- Ações / Filtros --}}
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <a href="{{ route('transacoes.create') }}">
                        <x-primary-button>➕ Nova transação</x-primary-button>
                    </a>

                    @if(session('success'))
                    <div x-data="{ show:true }" x-show="show" x-transition
                        class="rounded-md border border-green-200 bg-green-50 px-3 py-2 text-green-700">
                        {{ session('success') }}
                        <button class="ml-2 underline" @click="show=false">Fechar</button>
                    </div>
                    @endif

                    <div class="flex-1"></div>

                    <div x-data="{ open: {{ $filtrosAtivos ? 'true':'false' }} }">
                        <button type="button" @click="open=!open"
                            class="h-9 px-3 text-sm rounded-lg border bg-white hover:bg-gray-50"
                            :class="open ? 'border-indigo-300 text-indigo-700' : 'border-gray-300 text-gray-700'">
                            <span x-show="!open">Mostrar filtros</span>
                            <span x-show="open">Ocultar filtros</span>
                        </button>

                        {{-- FILTROS --}}
                        <div x-show="open" x-transition class="mt-4">
                            <form method="GET" class="grid md:grid-cols-6 gap-3 items-end">
                                <div class="md:col-span-2">
                                    <x-input-label value="Buscar" />
                                    <x-text-input name="q" value="{{ request('q') }}" class="w-full" />
                                </div>

                                <div>
                                    <x-input-label value="Tipo" />
                                    <select name="tipo" class="w-full border rounded-lg h-10">
                                        <option value="">Todos</option>
                                        <option value="receita" @selected(request('tipo')==='receita' )>Receita</option>
                                        <option value="despesa" @selected(request('tipo')==='despesa' )>Despesa</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label value="Status" />
                                    <select name="status" class="w-full border rounded-lg h-10">
                                        <option value="">Todos</option>
                                        <option value="pendente" @selected(request('status')==='pendente' )>Pendente</option>
                                        <option value="pago" @selected(request('status')==='pago' )>Pago</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label value="Banco" />
                                    <select name="banco_id" class="w-full border rounded-lg h-10">
                                        <option value="">Todos</option>
                                        @foreach($bancos as $b)
                                        <option value="{{ $b->id }}" @selected(request('banco_id')==$b->id)>{{ $b->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <x-input-label value="Categoria" />
                                    <select name="categoria_id" class="w-full border rounded-lg h-10">
                                        <option value="">Todas</option>
                                        @foreach($categorias as $c)
                                        <option value="{{ $c->id }}" @selected(request('categoria_id')==$c->id)>{{ $c->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label value="Fornecedor" />
                                    <select name="fornecedor_id" class="w-full border rounded-lg h-10">
                                        <option value="">Todos</option>
                                        @foreach($fornecedores as $f)
                                        <option value="{{ $f->id }}" @selected(request('fornecedor_id')==$f->id)>{{ $f->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <x-input-label value="Data inicial" />
                                    <x-text-input type="date" name="data_ini" value="{{ request('data_ini') }}" class="w-full" />
                                </div>

                                <div>
                                    <x-input-label value="Data final" />
                                    <x-text-input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full" />
                                </div>

                                <div class="md:col-span-2 flex gap-2">
                                    <x-secondary-button onclick="this.closest('form').reset();this.closest('form').submit();">
                                        Limpar
                                    </x-secondary-button>
                                    <x-primary-button>Filtrar</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- CARDS: Receitas / Despesas / Saldo --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200">
                        <div class="text-sm text-emerald-700 font-medium">Receitas</div>
                        <div class="text-2xl font-semibold text-emerald-900">
                            R$ {{ number_format($receitas, 2, ',', '.') }}
                        </div>
                    </div>

                    <div class="rounded-xl p-4 bg-rose-50 border border-rose-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-rose-700 font-medium">Despesas</div>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-white border border-rose-200 text-rose-600">todas</span>
                        </div>
                        <div class="text-2xl font-semibold text-rose-900">
                            R$ {{ number_format($despesas, 2, ',', '.') }}
                        </div>
                        <div class="mt-1 text-[12px] text-rose-700">
                            (Pagas: R$ {{ number_format($despesasPagas, 2, ',', '.') }})
                        </div>
                    </div>

                    <div class="rounded-xl p-4 bg-indigo-50 border border-indigo-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-indigo-700 font-medium">Saldo</div>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-white border border-indigo-200 text-indigo-600">
                                considera só pagas
                            </span>
                        </div>
                        <div class="text-2xl font-semibold text-indigo-900">
                            R$ {{ number_format($saldo, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- TABELA --}}
                <div class="overflow-x-auto rounded-xl border">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="p-3 text-left">Data</th>
                                <th class="p-3 text-left">Descrição</th>
                                <th class="p-3 text-left">Categoria</th>
                                <th class="p-3 text-left">Banco</th>
                                <th class="p-3 text-left">Fornecedor</th>
                                <th class="p-3 text-right">Valor</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($transacoes as $t)
                            @php $tipo = optional($t->categoria)->tipo; @endphp
                            <tr>
                                {{-- Data --}}
                                <td class="p-3">{{ optional($t->data)->format('d/m/Y') }}</td>

                                {{-- Descrição + Tipo + Parcelas --}}
                                <td class="p-3">
                                    <div class="font-medium">{{ $t->descricao }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $tipo ? strtoupper($tipo) : '—' }}
                                        @if($t->parcela_total)
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full border text-[10px] text-gray-600">
                                            {{ $t->parcela_num }}/{{ $t->parcela_total }}
                                        </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Categoria / Banco / Fornecedor --}}
                                <td class="p-3">{{ optional($t->categoria)->nome ?? '—' }}</td>
                                <td class="p-3">{{ optional($t->banco)->nome ?? '—' }}</td>
                                <td class="p-3">{{ optional($t->fornecedor)->nome ?? '—' }}</td>

                                {{-- Valor --}}
                                <td class="p-3 text-right {{ $tipo==='despesa' ? 'text-rose-600' : 'text-emerald-700' }}">
                                    R$ {{ number_format($t->valor, 2, ',', '.') }}
                                </td>

                                {{-- Status --}}
                                <td class="p-3 text-center">
                                    @if($t->status === 'pago')
                                    <span class="px-2 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Pago
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        Pendente
                                    </span>
                                    @endif
                                </td>

                                {{-- Ações --}}
                                <td class="p-3 text-right space-x-2">
                                    {{-- Botão Marcar como Pago (se pendente) --}}
                                    @if($t->status === 'pendente')
                                    <form method="POST" action="{{ route('transacoes.marcarPago', $t) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <x-primary-button class="!py-1 !px-2 text-xs"
                                            onclick="return confirm('Confirmar pagamento desta transação?')">
                                            ✅ pago
                                        </x-primary-button>
                                    </form>
                                    @endif

                                    {{-- Editar --}}
                                    <a href="{{ route('transacoes.edit', $t) }}">
                                        <x-secondary-button class="!py-1 !px-2 text-xs">Editar</x-secondary-button>
                                    </a>

                                    {{-- Excluir --}}
                                    <form method="POST" action="{{ route('transacoes.destroy', $t) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <x-danger-button class="!py-1 !px-2 text-xs"
                                            onclick="return confirm('Excluir esta transação?')">Excluir</x-danger-button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-gray-500">
                                    Nenhuma transação encontrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="mt-4">
                    {{ $transacoes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>