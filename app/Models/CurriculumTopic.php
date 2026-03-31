<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumTopic extends Model
{
    /** @use HasFactory<\Database\Factories\CurriculumTopicFactory> */
    use HasFactory;

    protected $fillable = ['offer_id', 'lesson_number', 'title', 'description'];

    protected function casts(): array
    {
        return ['lesson_number' => 'integer'];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
