<?php

use App\Models\Project;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Support\Facades\Storage;

test('the portfolio seeder creates every project', function () {
    Storage::fake('public');

    $this->seed(PortfolioSeeder::class);

    expect(Project::query()->count())->toBe(21)
        ->and(Project::query()->whereNotNull('embedding')->count())->toBe(0);
});

test('projects with an image folder get stored images and a thumbnail from the first image', function () {
    Storage::fake('public');

    $this->seed(PortfolioSeeder::class);

    $project = Project::query()->where('slug', 'freightanywhere-tms')->firstOrFail();

    expect($project->thumbnail)->toStartWith('projects/thumbnails/')
        ->and($project->images)->toHaveCount(8);

    Storage::disk('public')->assertExists($project->thumbnail);

    $project->images->each(function ($image, $index) {
        expect($image->image)->toStartWith('projects/images/')
            ->and($image->sort_order)->toBe($index)
            ->and($image->title)->not->toBeEmpty();

        Storage::disk('public')->assertExists($image->image);
    });
});

test('nested folder images are included after the main folder images', function () {
    Storage::fake('public');

    $this->seed(PortfolioSeeder::class);

    $project = Project::query()->where('slug', 'pa-transport-logistics-tms')->firstOrFail();

    expect($project->images)->toHaveCount(27);
});

test('projects without an image folder are seeded without images or thumbnail', function () {
    Storage::fake('public');

    $this->seed(PortfolioSeeder::class);

    $project = Project::query()->where('slug', 'marstranslation')->firstOrFail();

    expect($project->thumbnail)->toBeNull()
        ->and($project->images)->toHaveCount(0);
});

test('the seeder is idempotent', function () {
    Storage::fake('public');

    $this->seed(PortfolioSeeder::class);
    $this->seed(PortfolioSeeder::class);

    expect(Project::query()->count())->toBe(21);
});

test('seeded descriptions never contain credentials', function () {
    Storage::fake('public');

    $this->seed(PortfolioSeeder::class);

    Project::query()->get()->each(function (Project $project) {
        expect($project->description)
            ->not->toContain('Username =')
            ->not->toContain('Password =')
            ->not->toContain('Login Credintials')
            ->not->toContain('@gmail.com')
            ->not->toContain('@admin.com');
    });
});
