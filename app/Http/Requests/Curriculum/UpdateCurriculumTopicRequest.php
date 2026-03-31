<?php

namespace App\Http\Requests\Curriculum;

use App\Models\CurriculumTopic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurriculumTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var CurriculumTopic $topic */
        $topic = $this->route('topic');

        return [
            'lesson_number' => ['required', 'integer', 'min:1', 'max:50',
                Rule::unique('curriculum_topics')
                    ->where('offer_id', $topic->offer_id)
                    ->ignore($topic->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
