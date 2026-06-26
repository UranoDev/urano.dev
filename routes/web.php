<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\LinkClickController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\ServiceController;use App\Models\Link;
use Illuminate\Support\Facades\Route;

Route::prefix('auth/google')->name('auth.google.')->group(function () {
    Route::get('redirect', [OAuthController::class, 'redirectToGoogle'])->name('redirect');
    Route::get('callback', [OAuthController::class, 'handleGoogleCallback'])->name('callback');
});

Route::middleware(['auth', 'verified', 'dashboard.access'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('dashboard/posts', 'pages::posts.index')->name('posts.index');
    Route::livewire('dashboard/posts/create', 'pages::posts.form')->name('posts.create');
    Route::livewire('dashboard/posts/{post}/edit', 'pages::posts.form')->name('posts.edit');

    Route::middleware(['admin'])->group(function () {
        Route::livewire('dashboard/ideas', 'pages::ideas.index')->name('ideas.index');
        Route::livewire('dashboard/links', 'pages::links.index')->name('links.index');
        Route::livewire('dashboard/users', 'pages::users.index')->name('users.index');    });
});

Route::get('/', function () {
    return view('home');
})->name('home');

Route::livewire('ideas', 'pages::ideas.public')->name('ideas.public');

Route::get('/nosotros', function () {
    return view('about');
})->name('nosotros');
Route::get('/pricing', function () {
    return view('pricing');
});

Route::get('/links', function () {
    $links = Link::where('is_active', true)->with('owner')->orderBy('sort_order')->orderBy('id')->get();
    return view('links', compact('links'));
})->name('links.public');

Route::get('/links/{link}/click', [LinkClickController::class, 'click'])->name('links.click');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

Route::get('/servicios', [ServiceController::class, 'index'])->name('services.index');
Route::get('/servicios/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');

require __DIR__.'/settings.php';
