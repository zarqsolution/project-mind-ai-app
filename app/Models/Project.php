<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $thumbnail
 * @property string $description
 * @property array<int, float>|null $embedding
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $thumbnail_url
 * @property-read bool $has_embedding
 * @property-read Collection<int, ProjectImage> $images
 */
#[Fillable(['title', 'slug', 'thumbnail', 'description', 'embedding'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['thumbnail_url', 'has_embedding'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['embedding'];

    /**
     * Use the slug for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the images for the project, ordered for display.
     *
     * @return HasMany<ProjectImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    /**
     * Get the public URL for the thumbnail.
     *
     * @return Attribute<string|null, never>
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->thumbnail
                ? Storage::disk('public')->url($this->thumbnail)
                : null,
        );
    }

    /**
     * Determine if the project has a stored embedding.
     *
     * @return Attribute<bool, never>
     */
    protected function hasEmbedding(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->embedding !== null,
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'embedding' => 'array',
        ];
    }
}
