<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Editar banco</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-2xl p-6 space-y-6">
                <form method="POST" action="{{ route('bancos.update', $banco) }}">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Nome" />
                            <x-text-input name="nome" class="w-full"
                                value="{{ old('nome', $banco->nome) }}" />
                            <x-input-error :messages="$errors->get('nome')" />
                        </div>

                        <div>
                            <x-input-label value="Agência" />
                            <x-text-input name="agencia" class="w-full"
                                value="{{ old('agencia', $banco->agencia) }}" />
                            <x-input-error :messages="$errors->get('agencia')" />
                        </div>

                        <div>
                            <x-input-label value="Conta" />
                            <x-text-input name="conta" class="w-full"
                                value="{{ old('conta', $banco->conta) }}" />
                            <x-input-error :messages="$errors->get('conta')" />
                        </div>

                        <div>
                            <x-input-label value="Tipo" />
                            <x-text-input name="tipo" class="w-full"
                                value="{{ old('tipo', $banco->tipo) }}" />
                            <x-input-error :messages="$errors->get('tipo')" />
                        </div>

                        <div>
                            <x-input-label value="Saldo inicial" />
                            <x-text-input type="number" step="0.01" name="saldo_inicial" class="w-full"
                                value="{{ old('saldo_inicial', $banco->saldo_inicial) }}" />
                            <x-input-error :messages="$errors->get('saldo_inicial')" />
                        </div>

                        <div>
                            <x-input-label value="Ativo" />
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="is_ativo" value="1"
                                       {{ old('is_ativo', $banco->is_ativo) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">Sim</span>
                            </label>
                            <x-input-error :messages="$errors->get('is_ativo')" />
                        </div>

                        {{-- SOMENTE LEITURA: Saldo atual (dinâmico) --}}
                        <div class="md:col-span-2">
                            <x-input-label value="Saldo atual (dinâmico)" />
                            <div class="h-10 flex items-center px-3 rounded-lg border bg-gray-50">
                                <span class="{{ ($banco->saldo_atual ?? 0) >= 0 ? 'text-sky-700' : 'text-rose-700' }}">
                                    R$ {{ number_format($banco->saldo_atual ?? 0, 2, ',', '.') }}
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Calculado: saldo inicial + receitas pagas − despesas pagas.
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label value="Observação" />
                            <textarea name="observacao" rows="3" class="w-full border rounded-lg p-2">{{ old('observacao', $banco->observacao) }}</textarea>
                            <x-input-error :messages="$errors->get('observacao')" />
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <form method="POST" action="{{ route('bancos.reconciliar', $banco) }}"
                              onsubmit="return confirm('Atualizar saldo inicial para o saldo atual?');">
                            @csrf
                            <x-secondary-button type="submit">Usar saldo atual como saldo inicial</x-secondary-button>
                        </form>

                        <div class="flex gap-2">
                            <x-secondary-button onclick="history.back()">Cancelar</x-secondary-button>
                            <x-primary-button>Salvar</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
