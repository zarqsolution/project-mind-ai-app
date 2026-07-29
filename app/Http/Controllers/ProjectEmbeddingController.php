<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateProjectEmbedding;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ProjectEmbeddingController extends Controller
{
    /**
     * Queue embedding generation for the given project.
     */
    public function __invoke(Project $project): RedirectResponse
    {
        GenerateProjectEmbedding::dispatch($project);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Embedding generation started.')]);

        return back();
    }
}
