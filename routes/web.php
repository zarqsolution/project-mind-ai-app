<?php

use App\Http\Controllers\AISearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectEmbeddingController;
use App\Http\Controllers\ProjectImageController;
use App\Http\Controllers\PromptHistoryController;
use App\Http\Controllers\PromptTemplateController;
use App\Http\Controllers\ProposalController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/images', [ProjectImageController::class, 'store'])->name('projects.images.store');
    Route::put('projects/{project}/images/reorder', [ProjectImageController::class, 'reorder'])->name('projects.images.reorder');
    Route::delete('projects/{project}/images/{image}', [ProjectImageController::class, 'destroy'])->scopeBindings()->name('projects.images.destroy');
    Route::post('projects/{project}/embedding', ProjectEmbeddingController::class)->name('projects.embedding.generate');
    Route::get('ai-search', [AISearchController::class, 'index'])->name('ai-search.index');
    Route::get('proposal-writer', [ProposalController::class, 'index'])->name('proposal-writer.index');
    Route::post('proposal-writer', [ProposalController::class, 'store'])->name('proposal-writer.store');
    Route::resource('prompt-templates', PromptTemplateController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('history', [PromptHistoryController::class, 'index'])->name('history.index');
    Route::delete('history/{history}', [PromptHistoryController::class, 'destroy'])->name('history.destroy');
});

require __DIR__.'/settings.php';
