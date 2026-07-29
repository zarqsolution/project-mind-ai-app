<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateProjectEmbedding;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ProjectEmbeddingController extends Controller
{
    /**
     * Generate the embedding for the given project immediately.
     */
    public function __invoke(Project $project): RedirectResponse
    {
        GenerateProjectEmbedding::dispatchSync($project);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Embedding generated.')]);

        return back();
    }
}
