<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class EventOccurrenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locationMode = $this->input('location_mode');

        return [
            'label' => ['nullable', 'string', 'max:150'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'timezone' => ['required', 'timezone'],
            'location_mode' => ['required', Rule::in(['offline', 'online', 'hybrid'])],
            'location_text' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'online_url' => [Rule::requiredIf(in_array($locationMode, ['online', 'hybrid'], true)), 'nullable', 'url', 'max:2048'],
            'registration_open_at' => ['nullable', 'date', 'before_or_equal:registration_close_at'],
            'registration_close_at' => ['nullable', 'date', 'before:starts_at'],
            'capacity' => ['required', 'integer', 'min:1'],
            'lifecycle_status' => ['sometimes', Rule::in(['scheduled', 'cancelled', 'completed'])],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->streamJson([
            'success' => false,
            'status' => 'failed',
            'message' => $validator->errors(),
        ], 422));
    }
}
