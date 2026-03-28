<?php

namespace App\Models;

use App\Enums\OfferType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Offer extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\OfferFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'type',
        'theory_lessons',
        'driving_lessons',
        'track_required',
        'slippery_required',
        'requires_theory_exam',
        'requires_practical_exam',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'type' => OfferType::class,
            'theory_lessons' => 'integer',
            'driving_lessons' => 'integer',
            'track_required' => 'boolean',
            'slippery_required' => 'boolean',
            'requires_theory_exam' => 'boolean',
            'requires_practical_exam' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Offer $offer) {
            if (blank($offer->slug) && filled($offer->name)) {
                $offer->slug = static::uniqueSlugFromName($offer->name);
            }
        });
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->first()
            ?? $this->where('id', $value)->first();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function uniqueSlugFromName(string $name): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'pakke';
        }
        $slug = $base;
        $i = 2;
        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('materials');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)->withPivot('assigned_at');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /** @param Builder<Offer> $query */
    public function scopePrimary(Builder $query): void
    {
        $query->where('type', OfferType::Primary);
    }

    /** @param Builder<Offer> $query */
    public function scopeAddon(Builder $query): void
    {
        $query->where('type', OfferType::Addon);
    }

    public function isPrimary(): bool
    {
        return $this->type === OfferType::Primary;
    }
}
