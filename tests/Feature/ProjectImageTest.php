<?php

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('an image can be added to a project', function () {
    Storage::fake('public');

    $project = Project::factory()->create();

    $this->post(route('projects.images.store', $project), [
        'title' => 'Dashboard overview',
        'image' => UploadedFile::fake()->image('screen.png', 1600, 900),
    ])->assertRedirect(route('projects.show', $project));

    $image = $project->images()->firstOrFail();

    expect($image)
        ->title->toBe('Dashboard overview')
        ->sort_order->toBe(1);

    Storage::disk('public')->assertExists($image->image);
});

test('new images are appended to the end of the gallery', function () {
    Storage::fake('public');

    $project = Project::factory()->create();
    ProjectImage::factory()->for($project)->create(['sort_order' => 5]);

    $this->post(route('projects.images.store', $project), [
        'title' => 'Checkout flow',
        'image' => UploadedFile::fake()->image('screen.png'),
    ]);

    expect($project->images()->where('title', 'Checkout flow')->firstOrFail()->sort_order)->toBe(6);
});

test('adding an image requires a title and an image file', function () {
    $project = Project::factory()->create();

    $this->from(route('projects.show', $project))
        ->post(route('projects.images.store', $project), [])
        ->assertRedirect(route('projects.show', $project))
        ->assertSessionHasErrors(['title', 'image']);
});

test('the project detail page includes ordered images', function () {
    $project = Project::factory()->create();
    $second = ProjectImage::factory()->for($project)->create(['sort_order' => 2]);
    $first = ProjectImage::factory()->for($project)->create(['sort_order' => 1]);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('projects/show')
            ->has('project.images', 2)
            ->where('project.images.0.id', $first->id)
            ->where('project.images.1.id', $second->id));
});

test('images can be reordered', function () {
    $project = Project::factory()->create();
    $images = ProjectImage::factory()
        ->for($project)
        ->count(3)
        ->sequence(fn ($sequence) => ['sort_order' => $sequence->index])
        ->create();

    $this->put(route('projects.images.reorder', $project), [
        'order' => [$images[2]->id, $images[0]->id, $images[1]->id],
    ])->assertRedirect(route('projects.show', $project));

    expect($project->images()->pluck('id')->all())
        ->toBe([$images[2]->id, $images[0]->id, $images[1]->id]);
});

test('an image can be deleted along with its file', function () {
    Storage::fake('public');

    $path = UploadedFile::fake()->image('screen.png')->store('projects/images', 'public');
    $project = Project::factory()->create();
    $image = ProjectImage::factory()->for($project)->create(['image' => $path]);

    $this->delete(route('projects.images.destroy', [$project, $image]))
        ->assertRedirect(route('projects.show', $project));

    expect(ProjectImage::query()->count())->toBe(0);
    Storage::disk('public')->assertMissing($path);
});

test('an image cannot be deleted through another project', function () {
    $image = ProjectImage::factory()->create();
    $otherProject = Project::factory()->create();

    $this->delete(route('projects.images.destroy', [$otherProject, $image]))
        ->assertNotFound();

    expect(ProjectImage::query()->count())->toBe(1);
});

test('deleting a project deletes its image files', function () {
    Storage::fake('public');

    $path = UploadedFile::fake()->image('screen.png')->store('projects/images', 'public');
    $project = Project::factory()->create();
    ProjectImage::factory()->for($project)->create(['image' => $path]);

    $this->delete(route('projects.destroy', $project));

    expect(ProjectImage::query()->count())->toBe(0);
    Storage::disk('public')->assertMissing($path);
});

test('the project list includes the image count', function () {
    $project = Project::factory()->create();
    ProjectImage::factory()->for($project)->count(2)->create();

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('projects/index')
            ->where('projects.data.0.images_count', 2));
});

test('guests cannot manage project images', function () {
    auth()->logout();

    $project = Project::factory()->create();

    $this->post(route('projects.images.store', $project))->assertRedirect(route('login'));
    $this->put(route('projects.images.reorder', $project))->assertRedirect(route('login'));
});
