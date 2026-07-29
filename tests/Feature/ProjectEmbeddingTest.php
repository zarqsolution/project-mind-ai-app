<?php

use App\Jobs\GenerateProjectEmbedding;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\User;
use App\Services\EmbeddingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
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

test('updating the title or description queues an embedding regeneration', function () {
    Queue::fake();

    $project = Project::factory()->create();

    $this->put(route('projects.update', $project), [
        'title' => $project->title,
        'description' => '<p>Updated description.</p>',
    ])->assertRedirect();

    Queue::assertPushed(GenerateProjectEmbedding::class, fn ($job) => $job->project->is($project));
});

test('updating without changing the title or description does not queue a regeneration', function () {
    Queue::fake();

    $project = Project::factory()->create();

    $this->put(route('projects.update', $project), [
        'title' => $project->title,
        'description' => $project->description,
    ])->assertRedirect();

    Queue::assertNotPushed(GenerateProjectEmbedding::class);
});

test('adding and deleting images queue an embedding regeneration', function () {
    Queue::fake();
    Storage::fake('public');

    $project = Project::factory()->create();

    $this->post(route('projects.images.store', $project), [
        'title' => 'Dashboard view',
        'image' => UploadedFile::fake()->image('dashboard.png'),
    ])->assertRedirect();

    Queue::assertPushed(GenerateProjectEmbedding::class, 1);

    $image = $project->images()->firstOrFail();

    $this->delete(route('projects.images.destroy', ['project' => $project, 'image' => $image]))
        ->assertRedirect();

    Queue::assertPushed(GenerateProjectEmbedding::class, 2);
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
