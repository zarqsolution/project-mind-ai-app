<?php

use App\Models\Project;
use App\Models\User;
use Laravel\Ai\Embeddings;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('the search page renders without a query', function () {
    Embeddings::fake();

    $this->get(route('ai-search.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('ai-search/index')
            ->where('query', '')
            ->where('results', null));

    Embeddings::assertNothingGenerated();
});

test('searching returns the most similar projects first', function () {
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    $logistics = Project::factory()->create([
        'title' => 'FleetTrack Logistics Platform',
        'embedding' => [1.0, 0.0, 0.0],
    ]);

    $ecommerce = Project::factory()->create([
        'title' => 'ShopStream E-commerce Store',
        'embedding' => [0.0, 1.0, 0.0],
    ]);

    $this->get(route('ai-search.index', ['query' => 'logistics tracking']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('ai-search/index')
            ->where('query', 'logistics tracking')
            ->count('results', 2)
            ->where('results.0.project.id', $logistics->id)
            ->where('results.0.score', 1)
            ->where('results.1.project.id', $ecommerce->id)
            ->where('results.1.score', 0));

    Embeddings::assertGenerated(fn ($prompt) => $prompt->contains('logistics tracking'));
});

test('projects without embeddings are excluded from the results', function () {
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    Project::factory()->create(['embedding' => null]);
    $indexed = Project::factory()->create(['embedding' => [1.0, 0.0, 0.0]]);

    $this->get(route('ai-search.index', ['query' => 'anything at all']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->count('results', 1)
            ->where('results.0.project.id', $indexed->id));
});

test('results are limited to the top five matches', function () {
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    Project::factory()->count(6)->create(['embedding' => [1.0, 0.0, 0.0]]);

    $this->get(route('ai-search.index', ['query' => 'anything at all']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->count('results', 5));
});

test('the reason mentions query terms found in the project', function () {
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    Project::factory()->create([
        'title' => 'FleetTrack Logistics Platform',
        'description' => '<p>Realtime GPS tracking for delivery fleets.</p>',
        'embedding' => [1.0, 0.0, 0.0],
    ]);

    $this->get(route('ai-search.index', ['query' => 'Show me logistics projects with tracking']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('results.0.reason', 'Mentions logistics, tracking in the project description.'));
});

test('a semantic fallback reason is used when no query terms match', function () {
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    Project::factory()->create([
        'title' => 'FleetTrack Logistics Platform',
        'description' => '<p>Realtime GPS tracking for delivery fleets.</p>',
        'embedding' => [1.0, 0.0, 0.0],
    ]);

    $this->get(route('ai-search.index', ['query' => 'healthcare portals']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('results.0.reason', 'Semantically related to your query based on the project description.'));
});

test('raw embeddings are not shared with the search results', function () {
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    Project::factory()->create(['embedding' => [1.0, 0.0, 0.0]]);

    $this->get(route('ai-search.index', ['query' => 'anything at all']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->missing('results.0.project.embedding'));
});

test('guests cannot use the AI search', function () {
    auth()->logout();

    $this->get(route('ai-search.index'))
        ->assertRedirect(route('login'));
});
