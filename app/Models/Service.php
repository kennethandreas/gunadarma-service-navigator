<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'capabilities',
        'url',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'capabilities' => 'array',
        ];
    }

    /**
     * Use the slug in route model binding (e.g. /layanan/{service:slug}).
     *
     * Note: this does NOT restrict binding to active services — that's
     * intentional, so admin routes can still open inactive services to
     * re-enable them. Public controllers must apply the `active` scope
     * themselves (see ServiceController@show).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * A service belongs to a category.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A service can be referenced by many search history entries.
     *
     * @return HasMany<SearchHistory, $this>
     */
    public function searchHistories(): HasMany
    {
        return $this->hasMany(SearchHistory::class);
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Auto-generate a unique slug from the name when none is provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Service $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }
}
