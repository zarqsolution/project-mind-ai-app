<?php

use App\Models\User;

dataset('module pages', [
    'projects' => ['projects.index', 'projects/index'],
    'ai search' => ['ai-search.index', 'ai-search/index'],
    'proposal writer' => ['proposal-writer.index', 'proposal-writer/index'],
    'prompt templates' => ['prompt-templates.index', 'prompt-templates/index'],
    'history' => ['history.index', 'history/index'],
]);

test('guests are redirected to the login page', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with('module pages');

test('authenticated users can visit module pages', function (string $routeName, string $component) {
    $this->actingAs(User::factory()->create());

    $this->get(route($routeName))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component($component));
})->with('module pages');
