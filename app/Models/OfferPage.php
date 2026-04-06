<?php

namespace App\Models;

use Database\Factories\OfferPageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OfferPage extends Model implements HasMedia
{
    /** @use HasFactory<OfferPageFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'offer_module_id',
        'title',
        'body',
        'sort_order',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
        $this->addMediaCollection('banner')->singleFile();
        $this->addMediaCollection('video')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->extractVideoFrameAtSecond(1)
            ->performOnCollections('video')
            ->width(1280)
            ->height(720);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(OfferModule::class, 'offer_module_id');
    }

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(OfferPageQuizQuestion::class)->orderBy('sort_order');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(StudentPageProgress::class);
    }
}
