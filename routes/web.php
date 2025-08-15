<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TransacaoController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\BancoController;
use App\Http\Controllers\ReportController;


/*
|--------------------------------------------------------------------------
| Home: se logado → dashboard; senão → login
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rotas de autenticação (Breeze/Fortify)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rotas protegidas (usuário autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categorias', CategoriaController::class);
    Route::resource('transacoes', \App\Http\Controllers\TransacaoController::class)
    ->parameters(['transacoes' => 'transacao']); 
});

/*
|--------------------------------------------------------------------------
| Rotas somente para ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/usuarios/novo', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/usuarios', [UserManagementController::class, 'store'])->name('users.store');
    });



    /*
|--------------------------------------------------------------------------
| Home: Graficos
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
->middleware(['auth','verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('fornecedores', FornecedorController::class)
        ->parameters(['fornecedores' => 'fornecedor']);  

    Route::resource('bancos', BancoController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/relatorios', [ReportController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/export', [ReportController::class, 'exportCsv'])->name('relatorios.export');
    Route::get('/relatorios/pdf', [ReportController::class, 'exportPdf'])->name('relatorios.pdf'); 
});