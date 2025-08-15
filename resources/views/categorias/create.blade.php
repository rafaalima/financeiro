<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nova categoria
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6">

                {{-- mensagens flash opcionais --}}
                @if (session('success'))
                    <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('categorias.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Nome --}}
                    <div>
                        <x-input-label for="nome" value="Nome" />
                        <x-text-input
                            id="nome"
                            name="nome"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('nome')"
                            required
                            autofocus
                        />
                        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <x-input-label for="tipo" value="Tipo" />
                        <select
                            id="tipo"
                            name="tipo"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="" disabled {{ old('tipo') ? '' : 'selected' }}>Selecione…</option>
                            <option value="receita" @selected(old('tipo') === 'receita')>Receita</option>
                            <option value="despesa" @selected(old('tipo') === 'despesa')>Despesa</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                    </div>

                    {{-- Ações --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('categorias.index') }}">
                            <x-secondary-button type="button">Cancelar</x-secondary-button>
                        </a>
                        <x-primary-button>
                            Salvar
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
