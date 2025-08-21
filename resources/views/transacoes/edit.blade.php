<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Editar transação</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6">
                <form method="POST" action="{{ route('transacoes.update', $transacao) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Descrição" />
                            <x-text-input name="descricao" value="{{ old('descricao', $transacao->descricao) }}" class="w-full" />
                            <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Valor" />
                            <x-text-input type="number" step="0.01" min="0" name="valor" value="{{ old('valor', $transacao->valor) }}" class="w-full" />
                            <x-input-error :messages="$errors->get('valor')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Data" />
                            <x-text-input type="date" name="data" value="{{ old('data', optional($transacao->data)->format('Y-m-d')) }}" class="w-full" />
                            <x-input-error :messages="$errors->get('data')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Status" />
                            <select name="status" class="w-full h-10 border rounded-lg">
                                <option value="pendente" @selected(old('status', $transacao->status)==='pendente')>Pendente</option>
                                <option value="pago" @selected(old('status', $transacao->status)==='pago')>Pago</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Categoria" />
                            <select name="categoria_id" class="w-full h-10 border rounded-lg">
                                @foreach($categorias as $c)
                                    <option value="{{ $c->id }}" @selected(old('categoria_id', $transacao->categoria_id)==$c->id)>{{ $c->nome }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('categoria_id')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Banco" />
                            <select name="banco_id" class="w-full h-10 border rounded-lg">
                                <option value="">—</option>
                                @foreach($bancos as $b)
                                    <option value="{{ $b->id }}" @selected(old('banco_id', $transacao->banco_id)==$b->id)>{{ $b->nome }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('banco_id')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Fornecedor" />
                            <select name="fornecedor_id" class="w-full h-10 border rounded-lg">
                                <option value="">—</option>
                                @foreach($fornecedores as $f)
                                    <option value="{{ $f->id }}" @selected(old('fornecedor_id', $transacao->fornecedor_id)==$f->id)>{{ $f->nome }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('fornecedor_id')" class="mt-1" />
                        </div>

                        @if($transacao->parcela_total)
                            <div class="md:col-span-2">
                                <x-input-label value="Parcelas" />
                                <div class="h-10 flex items-center px-3 rounded-lg border bg-gray-50 text-gray-600">
                                    {{ $transacao->parcela_num }}/{{ $transacao->parcela_total }} — agrupamento: {{ $transacao->grupo_uuid }}
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Este lançamento faz parte de um parcelamento e não será redividido.</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('transacoes.index') }}"><x-secondary-button>Cancelar</x-secondary-button></a>
                        <x-primary-button>Atualizar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
