<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectImageController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/images', [ProjectImageController::class, 'store'])->name('projects.images.store');
    Route::put('projects/{project}/images/reorder', [ProjectImageController::class, 'reorder'])->name('projects.images.reorder');
    Route::delete('projects/{project}/images/{image}', [ProjectImageController::class, 'destroy'])->scopeBindings()->name('projects.images.destroy');
    Route::inertia('ai-search', 'ai-search/index')->name('ai-search.index');
    Route::inertia('proposal-writer', 'proposal-writer/index')->name('proposal-writer.index');
    Route::inertia('prompt-templates', 'prompt-templates/index')->name('prompt-templates.index');
    Route::inertia('history', 'history/index')->name('history.index');
});

require __DIR__.'/settings.php';
