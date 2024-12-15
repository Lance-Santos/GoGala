<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Verify;
use App\Livewire\Pages\Events;
use App\Livewire\Auth\Register;
use App\Livewire\Pages\Profile;
use App\Livewire\Pages\Checkout;
use App\Livewire\Pages\Attending;
use App\Livewire\Pages\EventView;
use App\Livewire\Pages\Favorites;
use App\Livewire\Pages\ViewEvent;
use App\Livewire\Pages\CreateEvent;
use App\Livewire\Pages\EventProfile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\Editor;
use App\Livewire\Auth\Passwords\Email;
use App\Livewire\Auth\Passwords\Reset;
use App\Livewire\Verify\UserInvitation;
use App\Livewire\Auth\Passwords\Confirm;
use App\Livewire\Pages\CreateOrganization;
use App\Livewire\Pages\OrganizationProfile;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Webhook\PayMongoWebhookController;
use App\Livewire\Pages\PurchaseHistory;

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

Route::post('/webhooks/paymongo', [PayMongoWebhookController::class, 'handle']);



Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)
        ->name('login');

    Route::get('register', Register::class)
        ->name('register');
});

Route::get('password/reset', Email::class)
    ->name('password.request');

Route::get('password/reset/{token}', Reset::class)
    ->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::get('email/verify', Verify::class)
        ->middleware('throttle:6,1')
        ->name('verification.notice');
    Route::get('password/confirm', action: Confirm::class)
        ->name('password.confirm');
    Route::get('/invitations/accept/{token}', UserInvitation::class);
});
// Event related routes
// This route group will be exclusive for events ONLY
// Must run through auth
Route::middleware('auth')->group(function(){
    Route::get('events',Events::class)->name('events');
    Route::get('events/{eventSlug}', ViewEvent::class)->name('event.view');
    Route::get('/checkout',Checkout::class)->name('checkout');
    Route::get('/attending', Attending::class)->name('attending');
    Route::get('/transactions',PurchaseHistory::class)->name('purchases');
});


Route::middleware('auth')->group(function () {
    Route::get('email/verify/{id}/{hash}', EmailVerificationController::class)->middleware('signed')->name('verification.verify');
    Route::post('logout', LogoutController::class)->name('logout');
    Route::get('profile/',Profile::class)->name('profile');
    Route::get('create-organization/', CreateOrganization::class)->name('create-organization');
    Route::get('organization/{organization_slug}', OrganizationProfile::class)->name('organization-profile');
    Route::get('organization/{organization_slug}/{event_slug}', EventProfile::class)->name('event-profile');
    Route::get('/organization/{organization_slug}/{event_slug}/editor',[Editor::class,'show'])->name('editor');
    Route::post('/{organization}/{event}/save-layout', [Editor::class, 'store'])->name('store-layout');
    Route::get('/favorites', Favorites::class)->name('favorites');
});

