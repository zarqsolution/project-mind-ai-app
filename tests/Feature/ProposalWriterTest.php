<?php

use App\Ai\Agents\ProposalWriter;
use App\Models\Project;
use App\Models\PromptTemplate;
use App\Models\User;
use Laravel\Ai\Embeddings;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('the proposal writer page renders without a proposal', function () {
    $this->get(route('proposal-writer.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('proposal-writer/index')
            ->where('proposal', null));
});

test('a proposal can be generated from a job description', function () {
    ProposalWriter::fake(['Here is your personalized proposal.']);
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    Project::factory()->create(['embedding' => [1.0, 0.0, 0.0]]);
    PromptTemplate::factory()->default()->create();

    $this->post(route('proposal-writer.store'), [
        'job_description' => 'Need a Laravel developer for a logistics platform.',
    ])
        ->assertRedirect(route('proposal-writer.index'))
        ->assertSessionHas('proposal', 'Here is your personalized proposal.');
});

test('the generated proposal is shared with the page after redirect', function () {
    ProposalWriter::fake(['Here is your personalized proposal.']);
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    $this->followingRedirects()
        ->post(route('proposal-writer.store'), [
            'job_description' => 'Need a Laravel developer.',
        ])
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('proposal-writer/index')
            ->where('proposal', 'Here is your personalized proposal.'));
});

test('the prompt contains the default template, job description, and relevant projects', function () {
    ProposalWriter::fake(['Proposal.']);
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    Project::factory()->create([
        'title' => 'FleetTrack Logistics Platform',
        'embedding' => [1.0, 0.0, 0.0],
    ]);

    PromptTemplate::factory()->default()->create([
        'prompt' => 'Always open with a question about the client goal.',
    ]);

    $this->post(route('proposal-writer.store'), [
        'job_description' => 'Need a logistics tracking system.',
    ])->assertRedirect();

    ProposalWriter::assertPrompted(fn ($prompt) => $prompt->contains('Always open with a question about the client goal.')
        && $prompt->contains('Need a logistics tracking system.')
        && $prompt->contains('FleetTrack Logistics Platform'));
});

test('a fallback prompt is used when no default template exists', function () {
    ProposalWriter::fake(['Proposal.']);
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    $this->post(route('proposal-writer.store'), [
        'job_description' => 'Need a Laravel developer.',
    ])->assertRedirect();

    ProposalWriter::assertPrompted(fn ($prompt) => $prompt->contains('Write a short, friendly, and specific proposal'));
});

test('the prompt notes when no portfolio projects match', function () {
    ProposalWriter::fake(['Proposal.']);
    Embeddings::fake([[[1.0, 0.0, 0.0]]]);

    Project::factory()->create(['embedding' => null]);

    $this->post(route('proposal-writer.store'), [
        'job_description' => 'Need a Laravel developer.',
    ])->assertRedirect();

    ProposalWriter::assertPrompted(fn ($prompt) => $prompt->contains('No matching portfolio projects were found.'));
});

test('a job description is required', function () {
    ProposalWriter::fake();
    Embeddings::fake();

    $this->post(route('proposal-writer.store'), ['job_description' => ''])
        ->assertSessionHasErrors('job_description');

    ProposalWriter::assertNeverPrompted();
    Embeddings::assertNothingGenerated();
});

test('guests cannot use the proposal writer', function () {
    auth()->logout();

    $this->get(route('proposal-writer.index'))->assertRedirect(route('login'));
    $this->post(route('proposal-writer.store'))->assertRedirect(route('login'));
});
