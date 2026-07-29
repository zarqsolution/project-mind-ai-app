<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::inertia('projects', 'projects/index')->name('projects.index');
    Route::inertia('ai-search', 'ai-search/index')->name('ai-search.index');
    Route::inertia('proposal-writer', 'proposal-writer/index')->name('proposal-writer.index');
    Route::inertia('prompt-templates', 'prompt-templates/index')->name('prompt-templates.index');
    Route::inertia('history', 'history/index')->name('history.index');
});

require __DIR__.'/settings.php';
