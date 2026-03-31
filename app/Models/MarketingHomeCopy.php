<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingHomeCopy extends Model
{
    protected $fillable = [
        'key',
        'hero_headline_prefix',
        'hero_headline_accent',
        'hero_subtitle',
        'why_title',
        'why_lead',
        'reviews_title',
        'reviews_lead',
        'reviews_footnote',
        'explore_title',
        'explore_lead',
        'cta_title',
        'cta_lead',
    ];

    public static function forHome(): self
    {
        return static::query()->where('key', 'home')->firstOrFail();
    }
}
