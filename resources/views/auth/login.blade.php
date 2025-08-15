<x-guest-layout>
    <!-- Sobe o card -->
     <div class="min-h-screen bg-slate-100 flex items-center justify-center px-4">
        <!-- Card sem borda/outline -->
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-5">

                <!-- Painel lateral -->
                <div class="hidden lg:flex col-span-2 bg-gradient-to-br from-indigo-600 to-indigo-500 text-white p-8">
                    <div class="flex flex-col justify-between w-full">
                        <div class="space-y-4">
                            <div class="font-semibold text-lg">Meu Financeiro</div>
                            <p class="text-indigo-100/90">Centralize suas categorias e transações.</p>
                        </div>
                        <ul class="mt-8 space-y-3 text-indigo-100/95">
                            <li class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10">✔</span>
                                Despesas e receitas
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10">✔</span>
                                Categorias
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10">✔</span>
                                Relatórios
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Formulário -->
                <div class="col-span-3 p-8">
                    <h1 class="text-2xl font-bold text-gray-900">🔐 Acessar conta</h1>
                    <p class="text-sm text-gray-500 mt-1 mb-6">Entre para continuar</p>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    {{-- Alerta global de erro (credenciais inválidas) --}}
                    @if ($errors->has('email') || $errors->has('password'))
                        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                            E-mail e/ou senha inválidos. Tente novamente.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6 outline-none focus:outline-none"
                          x-data="{ loading: false }" @submit="loading = true">
                        @csrf

                        <!-- Email -->
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
                                    class="mt-1 block w-full pl-10 text-base py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autocomplete="username"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Senha -->
                        <div x-data="{ show: false }">
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
                                    x-bind:type="show ? 'text' : 'password'"
                                    class="mt-1 block w-full pl-10 pr-10 text-base py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    name="password"
                                    required
                                    autocomplete="current-password"
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

                        <!-- Lembrar + link -->
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <label class="inline-flex items-center gap-2 whitespace-nowrap">
                                <input id="remember_me" type="checkbox"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                       name="remember">
                                <span class="text-sm text-gray-600">Lembrar-me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-sm text-indigo-600 hover:text-indigo-700 font-medium focus:outline-none whitespace-nowrap"
                                   href="{{ route('password.request') }}">
                                    Esqueceu a senha?
                                </a>
                            @endif
                        </div>

                        <!-- Botão maior com loading -->
                        <x-primary-button
                            class="w-full justify-center text-base py-3 tracking-wide"
                            x-bind:class="loading ? 'opacity-60 cursor-not-allowed' : ''"
                            x-bind:disabled="loading">
                            <span x-show="!loading">Entrar</span>
                            <span x-show="loading">Entrando…</span>
                        </x-primary-button>
                    </form>

                    
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
