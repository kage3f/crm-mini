<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Dashboard\Index as Dashboard;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Clients\Show as ClientShow;
use App\Livewire\Opportunities\Board as KanbanBoard;
use App\Livewire\Tasks\Index as TasksIndex;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Security;
use App\Livewire\Settings\Company;
use App\Livewire\Settings\Team;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth routes (no middleware)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// Logout
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Email verification
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', VerifyEmail::class)->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email verificado com sucesso!');
    })->middleware('signed')->name('verification.verify');
    Route::post('/email/resend', function () {
        request()->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Email de verificação reenviado!');
    })->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Invitation acceptance (no auth required)
|--------------------------------------------------------------------------
*/
Route::get('/invitations/{token}/accept', [InvitationController::class, 'accept'])
    ->name('invitations.accept');
Route::post('/invitations/{token}/accept', [InvitationController::class, 'store'])
    ->name('invitations.store');

/*
|--------------------------------------------------------------------------
| App routes (protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Clients
    Route::get('/clients', ClientsIndex::class)->name('clients.index');
    Route::get('/clients/{id}', ClientShow::class)->name('clients.show');

    // Opportunities (Kanban)
    Route::get('/opportunities', KanbanBoard::class)->name('opportunities.index');

    // Tasks
    Route::get('/tasks', TasksIndex::class)->name('tasks.index');


    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/security', Security::class)->name('security');
        Route::get('/company', Company::class)->name('company');
        Route::get('/team', Team::class)->name('team');
    });
});
