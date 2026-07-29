<?php

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\User;
use App\Services\EmbeddingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Embeddings;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('the generate embedding button stores an embedding on the project', function () {
    Embeddings::fake();

    $project = Project::factory()->create();

    $this->post(route('projects.embedding.generate', $project))
        ->assertRedirect();

    expect($project->refresh()->embedding)
        ->toBeArray()
        ->not->toBeEmpty();

    Embeddings::assertGenerated(fn ($prompt) => $prompt->contains($project->title));
});

test('the embedding input contains the title, plain-text description, and image titles', function () {
    $project = Project::factory()->create([
        'title' => 'Realtime Chat App',
        'description' => '<h1>Overview</h1><p>Built with WebSockets.</p>',
    ]);

    ProjectImage::factory()->for($project)->create(['title' => 'Inbox screen']);

    $input = app(EmbeddingService::class)->buildEmbeddingInput($project);

    expect($input)
        ->toContain('Realtime Chat App')
        ->toContain('Overview Built with WebSockets.')
        ->toContain('Inbox screen')
        ->not->toContain('<h1>');
});

test('creating a project generates an embedding', function () {
    Embeddings::fake();

    $this->post(route('projects.store'), [
        'title' => 'Realtime Chat App',
        'description' => '<p>Built with WebSockets.</p>',
    ])->assertRedirect();

    $project = Project::query()->firstWhere('title', 'Realtime Chat App');

    expect($project->embedding)->toBeArray()->not->toBeEmpty();
});

test('updating the title or description regenerates the embedding', function () {
    Embeddings::fake();

    $project = Project::factory()->create();

    $this->put(route('projects.update', $project), [
        'title' => $project->title,
        'description' => '<p>Updated description.</p>',
    ])->assertRedirect();

    expect($project->refresh()->embedding)->toBeArray()->not->toBeEmpty();

    Embeddings::assertGenerated(fn ($prompt) => $prompt->contains('Updated description.'));
});

test('updating without changing the title or description does not regenerate the embedding', function () {
    Embeddings::fake();

    $project = Project::factory()->create();

    $this->put(route('projects.update', $project), [
        'title' => $project->title,
        'description' => $project->description,
    ])->assertRedirect();

    Embeddings::assertNothingGenerated();
});

test('adding and deleting images regenerate the embedding', function () {
    Embeddings::fake();
    Storage::fake('public');

    $project = Project::factory()->create();

    $this->post(route('projects.images.store', $project), [
        'title' => 'Dashboard view',
        'image' => UploadedFile::fake()->image('dashboard.png'),
    ])->assertRedirect();

    Embeddings::assertGenerated(fn ($prompt) => $prompt->contains('Dashboard view'));

    $image = $project->images()->firstOrFail();

    $this->delete(route('projects.images.destroy', ['project' => $project, 'image' => $image]))
        ->assertRedirect();

    Embeddings::assertGenerated(fn ($prompt) => ! $prompt->contains('Dashboard view'));

    expect($project->refresh()->embedding)->toBeArray()->not->toBeEmpty();
});

test('the raw embedding is hidden but its status is shared with the page', function () {
    Embeddings::fake();

    $project = app(EmbeddingService::class)->generateForProject(Project::factory()->create());

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('projects/show')
            ->where('project.has_embedding', true)
            ->missing('project.embedding'));
});

test('guests cannot generate embeddings', function () {
    auth()->logout();

    $project = Project::factory()->create();

    $this->post(route('projects.embedding.generate', $project))
        ->assertRedirect(route('login'));
});

test('the generate embedding button works against the real provider', function () {
    $project = Project::factory()->create([
        'title' => 'FleetTrack Logistics Platform',
        'description' => '<p>Real-time GPS tracking and dispatch for courier fleets.</p>',
    ]);

    $this->post(route('projects.embedding.generate', $project))
        ->assertRedirect();

    expect($project->refresh()->embedding)
        ->toBeArray()
        ->toHaveCount(1536);
})->skip('Live API test — verified passing on 2026-07-29. Remove this skip to re-run against the real provider.');
