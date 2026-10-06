<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
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
        ];
    }

    /**
     * A category has many services.
     *
     * @return HasMany<Service, $this>
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * A category has many search history entries predicted for it.
     *
     * @return HasMany<SearchHistory, $this>
     */
    public function searchHistories(): HasMany
    {
        return $this->hasMany(SearchHistory::class, 'predicted_category_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Use the slug in route model binding (e.g. admin/kategori/{category}).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * A small presentational icon for the service directory. Purely
     * cosmetic — falls back to a generic icon for categories an admin
     * creates later, so this never needs updating when categories change.
     */
    public function icon(): string
    {
        return match ($this->slug) {
            'jadwal-perkuliahan' => '🗓️',
            'krs' => '📝',
            'nilai-akademik' => '📊',
            'pembayaran-kuliah' => '💳',
            'pendaftaran-sidang' => '📋',
            'wisuda' => '🎓',
            'administrasi-akademik' => '🗂️',
            'surat-akademik' => '✉️',
            'kemahasiswaan' => '📢',
            'informasi-perkuliahan' => 'ℹ️',
            default => '🔗',
        };
    }

    /**
     * Auto-generate a unique slug from the name when none is provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
