<?php

namespace App\Models;

use App\Enums\EnrollmentPaymentMethod;
use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentRequest extends Model
{
    protected $fillable = [
        'student_id',
        'offer_id',
        'payment_method',
        'status',
        'stripe_session_id',
        'rejection_reason',
        'approved_by_id',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => EnrollmentPaymentMethod::class,
            'status' => EnrollmentStatus::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
