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

                    @include('transacoes._form', ['transacao' => $transacao])

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('transacoes.index') }}"><x-secondary-button>Cancelar</x-secondary-button></a>
                        <x-primary-button>Atualizar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

