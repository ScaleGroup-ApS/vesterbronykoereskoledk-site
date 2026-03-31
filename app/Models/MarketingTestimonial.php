<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingTestimonial extends Model
{
    protected $fillable = [
        'quote',
        'author_name',
        'author_detail',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
