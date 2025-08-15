<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Bancos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6">
                <div class="mb-4 flex items-center justify-between">
                    <a href="{{ route('bancos.create') }}">
                        <x-primary-button>➕ Novo banco</x-primary-button>
                    </a>

                    @if(session('success'))
                        <div class="ml-4 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Agência/Conta</th>
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="p-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Inicial</th>
                                <th class="p-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="p-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($bancos as $banco)
                                <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                    <td class="p-3 text-sm text-gray-900">{{ $banco->nome }}</td>
                                    <td class="p-3 text-sm text-gray-700">{{ $banco->agencia }} / {{ $banco->conta }}</td>
                                    <td class="p-3 text-sm text-gray-700 capitalize">{{ $banco->tipo }}</td>
                                    <td class="p-3 text-sm text-right text-gray-700">R$ {{ number_format($banco->saldo_inicial, 2, ',', '.') }}</td>
                                    <td class="p-3 text-center">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                            {{ $banco->is_ativo ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $banco->is_ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex justify-center items-center gap-2">
                                            <a href="{{ route('bancos.edit', $banco) }}">
                                                <x-secondary-button>Editar</x-secondary-button>
                                            </a>
                                            <form method="POST" action="{{ route('bancos.destroy', $banco) }}"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este banco?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button>Excluir</x-danger-button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-500">Nenhum banco cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $bancos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
