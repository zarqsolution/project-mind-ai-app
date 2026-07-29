<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderProjectImagesRequest;
use App\Http\Requests\StoreProjectImageRequest;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ProjectImageController extends Controller
{
    public function __construct(protected ProjectService $projects) {}

    /**
     * Add an image to the project.
     */
    public function store(StoreProjectImageRequest $request, Project $project): RedirectResponse
    {
        $this->projects->addImage(
            $project,
            $request->string('title')->toString(),
            $request->file('image'),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Image added.')]);

        return to_route('projects.show', $project);
    }

    /**
     * Reorder the project's images.
     */
    public function reorder(ReorderProjectImagesRequest $request, Project $project): RedirectResponse
    {
        $orderedIds = array_map(
            fn ($imageId): int => (int) $imageId,
            array_values($request->array('order')),
        );

        $this->projects->reorderImages($project, $orderedIds);

        return to_route('projects.show', $project);
    }

    /**
     * Remove an image from the project.
     */
    public function destroy(Project $project, ProjectImage $image): RedirectResponse
    {
        $this->projects->deleteImage($image);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Image deleted.')]);

        return to_route('projects.show', $project);
    }
}
