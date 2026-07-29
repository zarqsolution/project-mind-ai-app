<?php

use App\Ai\Agents\ProposalWriter;
use App\Models\PromptHistory;
use App\Models\User;
use Laravel\Ai\Embeddings;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('generating a proposal saves a history entry', function () {
    ProposalWriter::fake(['Here is your personalized proposal.']);
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    $this->post(route('proposal-writer.store'), [
        'job_description' => 'Need a Laravel developer for a logistics platform.',
    ])->assertRedirect(route('proposal-writer.index'));

    expect(PromptHistory::query()->count())->toBe(1);

    $history = PromptHistory::query()->firstOrFail();

    expect($history->job_description)->toBe('Need a Laravel developer for a logistics platform.')
        ->and($history->used_prompt)->toContain('Need a Laravel developer for a logistics platform.')
        ->and($history->generated_proposal)->toBe('Here is your personalized proposal.');
});

test('the history page lists entries newest first', function () {
    $older = PromptHistory::factory()->create(['created_at' => now()->subDay()]);
    $newer = PromptHistory::factory()->create(['created_at' => now()]);

    $this->get(route('history.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('history/index')
            ->count('history.data', 2)
            ->where('history.data.0.id', $newer->id)
            ->where('history.data.1.id', $older->id)
            ->where('history.total', 2));
});

test('the history page paginates entries', function () {
    PromptHistory::factory()->count(11)->create();

    $this->get(route('history.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('history/index')
            ->count('history.data', 10)
            ->where('history.last_page', 2)
            ->where('history.total', 11));
});

test('a history entry can be deleted', function () {
    $history = PromptHistory::factory()->create();

    $this->delete(route('history.destroy', $history))
        ->assertRedirect(route('history.index'));

    $this->assertDatabaseMissing('prompt_history', ['id' => $history->id]);
});

test('the proposal writer prefills the job description for reuse', function () {
    $this->get(route('proposal-writer.index', ['job_description' => 'Reused job description']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('proposal-writer/index')
            ->where('jobDescription', 'Reused job description'));
});

test('guests cannot access prompt history', function () {
    auth()->logout();

    $history = PromptHistory::factory()->create();

    $this->get(route('history.index'))->assertRedirect(route('login'));
    $this->delete(route('history.destroy', $history))->assertRedirect(route('login'));
});
