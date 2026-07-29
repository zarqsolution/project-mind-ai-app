<?php

namespace App\Services;

use App\Ai\Agents\ProposalWriter;
use App\Models\PromptTemplate;

class ProposalService
{
    /**
     * The prompt used when no default prompt template exists.
     */
    protected const string FALLBACK_PROMPT = 'Write a short, friendly, and specific proposal for the job below. Reference the most relevant portfolio projects to prove experience.';

    public function __construct(
        protected AISearchService $search,
        protected EmbeddingService $embeddings,
    ) {}

    /**
     * Generate a proposal for the given job description.
     *
     * @return array{proposal: string, used_prompt: string}
     */
    public function generate(string $jobDescription): array
    {
        $prompt = $this->buildPrompt($jobDescription);

        return [
            'proposal' => (new ProposalWriter)->prompt($prompt)->text,
            'used_prompt' => $prompt,
        ];
    }

    /**
     * Build the full prompt from the default template, job description, and relevant projects.
     */
    public function buildPrompt(string $jobDescription): string
    {
        $template = PromptTemplate::query()->where('is_default', true)->first();

        $projects = collect($this->search->search($jobDescription, 3))
            ->map(fn (array $result): string => $this->embeddings->buildEmbeddingInput($result['project']));

        return collect([
            '# Instructions',
            $template->prompt ?? self::FALLBACK_PROMPT,
            '# Job Description',
            $jobDescription,
            '# Relevant Portfolio Projects',
            $projects->isEmpty()
                ? 'No matching portfolio projects were found.'
                : $projects->implode("\n\n---\n\n"),
        ])->implode("\n\n");
    }
}
