<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrawlerController;
use App\Livewire\WebScraper;

Route::get('/', WebScraper::class);
Route::post('/crawl', [CrawlerController::class, 'handleCrawl']);

Route::view('dashboard', 'dashboard')
->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
