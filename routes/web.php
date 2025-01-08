<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\WebScraper;


Route::get('/', WebScraper::class);

Route::get('/scraper', WebScraper::class)
    ->middleware(['auth'])
    ->name('web-scraper');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
