<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\PromptHistory;
use App\Models\PromptTemplate;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    /**
     * Gather the data displayed on the dashboard.
     *
     * @return array{
     *     stats: array{projects: int, images: int, promptTemplates: int, proposals: int},
     *     recentProjects: Collection<int, Project>,
     *     recentProposals: Collection<int, PromptHistory>,
     * }
     */
    public function overview(): array
    {
        return [
            'stats' => [
                'projects' => Project::query()->count(),
                'images' => ProjectImage::query()->count(),
                'promptTemplates' => PromptTemplate::query()->count(),
                'proposals' => PromptHistory::query()->count(),
            ],
            'recentProjects' => Project::query()
                ->withCount('images')
                ->latest('updated_at')
                ->orderByDesc('id')
                ->take(5)
                ->get(),
            'recentProposals' => PromptHistory::query()
                ->latest('created_at')
                ->orderByDesc('id')
                ->take(5)
                ->get(),
        ];
    }
}
