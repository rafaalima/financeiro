<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Meu perfil</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2">
                {{-- Dados da conta --}}
                <div class="bg-white shadow-lg rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Dados da conta</h3>
                    <p class="text-sm text-gray-500 mb-4">Atualize seu nome, e-mail e preferências.</p>

                    @if (session('status') === 'profile-updated')
                        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-green-700">
                            Perfil atualizado com sucesso.
                        </div>
                    @endif

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-4" x-data="{ loading:false }" @submit="loading = true">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="name" value="Nome" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          :value="old('name', auth()->user()->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                        </div>

                        <div>
                            <x-input-label for="email" value="E-mail" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                          :value="old('email', auth()->user()->email)" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                        </div>

                        @if(auth()->user()->is_admin)
                            <div class="pt-2">
                                <x-input-label value="Permissões" />
                                {{-- Toggle de admin (só admins veem) --}}
                                <label class="mt-2 flex items-center justify-between gap-4">
                                    <span class="text-sm text-gray-700">É administrador?</span>
                                    <input type="checkbox" name="is_admin" value="1" class="sr-only peer"
                                           @checked(old('is_admin', auth()->user()->is_admin))>
                                    <span class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-indigo-600 relative transition">
                                        <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow
                                                     transition peer-checked:translate-x-5"></span>
                                    </span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Somente administradores podem alterar isto.</p>
                                <x-input-error :messages="$errors->get('is_admin')" class="mt-2"/>
                            </div>
                        @endif

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <x-secondary-button type="button" onclick="window.location='{{ route('dashboard') }}'">Cancelar</x-secondary-button>
                            <x-primary-button x-bind:class="loading ? 'opacity-60 cursor-not-allowed' : ''"
                                              x-bind:disabled="loading">
                                <span x-show="!loading">Salvar</span>
                                <span x-show="loading">Salvando…</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                {{-- Segurança (mantém Partials do Breeze) --}}
                <div class="space-y-6">
                    <div class="bg-white shadow-lg rounded-2xl p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                    <div class="bg-white shadow-lg rounded-2xl p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
