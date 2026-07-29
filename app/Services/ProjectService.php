<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProjectService
{
    public const string DESCRIPTION_TEMPLATE = <<<'HTML'
<h1>Overview</h1>
<p>Describe the project.</p>
<hr>
<h1>Client</h1>
<p></p>
<hr>
<h1>Problem</h1>
<p></p>
<hr>
<h1>Solution</h1>
<p></p>
<hr>
<h1>My Role</h1>
<p></p>
<hr>
<h1>Technologies</h1>
<p></p>
<hr>
<h1>Features</h1>
<p></p>
<hr>
<h1>Integrations</h1>
<p></p>
<hr>
<h1>Challenges</h1>
<p></p>
<hr>
<h1>Performance Optimizations</h1>
<p></p>
<hr>
<h1>Screens</h1>
<p>Describe important screenshots.</p>
<hr>
<h1>Proposal Notes</h1>
<p>Mention what AI should highlight in proposals.</p>
<hr>
<h1>Keywords</h1>
<p></p>
HTML;

    /**
     * Paginate projects, optionally filtered by a title search.
     *
     * @return LengthAwarePaginator<int, Project>
     */
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Project::query()
            ->withCount('images')
            ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->latest('updated_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new project.
     *
     * @param  array{title: string, description: string}  $data
     */
    public function create(array $data, ?UploadedFile $thumbnail = null): Project
    {
        return Project::query()->create([
            'title' => $data['title'],
            'slug' => $this->generateUniqueSlug($data['title']),
            'description' => $data['description'],
            'thumbnail' => $thumbnail ? $this->storeThumbnail($thumbnail) : null,
        ]);
    }

    /**
     * Update an existing project.
     *
     * @param  array{title: string, description: string}  $data
     */
    public function update(Project $project, array $data, ?UploadedFile $thumbnail = null): Project
    {
        $attributes = [
            'title' => $data['title'],
            'description' => $data['description'],
        ];

        if ($data['title'] !== $project->title) {
            $attributes['slug'] = $this->generateUniqueSlug($data['title'], $project->id);
        }

        if ($thumbnail) {
            $this->deleteThumbnail($project);

            $attributes['thumbnail'] = $this->storeThumbnail($thumbnail);
        }

        $project->update($attributes);

        return $project->refresh();
    }

    /**
     * Delete a project and its stored files.
     */
    public function delete(Project $project): void
    {
        $this->deleteThumbnail($project);

        Storage::disk('public')->delete($project->images->pluck('image')->all());

        $project->delete();
    }

    /**
     * Add an image to a project, placing it at the end of the gallery.
     */
    public function addImage(Project $project, string $title, UploadedFile $image): ProjectImage
    {
        return $project->images()->create([
            'title' => $title,
            'image' => $this->storePublicFile($image, 'projects/images'),
            'sort_order' => ((int) $project->images()->max('sort_order')) + 1,
        ]);
    }

    /**
     * Delete a project image and its stored file.
     */
    public function deleteImage(ProjectImage $image): void
    {
        Storage::disk('public')->delete($image->image);

        $image->delete();
    }

    /**
     * Reorder a project's images to match the given list of image IDs.
     *
     * @param  list<int>  $orderedIds
     */
    public function reorderImages(Project $project, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $imageId) {
            $project->images()->whereKey($imageId)->update(['sort_order' => $index]);
        }
    }

    /**
     * Generate a slug that is unique across all projects.
     */
    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'project';
        $slug = $base;
        $suffix = 2;

        while (Project::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    protected function storeThumbnail(UploadedFile $thumbnail): string
    {
        return $this->storePublicFile($thumbnail, 'projects/thumbnails');
    }

    /**
     * Store an uploaded file on the public disk.
     */
    protected function storePublicFile(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');

        if ($path === false) {
            throw new RuntimeException('Unable to store the uploaded file.');
        }

        return $path;
    }

    protected function deleteThumbnail(Project $project): void
    {
        if ($project->thumbnail) {
            Storage::disk('public')->delete($project->thumbnail);
        }
    }
}
