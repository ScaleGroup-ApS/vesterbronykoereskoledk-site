<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPageProgress extends Model
{
    /** @use HasFactory<\Database\Factories\StudentPageProgressFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'offer_page_id',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
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
