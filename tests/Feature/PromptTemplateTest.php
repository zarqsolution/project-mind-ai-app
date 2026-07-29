<?php

use App\Models\PromptTemplate;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('the prompt templates page lists templates with the default first', function () {
    PromptTemplate::factory()->create(['name' => 'Aaa template']);
    $default = PromptTemplate::factory()->default()->create(['name' => 'Zzz template']);

    $this->get(route('prompt-templates.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('prompt-templates/index')
            ->count('templates', 2)
            ->where('templates.0.id', $default->id)
            ->where('templates.0.is_default', true));
});

test('a prompt template can be created', function () {
    $this->post(route('prompt-templates.store'), [
        'name' => 'Upwork proposal',
        'prompt' => 'Write a short, personal proposal.',
        'is_default' => '0',
    ])->assertRedirect(route('prompt-templates.index'));

    $this->assertDatabaseHas('prompt_templates', [
        'name' => 'Upwork proposal',
        'prompt' => 'Write a short, personal proposal.',
    ]);
});

test('the first template automatically becomes the default', function () {
    $this->post(route('prompt-templates.store'), [
        'name' => 'Upwork proposal',
        'prompt' => 'Write a short, personal proposal.',
        'is_default' => '0',
    ])->assertRedirect();

    expect(PromptTemplate::query()->sole()->is_default)->toBeTrue();
});

test('creating a new default unsets the previous default', function () {
    $previous = PromptTemplate::factory()->default()->create();

    $this->post(route('prompt-templates.store'), [
        'name' => 'New default',
        'prompt' => 'Prompt body.',
        'is_default' => '1',
    ])->assertRedirect();

    expect($previous->refresh()->is_default)->toBeFalse()
        ->and(PromptTemplate::query()->where('is_default', true)->sole()->name)->toBe('New default');
});

test('name and prompt are required', function () {
    $this->post(route('prompt-templates.store'), [
        'name' => '',
        'prompt' => '',
    ])->assertSessionHasErrors(['name', 'prompt']);
});

test('a prompt template can be updated', function () {
    $template = PromptTemplate::factory()->create();

    $this->put(route('prompt-templates.update', $template), [
        'name' => 'Renamed template',
        'prompt' => 'Updated prompt body.',
        'is_default' => '0',
    ])->assertRedirect(route('prompt-templates.index'));

    expect($template->refresh())
        ->name->toBe('Renamed template')
        ->prompt->toBe('Updated prompt body.');
});

test('marking a template as default via update unsets the previous default', function () {
    $previous = PromptTemplate::factory()->default()->create();
    $template = PromptTemplate::factory()->create();

    $this->put(route('prompt-templates.update', $template), [
        'name' => $template->name,
        'prompt' => $template->prompt,
        'is_default' => '1',
    ])->assertRedirect();

    expect($template->refresh()->is_default)->toBeTrue()
        ->and($previous->refresh()->is_default)->toBeFalse();
});

test('a prompt template can be deleted', function () {
    $template = PromptTemplate::factory()->create();

    $this->delete(route('prompt-templates.destroy', $template))
        ->assertRedirect(route('prompt-templates.index'));

    $this->assertDatabaseMissing('prompt_templates', ['id' => $template->id]);
});

test('deleting the default template promotes another template', function () {
    $default = PromptTemplate::factory()->default()->create();
    $other = PromptTemplate::factory()->create();

    $this->delete(route('prompt-templates.destroy', $default))
        ->assertRedirect();

    expect($other->refresh()->is_default)->toBeTrue();
});

test('guests cannot manage prompt templates', function () {
    auth()->logout();

    $template = PromptTemplate::factory()->create();

    $this->get(route('prompt-templates.index'))->assertRedirect(route('login'));
    $this->post(route('prompt-templates.store'))->assertRedirect(route('login'));
    $this->put(route('prompt-templates.update', $template))->assertRedirect(route('login'));
    $this->delete(route('prompt-templates.destroy', $template))->assertRedirect(route('login'));
});
