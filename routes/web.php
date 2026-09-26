<?php

use App\Enums\Role;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CasoExitoController;
use App\Http\Controllers\LinkClickController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Models\Link;
use App\Models\User;
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
        Route::livewire('dashboard/users', 'pages::users.index')->name('users.index');
        Route::livewire('dashboard/contactos', 'pages::inquiries.index')->name('inquiries.index');
    });
});

Route::get('/', function () {
    return view('home');
})->name('home');

Route::livewire('ideas', 'pages::ideas.public')->name('ideas.public');

Route::livewire('contacto', 'pages::contact.index')->name('contact');

Route::get('/nosotros', function () {
    $team = User::whereIn('role', [Role::Admin, Role::Author])
        ->where('is_active', true)
        ->get();

    return view('about', compact('team'));
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

Route::get('/portafolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portafolio/{project:slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

Route::get('/casos-exito', [CasoExitoController::class, 'index'])->name('casos-exito.index');
Route::get('/casos-exito/{proyecto}', [CasoExitoController::class, 'show'])
    ->where('proyecto', '[a-z0-9-]+')
    ->name('casos-exito.show');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// El comodín del blog va al final: cualquier ruta propia se registra antes,
// o queda escondida detrás de él.
Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');

require __DIR__.'/settings.php';
