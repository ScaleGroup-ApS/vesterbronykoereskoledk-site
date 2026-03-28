<?php

namespace App\Http\Requests\Bookings;

use App\Enums\DrivingSkill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateBookingSkillsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isInstructor();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'driving_skills' => ['present', 'array'],
            'driving_skills.*' => [new Enum(DrivingSkill::class)],
        ];
    }
}
