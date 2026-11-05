<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Default root loads the AI Chatbot directly
Route::get('/', [ChatController::class, 'index'])->name('home');

// Redirect /translate to /chat in case user opens old URL
Route::redirect('/translate', '/chat');

Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/c/{conversation}', [ChatController::class, 'index'])->name('conversation');

    Route::post('/upload', [ChatController::class, 'upload'])->name('upload');

    Route::get('/api/conversations', [ChatController::class, 'list'])->name('list');
    Route::get('/api/conversations/{conversation}', [ChatController::class, 'show'])->name('show');
    Route::post('/api/conversations', [ChatController::class, 'store'])->name('store');
    Route::patch('/api/conversations/{conversation}', [ChatController::class, 'update'])->name('update');
    Route::delete('/api/conversations/{conversation}', [ChatController::class, 'destroy'])->name('destroy');

    Route::post('/send', [ChatController::class, 'send'])->name('send');
    Route::post('/stream', [ChatController::class, 'stream'])->name('stream');
});
