<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Esquerda: Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
            </div>

            {{-- Centro: Links (desktop) --}}
            <div class="hidden sm:flex sm:items-center">
                <nav class="flex items-center gap-6">

                    <a href="{{ route('dashboard') }}"
                        class="px-1 pt-1 text-sm font-medium border-b-2
              {{ request()->routeIs('dashboard') ? 'text-indigo-600 border-indigo-600' : 'text-gray-600 hover:text-gray-800 hover:border-gray-300 border-transparent' }}">
                        📊 Meu Financeiro
                    </a>

                    {{-- Cadastros (dropdown) --}}
                    <div x-data="{ open:false }" class="relative">
                        <button @click="open = !open"
                            class="px-1 pt-1 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 inline-flex items-center gap-1">
                            🗂 Cadastros
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 111.08 1.04l-4.25 4.39a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" />
                            </svg>
                        </button>
                        <div x-cloak x-show="open" @click.outside="open=false"
                            class="absolute left-0 mt-2 w-56 bg-white shadow-lg rounded-lg border border-gray-100 py-2 z-50">
                            <a href="{{ route('categorias.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Categorias</a>
                            <a href="{{ route('fornecedores.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Fornecedores</a>
                            <a href="{{ route('bancos.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Bancos</a>
                        </div>
                    </div>

                    <a href="{{ route('transacoes.index') }}"
                        class="px-1 pt-1 text-sm font-medium border-b-2
              {{ request()->routeIs('transacoes.*') ? 'text-indigo-600 border-indigo-600' : 'text-gray-600 hover:text-gray-800 hover:border-gray-300 border-transparent' }}">
                        💸 Transações
                    </a>

                    {{-- ... outros itens ... --}}

                    <a href="{{ route('relatorios.index') }}"
                        class="px-1 pt-1 text-sm font-medium border-b-2
      {{ request()->routeIs('relatorios.*')
           ? 'text-indigo-600 border-indigo-600'
           : 'text-gray-600 hover:text-gray-800 hover:border-gray-300 border-transparent' }}">
                        📈 Relatórios
                    </a>

                </nav>
            </div>

            <!-- Direita: Usuário (desktop) -->
            <div class="hidden sm:flex sm:items-center sm:space-x-3">
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.19l3.71-3.96a.75.75 0 111.08 1.04l-4.24 4.53a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- Exemplo de link extra (opcional) --}}
                        <x-dropdown-link :href="route('profile.edit')">
                            👤 Perfil
                        </x-dropdown-link>
                        @if (auth()->user()->is_admin)

                        <x-dropdown-link href="{{ route('admin.users.create') }}">
                            👤 Cadastrar usuário
                        </x-dropdown-link>
                        <div class="border-t my-1"></div>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Sair
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @endauth

                @guest
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-800">Entrar</a>
                @endguest
            </div>

            <!-- Botão mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-800 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu mobile --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium
            {{ request()->routeIs('dashboard') ? 'text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">📊 Meu Financeiro</a>

            <div x-data="{ cad:false }" class="px-2">
                <button @click="cad = !cad" class="w-full text-left px-2 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 flex items-center justify-between">
                    🗂 Cadastros
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 111.08 1.04l-4.25 4.39a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" />
                    </svg>
                </button>
                <div x-show="cad" x-cloak class="ml-4 mt-1 space-y-1">
                    <a href="{{ route('categorias.index') }}" class="block px-2 py-1 text-sm text-gray-700 hover:bg-gray-50">Categorias</a>
                    <a href="{{ route('fornecedores.index') }}" class="block px-2 py-1 text-sm text-gray-700 hover:bg-gray-50">Fornecedores</a>
                    <a href="{{ route('bancos.index') }}" class="block px-2 py-1 text-sm text-gray-700 hover:bg-gray-50">Bancos</a>
                </div>
            </div>

            <a href="{{ route('transacoes.index') }}" class="block px-4 py-2 text-base font-medium
            {{ request()->routeIs('transacoes.*') ? 'text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">💸 Transações</a>

            <a href="{{ route('relatorios.index') }}" class="block px-4 py-2 text-base font-medium
{{ request()->routeIs('relatorios.*') ? 'text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">📈 Relatórios</a>



        </div>

        {{-- ... (seu bloco de perfil/logout) --}}
    </div>

</nav>