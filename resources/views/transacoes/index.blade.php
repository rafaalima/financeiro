<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Transações</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6 space-y-6">
                {{-- Ações / Filtros --}}
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <a href="{{ route('transacoes.create') }}">
                        <x-primary-button>➕ Nova transação</x-primary-button>
                    </a>

                    @if(session('success'))
                    <div class="rounded-md border border-green-200 bg-green-50 px-3 py-2 text-green-700">
                        {{ session('success') }}
                    </div>
                    @endif
                </div>

                {{-- Filtros compactos --}}
                <form method="GET" class="space-y-3">
                    {{-- linha só das datas (lado a lado) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <x-input-label value="Início" />
                            <x-text-input type="date" name="inicio" class="mt-1 w-full h-10 text-sm"
                                :value="request('inicio')" />
                        </div>

                        <div>
                            <x-input-label value="Fim" />
                            <x-text-input type="date" name="fim" class="mt-1 w-full h-10 text-sm"
                                :value="request('fim')" />
                        </div>
                    </div>

                    {{-- status fica abaixo (linha própria) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <x-input-label value="Status" />
                            <select name="status" class="mt-1 w-full h-10 text-sm rounded-md border-gray-300">
                                <option value="">—</option>
                                <option value="pendente" @selected(request('status')==='pendente' )>Pendente</option>
                                <option value="pago" @selected(request('status')==='pago' )>Pago</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 flex items-end justify-end gap-2">
                            <x-secondary-button class="h-9 px-3 text-sm"
                                onclick="this.closest('form').reset(); this.closest('form').submit();">
                                Limpar
                            </x-secondary-button>
                            <x-primary-button class="h-9 px-4 text-sm">Filtrar</x-primary-button>
                        </div>
                    </div>

                    {{-- Mais filtros (continua igual) --}}
                    <div x-data="{ open: {{ request()->filled('categoria_id') || request()->filled('banco_id') || request()->filled('fornecedor_id') ? 'true' : 'false' }} }">
                        <button type="button" @click="open = !open"
                            class="text-sm text-gray-600 hover:text-gray-800 mt-1">
                            <span x-show="!open">+ Mais filtros</span>
                            <span x-show="open">– Menos filtros</span>
                        </button>

                        <div x-show="open" x-cloak class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <x-input-label value="Categoria" />
                                <select name="categoria_id" class="mt-1 w-full h-10 text-sm rounded-md border-gray-300">
                                    <option value="">—</option>
                                    @foreach($categorias as $c)
                                    <option value="{{ $c->id }}" @selected(request('categoria_id')==$c->id)>
                                        {{ $c->nome }} ({{ $c->tipo }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Banco" />
                                <select name="banco_id" class="mt-1 w-full h-10 text-sm rounded-md border-gray-300">
                                    <option value="">—</option>
                                    @foreach($bancos as $b)
                                    <option value="{{ $b->id }}" @selected(request('banco_id')==$b->id)>{{ $b->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Fornecedor" />
                                <select name="fornecedor_id" class="mt-1 w-full h-10 text-sm rounded-md border-gray-300">
                                    <option value="">—</option>
                                    @foreach($fornecedores as $f)
                                    <option value="{{ $f->id }}" @selected(request('fornecedor_id')==$f->id)>{{ $f->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Totais --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4">
                        <div class="text-sm text-emerald-700">Receitas</div>
                        <div class="text-2xl font-bold text-emerald-700">R$ {{ number_format($receitas,2,',','.') }}</div>
                    </div>
                    <div class="bg-rose-50 border border-rose-100 rounded-xl p-4">
                        <div class="text-sm text-rose-700">Despesas</div>
                        <div class="text-2xl font-bold text-rose-700">R$ {{ number_format($despesas,2,',','.') }}</div>
                    </div>
                    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                        <div class="text-sm text-indigo-700">Saldo</div>
                        <div class="text-2xl font-bold {{ $saldo>=0?'text-indigo-700':'text-rose-700' }}">R$ {{ number_format($saldo,2,',','.') }}</div>
                    </div>
                </div>

                {{-- Tabela --}}
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Banco</th>
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Fornecedor</th>
                                <th class="p-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>
                                <th class="p-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="p-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($transacoes as $t)
                            <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                <td class="p-3 text-sm text-gray-700">{{ $t->data->format('d/m/Y') }}</td>
                                <td class="p-3 text-sm text-gray-900">
                                    {{ $t->descricao }}
                                    @if($t->parcela_total > 1)
                                    <span class="ml-2 inline-flex items-center rounded-full bg-gray-200 text-gray-700 px-2 py-0.5 text-[11px]">
                                        {{ $t->parcela_num }}/{{ $t->parcela_total }}
                                    </span>
                                    @endif
                                </td>
                                <td class="p-3 text-sm text-gray-700">
                                    {{ $t->categoria?->nome }}
                                    @if($t->categoria)
                                    <span class="ml-1 text-[11px] px-1.5 py-0.5 rounded
                                            {{ $t->categoria->tipo === 'receita' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $t->categoria->tipo }}
                                    </span>
                                    @endif
                                </td>
                                <td class="p-3 text-sm text-gray-700">{{ $t->banco?->nome ?? '—' }}</td>
                                <td class="p-3 text-sm text-gray-700">{{ $t->fornecedor?->nome ?? '—' }}</td>
                                <td class="p-3 text-sm text-right font-medium
                                    {{ optional($t->categoria)->tipo === 'receita' ? 'text-emerald-700' : 'text-rose-700' }}">
                                    R$ {{ number_format($t->valor, 2, ',', '.') }}
                                </td>
                                <td class="p-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ $t->status === 'pago' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-700' }}">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="{{ route('transacoes.edit', $t) }}">
                                            <x-secondary-button>Editar</x-secondary-button>
                                        </a>
                                        <form method="POST" action="{{ route('transacoes.destroy', $t) }}"
                                            onsubmit="return confirm('Tem certeza que deseja excluir esta transação?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button>Excluir</x-danger-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-gray-500">Nenhuma transação encontrada.</td>
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