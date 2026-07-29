<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateProposalRequest;
use App\Services\ProposalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProposalController extends Controller
{
    public function __construct(protected ProposalService $proposals) {}

    /**
     * Display the proposal writer page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('proposal-writer/index', [
            'proposal' => $request->session()->get('proposal'),
            'jobDescription' => $request->string('job_description')->toString(),
        ]);
    }

    /**
     * Generate a proposal for the submitted job description.
     */
    public function store(GenerateProposalRequest $request): RedirectResponse
    {
        $result = $this->proposals->generate(
            $request->string('job_description')->toString(),
        );

        return to_route('proposal-writer.index')->with('proposal', $result['proposal']);
    }
}
