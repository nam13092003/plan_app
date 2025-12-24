<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OidcAuthController;

// Redirect root to SPA entry
Route::redirect('/', '/app')->name('home');

// Validate an account
Route::get('/validate-account/{user:creation_token}', function (User $user) {
    return view('validate-account', compact('user'));
})
    ->name('validate-account')
    ->middleware('web');

// Login default redirection
Route::redirect('/login-redirect', '/login')->name('login');

Route::name('oidc.')
    ->prefix('oidc')
    ->group(function () {
        Route::get('redirect', [OidcAuthController::class, 'redirect'])->name('redirect');
        Route::get('callback', [OidcAuthController::class, 'callback'])->name('callback');
    });

// Vue App Route - Phải đặt cuối cùng để không conflict với các routes khác
Route::view('/app/{any?}', 'app')
    ->where('any', '.*')
    ->name('app');