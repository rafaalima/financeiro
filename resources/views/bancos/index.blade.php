<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Bancos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-2xl p-5 space-y-4">
                {{-- Filtro simples --}}
                <form method="GET" class="flex gap-2 items-end">
                    <div class="flex-1">
                        <x-input-label value="Buscar" />
                        <x-text-input name="q" value="{{ $q ?? '' }}" class="w-full"/>
                    </div>
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <div class="overflow-x-auto rounded-xl border">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="p-3 text-left">Banco</th>
                                <th class="p-3 text-left">Agência</th>
                                <th class="p-3 text-left">Conta</th>
                                <th class="p-3 text-right">Saldo inicial</th>
                                <th class="p-3 text-right">Saldo atual</th>
                                <th class="p-3 text-right w-48">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($bancos as $b)
                                @php $saldoPos = ($b->saldo_atual ?? 0) >= 0; @endphp
                                <tr>
                                    <td class="p-3">{{ $b->nome }}</td>
                                    <td class="p-3">{{ $b->agencia ?? '—' }}</td>
                                    <td class="p-3">{{ $b->conta ?? '—' }}</td>
                                    <td class="p-3 text-right">
                                        R$ {{ number_format($b->saldo_inicial ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right {{ $saldoPos ? 'text-sky-700' : 'text-rose-700' }}">
                                        R$ {{ number_format($b->saldo_atual ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right space-x-2">
                                        <a href="{{ route('bancos.edit', $b) }}">
                                            <x-secondary-button class="!py-1 !px-2 text-xs">Editar</x-secondary-button>
                                        </a>

                                        <form method="POST" action="{{ route('bancos.destroy', $b) }}" class="inline"
                                              onsubmit="return confirm('Excluir este banco?');">
                                            @csrf @method('DELETE')
                                            <x-danger-button class="!py-1 !px-2 text-xs">Excluir</x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-500">Nenhum banco encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- opcional: botão criar --}}
                <div class="flex justify-end">
                    <a href="{{ route('bancos.create') }}">
                        <x-primary-button>Novo banco</x-primary-button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
