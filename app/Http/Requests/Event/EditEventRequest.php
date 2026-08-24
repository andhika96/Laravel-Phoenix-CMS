<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class EditEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->route('idOrSlug');

        return [
            'title' => ['required', 'string', 'max:255'],
            'uri' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/', Rule::unique('events', 'uri')->ignore($eventId)],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:event_categories,id'],
            'publication_status' => ['required', Rule::in(['draft', 'published', 'hidden'])],
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'reminder_lead_minutes' => ['nullable', 'integer', 'min:0'],
            'cancel_cutoff_minutes' => ['nullable', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:15000'],
            'remove_thumbnail' => ['nullable', 'boolean'],
            'thumbnail_source' => ['nullable', Rule::in(['upload', 'ckfinder'])],
            'thumbnail_ckfinder_url' => ['nullable', 'required_if:thumbnail_source,ckfinder', 'string', 'max:2048', 'regex:#^(?:https?://[^/]+)?/storage/ckfinder/events/[A-Za-z0-9._/-]+$#'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => t('Title required'),
            'content.required' => t('Content required'),
            'uri.regex' => t('Slug may contain only letters, numbers, and hyphens.'),
            'uri.unique' => t('Slug already exists'),
            'thumbnail.image' => t('The uploaded file must be a valid image.'),
            'thumbnail.max' => t('The thumbnail must not be larger than 15MB.'),
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
