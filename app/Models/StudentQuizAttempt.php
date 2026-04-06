<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentQuizAttempt extends Model
{
    /** @use HasFactory<\Database\Factories\StudentQuizAttemptFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'offer_page_id',
        'answers',
        'score',
        'total',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'score' => 'integer',
            'total' => 'integer',
            'attempted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(OfferPage::class, 'offer_page_id');
    }
}
