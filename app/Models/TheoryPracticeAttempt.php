<?php

namespace App\Models;

use Database\Factories\TheoryPracticeAttemptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TheoryPracticeAttempt extends Model
{
    /** @use HasFactory<TheoryPracticeAttemptFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'score',
        'total',
        'duration_seconds',
        'answers',
        'question_ids',
        'attempted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'question_ids' => 'array',
            'attempted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
