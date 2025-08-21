<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nova transação</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6">
                <form method="POST" action="{{ route('transacoes.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Descrição" />
                            <x-text-input name="descricao" value="{{ old('descricao') }}" class="w-full" />
                            <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Valor (total)" />
                            <x-text-input type="number" step="0.01" min="0" name="valor" value="{{ old('valor') }}" class="w-full" />
                            <x-input-error :messages="$errors->get('valor')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Data (1ª parcela)" />
                            <x-text-input type="date" name="data" value="{{ old('data') }}" class="w-full" />
                            <x-input-error :messages="$errors->get('data')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Status" />
                            <select name="status" class="w-full h-10 border rounded-lg">
                                <option value="pendente" @selected(old('status','pendente')==='pendente')>Pendente</option>
                                <option value="pago" @selected(old('status')==='pago')>Pago</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Categoria" />
                            <select name="categoria_id" class="w-full h-10 border rounded-lg">
                                <option value="">Selecione…</option>
                                @foreach($categorias as $c)
                                    <option value="{{ $c->id }}" @selected(old('categoria_id')==$c->id)>{{ $c->nome }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('categoria_id')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Banco" />
                            <select name="banco_id" class="w-full h-10 border rounded-lg">
                                <option value="">—</option>
                                @foreach($bancos as $b)
                                    <option value="{{ $b->id }}" @selected(old('banco_id')==$b->id)>{{ $b->nome }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('banco_id')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Fornecedor" />
                            <select name="fornecedor_id" class="w-full h-10 border rounded-lg">
                                <option value="">—</option>
                                @foreach($fornecedores as $f)
                                    <option value="{{ $f->id }}" @selected(old('fornecedor_id')==$f->id)>{{ $f->nome }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('fornecedor_id')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label value="Parcelas" />
                            <x-text-input type="number" min="1" name="parcela_total" value="{{ old('parcela_total', 1) }}" class="w-full" />
                            <p class="text-xs text-gray-500 mt-1">Ex.: 2 para dividir em 2x. O valor será rateado automaticamente.</p>
                            <x-input-error :messages="$errors->get('parcela_total')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('transacoes.index') }}"><x-secondary-button>Cancelar</x-secondary-button></a>
                        <x-primary-button>Salvar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
