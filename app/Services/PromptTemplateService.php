<?php

namespace App\Services;

use App\Models\PromptTemplate;
use Illuminate\Database\Eloquent\Collection;

class PromptTemplateService
{
    /**
     * List all prompt templates, with the default first.
     *
     * @return Collection<int, PromptTemplate>
     */
    public function list(): Collection
    {
        return PromptTemplate::query()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new prompt template.
     *
     * @param  array{name: string, prompt: string, is_default: bool}  $data
     */
    public function create(array $data): PromptTemplate
    {
        $makeDefault = $data['is_default'] || ! PromptTemplate::query()->exists();

        $template = PromptTemplate::query()->create([
            'name' => $data['name'],
            'prompt' => $data['prompt'],
        ]);

        if ($makeDefault) {
            $this->makeDefault($template);
        }

        return $template;
    }

    /**
     * Update an existing prompt template.
     *
     * @param  array{name: string, prompt: string, is_default: bool}  $data
     */
    public function update(PromptTemplate $template, array $data): PromptTemplate
    {
        $template->update([
            'name' => $data['name'],
            'prompt' => $data['prompt'],
        ]);

        if ($data['is_default']) {
            $this->makeDefault($template);
        }

        return $template->refresh();
    }

    /**
     * Delete a prompt template, promoting another to default when needed.
     */
    public function delete(PromptTemplate $template): void
    {
        $wasDefault = $template->is_default;

        $template->delete();

        if ($wasDefault) {
            PromptTemplate::query()->latest('updated_at')->first()?->update(['is_default' => true]);
        }
    }

    /**
     * Mark the given template as the only default.
     */
    public function makeDefault(PromptTemplate $template): PromptTemplate
    {
        PromptTemplate::query()->whereKeyNot($template->id)->update(['is_default' => false]);

        $template->update(['is_default' => true]);

        return $template;
    }
}
