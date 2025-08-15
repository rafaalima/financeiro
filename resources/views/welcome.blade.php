<!-- welcome v2 -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meu Financeiro</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="antialiased min-h-screen bg-slate-100 flex items-center justify-center px-4">
    <!-- Card central robusto -->
    <main class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- ESQUERDA: call-to-action para login (altura garantida) -->
            <section class="bg-gradient-to-br from-indigo-600 to-indigo-500 text-white p-10
                            flex flex-col items-center justify-center text-center min-h-[360px]">
                <h2 class="text-3xl font-extrabold">Bem-vindo!</h2>
                <p class="mt-2 text-indigo-100 max-w-sm">
                    Gerencie suas receitas, despesas e tenha controle total do seu dinheiro.
                </p>

                <div class="mt-8">
                    @if (Route::has('login'))

                    @endif
                </div>
            </section>

            <!-- DIREITA: título/descrição/CTAs (mesmas infos do seu original) -->
            <section class="p-10 flex flex-col justify-center min-h-[360px]">
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900">💰 Meu Financeiro</h1>
                <p class="mt-3 text-slate-600">
                    Gerencie suas receitas, despesas e tenha controle total do seu dinheiro.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    @if (Route::has('login'))
                    @auth
                    <a href="{{ url('/dashboard') }}">
                        <x-primary-button class="text-base py-3">Ir para Dashboard</x-primary-button>
                    </a>
                    @else
                    <a href="{{ route('login') }}">
                        <x-primary-button class="text-base py-3">Entrar</x-primary-button>
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}">
                        <x-secondary-button class="text-base py-3">Criar Conta</x-secondary-button>
                    </a>
                    @endif
                    @endauth
                    @endif
                </div>
            </section>
        </div>

        <footer class="px-8 py-5 bg-slate-50 border-t border-slate-200 text-xs text-slate-500 text-center">
            © {{ date('Y') }} Meu Financeiro
        </footer>
    </main>
</body>

</html>