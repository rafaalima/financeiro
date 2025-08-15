<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Cadastrar usuário</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-2xl p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nome" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="email" value="E-mail" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="password" value="Senha" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar senha" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        </div>
                    </div>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_admin" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Conceder acesso de administrador</span>
                    </label>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('dashboard') }}"><x-secondary-button>Cancelar</x-secondary-button></a>
                        <x-primary-button>Salvar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
