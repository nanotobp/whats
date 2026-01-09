<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\ContactUpload;
use App\Livewire\CampaignCreator;
use App\Livewire\CampaignHistory;
use App\Livewire\GroupManager;
use App\Livewire\CampaignMetrics;
use App\Livewire\Login;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\Authenticate;

// Public routes
Route::get('/login', Login::class)->name('login');

Route::post('/logout', function () {
    session()->flush();
    return redirect()->route('login');
})->name('logout');

// Protected routes
Route::middleware(Authenticate::class)->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/contacts', ContactUpload::class)->name('contacts');
    Route::get('/campaigns', CampaignCreator::class)->name('campaigns');
    Route::get('/campaigns/history', CampaignHistory::class)->name('campaigns.history');
    Route::get('/campaigns/{campaign}/metrics', CampaignMetrics::class)->name('campaign.metrics');
    Route::get('/groups', GroupManager::class)->name('groups');
});

// Webhook endpoint (without CSRF protection)
Route::post('/webhook/green-api', [WebhookController::class, 'greenApi'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
