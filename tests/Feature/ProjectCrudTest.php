<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('projects index lists projects', function () {
    $projects = Project::factory()->count(3)->create();

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('projects/index')
            ->has('projects.data', 3)
            ->where('projects.data.0.title', $projects->sortByDesc('updated_at')->first()->title)
            ->where('filters.search', ''));
});

test('projects index filters by search term', function () {
    Project::factory()->create(['title' => 'Realtime Chat App']);
    Project::factory()->create(['title' => 'Inventory System']);

    $this->get(route('projects.index', ['search' => 'chat']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('projects/index')
            ->has('projects.data', 1)
            ->where('projects.data.0.title', 'Realtime Chat App')
            ->where('filters.search', 'chat'));
});

test('create page provides the description template', function () {
    $this->get(route('projects.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('projects/create')
            ->where('descriptionTemplate', fn ($template) => str_contains((string) $template, '<h1>Overview</h1>')));
});

test('a project can be created with a thumbnail', function () {
    Storage::fake('public');

    $response = $this->post(route('projects.store'), [
        'title' => 'Realtime Chat App',
        'description' => 'A websocket powered chat application.',
        'thumbnail' => UploadedFile::fake()->image('thumb.png', 1200, 630),
    ]);

    $project = Project::query()->firstOrFail();

    $response->assertRedirect(route('projects.show', $project));

    expect($project)
        ->title->toBe('Realtime Chat App')
        ->slug->toBe('realtime-chat-app')
        ->thumbnail->not->toBeNull();

    Storage::disk('public')->assertExists($project->thumbnail);
});

test('a project can be created without a thumbnail', function () {
    $this->post(route('projects.store'), [
        'title' => 'Inventory System',
        'description' => 'Tracks stock levels.',
    ])->assertRedirect(route('projects.show', 'inventory-system'));

    expect(Project::query()->firstOrFail()->thumbnail)->toBeNull();
});

test('project creation requires title and description', function () {
    $this->from(route('projects.create'))
        ->post(route('projects.store'), [])
        ->assertRedirect(route('projects.create'))
        ->assertSessionHasErrors(['title', 'description']);
});

test('duplicate titles get a unique slug', function () {
    Project::factory()->create(['title' => 'Chat App', 'slug' => 'chat-app']);

    $this->post(route('projects.store'), [
        'title' => 'Chat App',
        'description' => 'Another chat app.',
    ]);

    expect(Project::query()->latest('id')->firstOrFail()->slug)->toBe('chat-app-2');
});

test('a project detail page can be viewed', function () {
    $project = Project::factory()->create();

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('projects/show')
            ->where('project.title', $project->title)
            ->where('project.slug', $project->slug));
});

test('a project can be updated and the slug follows the title', function () {
    $project = Project::factory()->create(['title' => 'Old Title', 'slug' => 'old-title']);

    $this->put(route('projects.update', $project), [
        'title' => 'New Title',
        'description' => 'Updated description.',
    ])->assertRedirect(route('projects.show', 'new-title'));

    expect($project->refresh())
        ->title->toBe('New Title')
        ->slug->toBe('new-title')
        ->description->toBe('Updated description.');
});

test('updating the thumbnail replaces the old file', function () {
    Storage::fake('public');

    $oldPath = UploadedFile::fake()->image('old.png')->store('projects/thumbnails', 'public');
    $project = Project::factory()->create(['thumbnail' => $oldPath]);

    $this->put(route('projects.update', $project), [
        'title' => $project->title,
        'description' => $project->description,
        'thumbnail' => UploadedFile::fake()->image('new.png'),
    ]);

    $project->refresh();

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($project->thumbnail);
});

test('a project can be deleted along with its thumbnail', function () {
    Storage::fake('public');

    $path = UploadedFile::fake()->image('thumb.png')->store('projects/thumbnails', 'public');
    $project = Project::factory()->create(['thumbnail' => $path]);

    $this->delete(route('projects.destroy', $project))
        ->assertRedirect(route('projects.index'));

    expect(Project::query()->count())->toBe(0);
    Storage::disk('public')->assertMissing($path);
});

test('guests cannot access project pages', function () {
    auth()->logout();

    $project = Project::factory()->create();

    $this->get(route('projects.index'))->assertRedirect(route('login'));
    $this->get(route('projects.show', $project))->assertRedirect(route('login'));
    $this->post(route('projects.store'))->assertRedirect(route('login'));
});
