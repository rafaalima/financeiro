<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Categorias
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6">
                <div class="mb-4">
                    <a href="{{ route('categorias.create') }}">
                        <x-primary-button>
                            ➕ Nova Categoria
                        </x-primary-button>
                    </a>
                </div>

                @if(session('success'))
                <div class="bg-green-50 text-green-700 p-3 rounded mb-4 border border-green-200">
                    {{ session('success') }}
                </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="p-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($categorias as $categoria)
                            <tr class="{{ $loop->odd ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                                <td class="p-3 text-sm text-gray-900">{{ $categoria->nome }}</td>
                                <td class="p-3 text-sm text-gray-700 capitalize">{{ $categoria->tipo }}</td>
                                <td class="p-3">
                                    <div class="flex justify-center items-center gap-3 whitespace-nowrap">
                                        <a href="{{ route('categorias.edit', $categoria) }}">
                                            <x-secondary-button>Editar</x-secondary-button>
                                        </a>
                                        <form method="POST" action="{{ route('categorias.destroy', $categoria) }}"
                                            onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button>Excluir</x-danger-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-6 text-center text-gray-500">
                                    Nenhuma categoria cadastrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                @if(method_exists($categorias, 'links'))
                <div class="mt-4">
                    {{ $categorias->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>