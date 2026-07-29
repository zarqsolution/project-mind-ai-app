<?php

namespace App\Http\Controllers;

use App\Models\PromptHistory;
use App\Services\ProposalService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PromptHistoryController extends Controller
{
    public function __construct(protected ProposalService $proposals) {}

    /**
     * Display the prompt history.
     */
    public function index(): Response
    {
        return Inertia::render('history/index', [
            'history' => $this->proposals->history(),
        ]);
    }

    /**
     * Delete the given prompt history entry.
     */
    public function destroy(PromptHistory $history): RedirectResponse
    {
        $this->proposals->deleteHistory($history);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('History entry deleted.'),
        ]);

        return to_route('history.index');
    }
}
