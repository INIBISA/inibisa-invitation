<?php

use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DemoInvitationController as AdminDemoInvitationController;
use App\Http\Controllers\Admin\InvitationController as AdminInvitationController;
use App\Http\Controllers\Admin\RsvpController as AdminRsvpController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TemplateController as AdminTemplateController;
use App\Http\Controllers\Admin\WeddingMusicController as AdminWeddingMusicController;
use App\Http\Controllers\Admin\WishController as AdminWishController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\CustomerInvitationSectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InvitationPreviewController;
use App\Http\Controllers\InvitationPublicationController;
use App\Http\Controllers\InvitationResponseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PublicInvitationController;
use App\Http\Controllers\PublicRsvpController;
use App\Http\Controllers\PublicWishController;
use App\Http\Controllers\TemplateBrowserController;
use App\Http\Controllers\YouTubeMusicSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:login');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
    Route::get('/auth/google/confirm', [GoogleAuthController::class, 'confirm'])->name('google.confirm');
    Route::post('/auth/google/confirm', [GoogleAuthController::class, 'storeConfirm'])->name('google.confirm.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::prefix('customer')->name('customer.')->group(function (): void {
        Route::get('/guests', [CustomerInvitationSectionController::class, 'guests'])->name('guests.index');
        Route::get('/rsvps', [CustomerInvitationSectionController::class, 'rsvps'])->name('rsvps.index');
        Route::get('/wishes', [CustomerInvitationSectionController::class, 'wishes'])->name('wishes.index');
    });
    Route::get('/templates', [TemplateBrowserController::class, 'index'])->name('templates.index');
    Route::get('/youtube/music/search', YouTubeMusicSearchController::class)->middleware('throttle:youtube-search')->name('youtube.music.search');
    Route::get('/templates/{template}', [TemplateBrowserController::class, 'show'])->name('templates.show');
    Route::get('/checkout/{template}', [PaymentController::class, 'create'])->name('payments.create');
    Route::resource('payments', PaymentController::class)->only(['index', 'store', 'show']);
    Route::post('/payments/{payment}/proof', [PaymentController::class, 'uploadProof'])->name('payments.proof.store');
    Route::get('/payments/{payment}/proof', [PaymentController::class, 'proof'])->name('payments.proof.show');
    Route::resource('invitations', InvitationController::class)->except(['index', 'show']);
    Route::get('/invitations/{invitation}/preview', InvitationPreviewController::class)->name('invitations.preview');
    Route::post('/invitations/{invitation}/publication', [InvitationPublicationController::class, 'store'])->name('invitations.publication.store');
    Route::delete('/invitations/{invitation}/publication', [InvitationPublicationController::class, 'destroy'])->name('invitations.publication.destroy');
    Route::get('/invitations/{invitation}/rsvps', [InvitationResponseController::class, 'rsvps'])->name('invitations.rsvps');
    Route::get('/invitations/{invitation}/wishes', [InvitationResponseController::class, 'wishes'])->name('invitations.wishes');
    Route::get('/invitations/{invitation}/guests', [GuestController::class, 'index'])->name('invitations.guests.index');
    Route::post('/invitations/{invitation}/guests', [GuestController::class, 'store'])->name('invitations.guests.store');
    Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
    Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('/invitations', AdminInvitationController::class)->name('invitations.index');
    Route::get('/templates', AdminTemplateController::class)->name('templates.index');
    Route::patch('/templates/{template}', [AdminTemplateController::class, 'update'])->name('templates.update');
    Route::get('/demos/{demo}/edit', [AdminDemoInvitationController::class, 'edit'])->name('demos.edit');
    Route::put('/demos/{demo}', [AdminDemoInvitationController::class, 'update'])->name('demos.update');
    Route::resource('music', AdminWeddingMusicController::class)->except(['show', 'create', 'edit']);
    Route::get('/rsvps', AdminRsvpController::class)->name('rsvps.index');
    Route::get('/wishes', AdminWishController::class)->name('wishes.index');
    Route::get('/guests', App\Http\Controllers\Admin\GuestController::class)->name('guests.index');
    Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::patch('/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'update'])->name('payments.update');
});

Route::view('/syarat-ketentuan', 'legal.terms')->name('terms');
Route::view('/kebijakan-privasi', 'legal.privacy')->name('privacy');

Route::post('/payments/midtrans/webhook', PaymentWebhookController::class)->withoutMiddleware('web')->name('payments.midtrans.webhook');
Route::post('/{invitation:slug}/rsvp', [PublicRsvpController::class, 'store'])->middleware('throttle:guest-interaction')->name('public.rsvp');
Route::post('/{invitation:slug}/wishes', [PublicWishController::class, 'store'])->middleware('throttle:guest-interaction')->name('public.wishes');
Route::get('/{slug}', PublicInvitationController::class)->name('public.invitation');
