<?php

namespace App\Services;

use App\Models\Project;
use Laravel\Ai\Embeddings;

class EmbeddingService
{
    /**
     * Generate and store the embedding vector for the given project.
     */
    public function generateForProject(Project $project): Project
    {
        $embedding = Embeddings::for([$this->buildEmbeddingInput($project)])
            ->generate()
            ->first();

        $project->forceFill(['embedding' => $embedding])->save();

        return $project;
    }

    /**
     * Build the text that represents a project for embedding purposes.
     *
     * Combines the title, the plain-text description, and the image titles.
     */
    public function buildEmbeddingInput(Project $project): string
    {
        $project->loadMissing('images');

        return collect([
            $project->title,
            $this->toPlainText($project->description),
            ...$project->images->pluck('title')->all(),
        ])
            ->filter(fn (string $part): bool => $part !== '')
            ->implode("\n");
    }

    /**
     * Convert stored HTML into normalized plain text.
     */
    protected function toPlainText(string $html): string
    {
        $text = strip_tags(str_replace('<', ' <', $html));

        return trim((string) preg_replace('/\s+/', ' ', $text));
    }
}
