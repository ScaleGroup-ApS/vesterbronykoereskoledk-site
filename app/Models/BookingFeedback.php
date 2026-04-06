<?php

namespace App\Models;

use Database\Factories\BookingFeedbackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingFeedback extends Model
{
    /** @use HasFactory<BookingFeedbackFactory> */
    use HasFactory;

    protected $table = 'booking_feedback';

    protected $fillable = [
        'booking_id',
        'student_id',
        'rating',
        'comment',
        'confidence_scores',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'confidence_scores' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
