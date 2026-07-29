<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromptTemplateRequest;
use App\Http\Requests\UpdatePromptTemplateRequest;
use App\Models\PromptTemplate;
use App\Services\PromptTemplateService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PromptTemplateController extends Controller
{
    public function __construct(protected PromptTemplateService $templates) {}

    /**
     * Display a listing of the prompt templates.
     */
    public function index(): Response
    {
        return Inertia::render('prompt-templates/index', [
            'templates' => $this->templates->list(),
        ]);
    }

    /**
     * Store a newly created prompt template.
     */
    public function store(StorePromptTemplateRequest $request): RedirectResponse
    {
        $this->templates->create([
            'name' => $request->string('name')->toString(),
            'prompt' => $request->string('prompt')->toString(),
            'is_default' => $request->boolean('is_default'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Prompt template created.')]);

        return to_route('prompt-templates.index');
    }

    /**
     * Update the specified prompt template.
     */
    public function update(UpdatePromptTemplateRequest $request, PromptTemplate $promptTemplate): RedirectResponse
    {
        $this->templates->update($promptTemplate, [
            'name' => $request->string('name')->toString(),
            'prompt' => $request->string('prompt')->toString(),
            'is_default' => $request->boolean('is_default'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Prompt template updated.')]);

        return to_route('prompt-templates.index');
    }

    /**
     * Remove the specified prompt template.
     */
    public function destroy(PromptTemplate $promptTemplate): RedirectResponse
    {
        $this->templates->delete($promptTemplate);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Prompt template deleted.')]);

        return to_route('prompt-templates.index');
    }
}
