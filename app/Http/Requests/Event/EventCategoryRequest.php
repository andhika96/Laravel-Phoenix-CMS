<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class EventCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->input('idOrSlug') ?: $this->route('idOrSlug');

        return [
            'category_name' => ['required', 'string', 'max:64', Rule::unique('event_categories', 'name')->ignore($categoryId)],
            'category_code' => ['nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/', Rule::unique('event_categories', 'code')->ignore($categoryId)],
            'category_status' => ['required', Rule::in(['active', 'inactive', 'hide'])],
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
