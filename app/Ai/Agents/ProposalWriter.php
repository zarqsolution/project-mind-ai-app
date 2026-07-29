<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::OpenRouter)]
#[Model('cohere/north-mini-code:free')]
class ProposalWriter implements Agent
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are an expert freelance proposal writer. Write concise, personal '
            .'proposals grounded strictly in the portfolio projects provided. '
            .'Never invent experience or projects that are not in the portfolio. '
            .'Respond with the proposal text only, without any preamble.';
    }
}
