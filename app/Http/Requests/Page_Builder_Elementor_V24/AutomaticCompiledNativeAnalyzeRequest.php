<?php

namespace App\Http\Requests\Page_Builder_Elementor_V24;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AutomaticCompiledNativeAnalyzeRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $viewports = $this->input('viewports');
        if (is_string($viewports)) {
            $decoded = json_decode($viewports, true);
            if (is_array($decoded)) {
                $this->merge(['viewports' => $decoded]);
            }
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source' => ['required', 'file', 'mimes:html,htm,zip', 'max:20480'],
            'framework' => ['nullable', 'string', Rule::in(['auto', 'plain', 'plain_css', 'tailwind', 'bootstrap5', 'bootstrap_css'])],
            'entry' => ['nullable', 'string', 'max:255'],
            'viewports' => ['nullable', 'array', 'max:3'],
            'viewports.*.name' => ['required', 'string', Rule::in(['desktop', 'tablet', 'mobile'])],
            'viewports.*.width' => ['required', 'integer', 'min:320', 'max:3840'],
            'viewports.*.height' => ['required', 'integer', 'min:240', 'max:2160'],
        ];
    }
}
