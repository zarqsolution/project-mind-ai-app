<?php

namespace App\Services;

use App\Models\Project;
use Laravel\Ai\Embeddings;

class AISearchService
{
    /**
     * Common words ignored when explaining why a project matched.
     *
     * @var list<string>
     */
    protected const array STOP_WORDS = [
        'a', 'an', 'and', 'are', 'find', 'for', 'in', 'is', 'me', 'my', 'of',
        'on', 'or', 'project', 'projects', 'show', 'that', 'the', 'to', 'using',
        'want', 'with',
    ];

    /**
     * Search the portfolio for the projects most similar to the given query.
     *
     * @return list<array{project: Project, score: float, reason: string}>
     */
    public function search(string $query, int $limit = 5): array
    {
        $queryEmbedding = Embeddings::for([$query])->generate()->first();

        $results = Project::query()
            ->whereNotNull('embedding')
            ->get()
            ->map(fn (Project $project): array => [
                'project' => $project,
                'score' => $this->cosineSimilarity($queryEmbedding, $project->embedding ?? []),
            ])
            ->sortByDesc('score')
            ->take($limit)
            ->map(fn (array $result): array => [
                'project' => $result['project'],
                'score' => round($result['score'], 4),
                'reason' => $this->buildReason($query, $result['project']),
            ])
            ->all();

        return array_values($results);
    }

    /**
     * Calculate the cosine similarity between two vectors.
     *
     * @param  array<int, float>  $a
     * @param  array<int, float>  $b
     */
    protected function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b) || $a === []) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $index => $value) {
            $dotProduct += $value * $b[$index];
            $normA += $value * $value;
            $normB += $b[$index] * $b[$index];
        }

        if ($normA === 0.0 || $normB === 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Explain why a project matched the given query.
     */
    protected function buildReason(string $query, Project $project): string
    {
        $haystack = mb_strtolower($project->title.' '.strip_tags($project->description));

        $matches = collect(preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($query)) ?: [])
            ->filter(fn (string $term): bool => mb_strlen($term) > 2
                && ! in_array($term, self::STOP_WORDS, true)
                && str_contains($haystack, $term))
            ->unique()
            ->values();

        if ($matches->isEmpty()) {
            return __('Semantically related to your query based on the project description.');
        }

        return __('Mentions :terms in the project description.', [
            'terms' => $matches->implode(', '),
        ]);
    }
}
