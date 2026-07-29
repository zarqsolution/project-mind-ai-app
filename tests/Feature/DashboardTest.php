<?php

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\PromptHistory;
use App\Models\PromptTemplate;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('the dashboard shows the stat counts', function () {
    $this->actingAs(User::factory()->create());

    $projects = Project::factory()->count(3)->create();
    ProjectImage::factory()->count(4)->for($projects->first())->create();
    PromptTemplate::factory()->count(2)->create();
    PromptHistory::factory()->count(5)->create();

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->where('stats.projects', 3)
            ->where('stats.images', 4)
            ->where('stats.promptTemplates', 2)
            ->where('stats.proposals', 5));
});

test('the dashboard lists the five most recently updated projects', function () {
    $this->actingAs(User::factory()->create());

    Project::factory()->count(6)->create(['updated_at' => now()->subWeek()]);
    $latest = Project::factory()->create(['updated_at' => now()]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->count('recentProjects', 5)
            ->where('recentProjects.0.id', $latest->id)
            ->missing('recentProjects.0.embedding'));
});

test('the dashboard lists the five most recent proposals', function () {
    $this->actingAs(User::factory()->create());

    PromptHistory::factory()->count(6)->create(['created_at' => now()->subWeek()]);
    $latest = PromptHistory::factory()->create(['created_at' => now()]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->count('recentProposals', 5)
            ->where('recentProposals.0.id', $latest->id));
});

test('the dashboard renders with no data', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->where('stats.projects', 0)
            ->where('stats.images', 0)
            ->where('stats.promptTemplates', 0)
            ->where('stats.proposals', 0)
            ->count('recentProjects', 0)
            ->count('recentProposals', 0));
});
