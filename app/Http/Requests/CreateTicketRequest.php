<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'priority' => ['required', 'string', 'in:low,normal,high,urgent'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'Skriv et emne.',
            'subject.max' => 'Emnet må højst være 255 tegn.',
            'message.required' => 'Skriv en besked.',
            'priority.required' => 'Vælg en prioritet.',
            'priority.in' => 'Vælg en gyldig prioritet.',
        ];
    }
}
