<?php

use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Message\LoadMessagesController;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\MessageStatus\UpdateMessageStatusController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::controller(ChatController::class)
        ->prefix('chats')
        ->name('chats.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{chat}', 'show')->name('show');
        });

    Route::controller(MessageController::class)
        ->prefix('messages')
        ->name('messages.')
        ->group(function () {
            Route::post('/', 'store')->name('store');
        });

    Route::post('/load/messages', LoadMessagesController::class)
        ->name('load.messages');

    Route::patch('/update/message/status', UpdateMessageStatusController::class)
        ->name('update.message.status');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
