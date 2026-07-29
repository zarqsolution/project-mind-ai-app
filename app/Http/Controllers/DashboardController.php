<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboard) {}

    /**
     * Display the dashboard.
     */
    public function index(): Response
    {
        return Inertia::render('dashboard', $this->dashboard->overview());
    }
}
