<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectService
{
    public const string DESCRIPTION_TEMPLATE = <<<'MARKDOWN'
# Overview

Describe the project.

---

# Client

---

# Problem

---

# Solution

---

# My Role

---

# Technologies

---

# Features

---

# Integrations

---

# Challenges

---

# Performance Optimizations

---

# Screens

Describe important screenshots.

---

# Proposal Notes

Mention what AI should highlight in proposals.

---

# Keywords

MARKDOWN;

    /**
     * Paginate projects, optionally filtered by a title search.
     *
     * @return LengthAwarePaginator<int, Project>
     */
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Project::query()
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

        $project->delete();
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
        return $thumbnail->store('projects/thumbnails', 'public');
    }

    protected function deleteThumbnail(Project $project): void
    {
        if ($project->thumbnail) {
            Storage::disk('public')->delete($project->thumbnail);
        }
    }
}
