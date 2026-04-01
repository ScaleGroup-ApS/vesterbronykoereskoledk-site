<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferPageQuizQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\OfferPageQuizQuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'offer_page_id',
        'question',
        'options',
        'correct_option',
        'explanation',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_option' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(OfferPage::class, 'offer_page_id');
    }
}
