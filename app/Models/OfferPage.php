<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class OfferPage extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\OfferPageFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'offer_module_id',
        'title',
        'body',
        'video_url',
        'sort_order',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
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
