<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\EmbeddingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateProjectEmbedding implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Project $project) {}

    /**
     * Execute the job.
     */
    public function handle(EmbeddingService $embeddings): void
    {
        $embeddings->generateForProject($this->project);
    }
}
