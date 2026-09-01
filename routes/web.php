<?php

use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvitationController as AdminInvitationController;
use App\Http\Controllers\Admin\TemplateController as AdminTemplateController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InvitationPreviewController;
use App\Http\Controllers\InvitationPublicationController;
use App\Http\Controllers\InvitationResponseController;
use App\Http\Controllers\PublicInvitationController;
use App\Http\Controllers\PublicRsvpController;
use App\Http\Controllers\PublicWishController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:login');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::view('/approval-pending', 'auth.approval-pending')->name('approval.pending');
});

Route::middleware(['auth', 'customer.active'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('invitations', InvitationController::class)->except(['index', 'show']);
    Route::get('/invitations/{invitation}/preview', InvitationPreviewController::class)->name('invitations.preview');
    Route::post('/invitations/{invitation}/publication', [InvitationPublicationController::class, 'store'])->name('invitations.publication.store');
    Route::delete('/invitations/{invitation}/publication', [InvitationPublicationController::class, 'destroy'])->name('invitations.publication.destroy');
    Route::get('/invitations/{invitation}/rsvps', [InvitationResponseController::class, 'rsvps'])->name('invitations.rsvps');
    Route::get('/invitations/{invitation}/wishes', [InvitationResponseController::class, 'wishes'])->name('invitations.wishes');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::patch('/customers/{customer}', [AdminCustomerController::class, 'update'])->name('customers.update');
    Route::get('/invitations', AdminInvitationController::class)->name('invitations.index');
    Route::get('/templates', AdminTemplateController::class)->name('templates.index');
});

Route::post('/{invitation:slug}/rsvp', [PublicRsvpController::class, 'store'])->middleware('throttle:guest-interaction')->name('public.rsvp');
Route::post('/{invitation:slug}/wishes', [PublicWishController::class, 'store'])->middleware('throttle:guest-interaction')->name('public.wishes');
Route::get('/{slug}', PublicInvitationController::class)->name('public.invitation');
