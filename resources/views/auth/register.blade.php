<x-guest-layout>
    <div class="min-h-screen bg-slate-100 flex items-center justify-center px-4">
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-5">

                {{-- Painel esquerdo (branding) --}}
                <aside class="hidden lg:flex col-span-2 bg-gradient-to-br from-indigo-600 to-indigo-500 text-white p-8">
                    <div class="flex flex-col justify-between w-full">
                        <div class="space-y-3">
                            <div class="font-semibold text-lg">Meu Financeiro</div>
                            <p class="text-indigo-100/90">Crie sua conta para começar a organizar receitas e despesas.</p>
                        </div>
                        <ul class="mt-8 space-y-3 text-indigo-100/95">
                            <li class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10">✔</span>
                                Categorias personalizadas
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10">✔</span>
                                Controle de receitas e despesas
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10">✔</span>
                                Relatórios (em breve)
                            </li>
                        </ul>
                    </div>
                </aside>

                {{-- Formulário de registro --}}
                <section class="col-span-3 p-8">
                    <h1 class="text-2xl font-bold text-gray-900">📝 Criar conta</h1>
                    <p class="text-sm text-gray-500 mt-1 mb-6">Preencha seus dados para começar</p>

                    {{-- Erro global opcional --}}
                    @if ($errors->any())
                        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                            Verifique os campos abaixo e tente novamente.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="space-y-6" x-data="{ loading:false, show:false, show2:false }" @submit="loading = true">
                        @csrf

                        {{-- Nome --}}
                        <div>
                            <x-input-label for="name" :value="__('Nome')" />
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    {{-- ícone usuário --}}
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M4 20a8 8 0 0116 0" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </span>
                                <x-text-input
                                    id="name"
                                    name="name"
                                    type="text"
                                    class="mt-1 block w-full pl-10 text-base py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    :value="old('name')"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('E-mail')" />
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="1.5" />
                                        <path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </span>
                                <x-text-input
                                    id="email"
                                    name="email"
                                    type="email"
                                    class="mt-1 block w-full pl-10 text-base py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    :value="old('email')"
                                    required
                                    autocomplete="username"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Senha --}}
                        <div>
                            <x-input-label for="password" :value="__('Senha')" />
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 10V7a5 5 0 0110 0v3" stroke="currentColor" stroke-width="1.5"/>
                                        <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </span>
                                <x-text-input
                                    id="password"
                                    name="password"
                                    x-bind:type="show ? 'text' : 'password'"
                                    class="mt-1 block w-full pl-10 pr-10 text-base py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                    autocomplete="new-password"
                                />
                                <button type="button"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                    @click="show = !show"
                                    :aria-label="show ? 'Ocultar senha' : 'Mostrar senha'">
                                    <svg x-show="!show" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                    <svg x-show="show" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M10.6 10.6A3 3 0 0012 15a3 3 0 002.4-4.4M6.7 6.7C4.3 8.2 2.9 10 2 12c0 0 4 7 10 7 2 0 3.8-.6 5.3-1.6M17.3 6.7A10.9 10.9 0 002 12" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        {{-- Confirmar senha --}}
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 10V7a5 5 0 0110 0v3" stroke="currentColor" stroke-width="1.5"/>
                                        <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </span>
                                <x-text-input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    x-bind:type="show2 ? 'text' : 'password'"
                                    class="mt-1 block w-full pl-10 pr-10 text-base py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                    autocomplete="new-password"
                                />
                                <button type="button"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                    @click="show2 = !show2"
                                    :aria-label="show2 ? 'Ocultar senha' : 'Mostrar senha'">
                                    <svg x-show="!show2" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                    <svg x-show="show2" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M10.6 10.6A3 3 0 0012 15a3 3 0 002.4-4.4M6.7 6.7C4.3 8.2 2.9 10 2 12c0 0 4 7 10 7 2 0 3.8-.6 5.3-1.6M17.3 6.7A10.9 10.9 0 002 12" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        {{-- Ações --}}
                        <x-primary-button
                            class="w-full justify-center text-base py-3 tracking-wide"
                            x-bind:class="loading ? 'opacity-60 cursor-not-allowed' : ''"
                            x-bind:disabled="loading">
                            <span x-show="!loading">Registrar</span>
                            <span x-show="loading">Registrando…</span>
                        </x-primary-button>
                    </form>

                    <p class="mt-6 text-center text-sm text-gray-600">
                        Já tem conta?
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-700 focus:outline-none">
                            Entrar
                        </a>
                    </p>
                </section>
            </div>
        </div>
    </div>
</x-guest-layout>
