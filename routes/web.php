<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\CopyController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Login', [
        // 'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas para gerenciamento dos usuários da bibloteca
    Route::prefix('users-library')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.library');
        Route::get('/all', [UserController::class, 'listAll']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
    Route::get('/check-cpf/{cpf}', [UserController::class, 'checkCPF']);

    // Rotas para gerenciamento dos livros
    Route::prefix('books')->group(function () {
        Route::get('/', [BookController::class, 'index'])->name('books.index');
        Route::post('/', [BookController::class, 'store'])->name('books.store');
        Route::get('/{id}', [BookController::class, 'show'])->name('books.show');
        Route::put('/{id}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/{id}', [BookController::class, 'destroy'])->name('books.destroy');
    });
    // Rotas para gerenciamento das copias de livros
    Route::get('/available-copies', [CopyController::class, 'available']);
    Route::get('/books/{book}/copies', [CopyController::class, 'index']);
    Route::post('/books/{book}/copies', [CopyController::class, 'store'])->name('books.copies.store');
    Route::delete('/copies/{copy}', [CopyController::class, 'destroy'])->name('copies.destroy');

    // Rotas para gerenciamento dos emprestimos
    Route::prefix('loans')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('loans.index');
        Route::post('/', [LoanController::class, 'store'])->name('loans.store');
        Route::get('/{id}', [LoanController::class, 'show'])->name('loans.show');
        Route::put('/{id}', [LoanController::class, 'update'])->name('loans.update');
        Route::put('/{id}/return', [LoanController::class, 'returnLoan'])->name('loans.return');
        Route::delete('/{id}', [LoanController::class, 'destroy'])->name('loans.destroy');
    });
});
require __DIR__.'/auth.php';
