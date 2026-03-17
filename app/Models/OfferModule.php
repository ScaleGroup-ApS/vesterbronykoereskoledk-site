<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferModule extends Model
{
    /** @use HasFactory<\Database\Factories\OfferModuleFactory> */
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'title',
        'sort_order',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(OfferPage::class)->orderBy('sort_order');
    }
}
