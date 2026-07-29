<?php

namespace App\Http\Controllers;

use App\Services\AISearchService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AISearchController extends Controller
{
    public function __construct(protected AISearchService $search) {}

    /**
     * Display the AI search page with optional search results.
     */
    public function index(Request $request): Response
    {
        $query = $request->string('query')->trim()->toString();

        return Inertia::render('ai-search/index', [
            'query' => $query,
            'results' => $query === '' ? null : $this->search->search($query),
        ]);
    }
}
