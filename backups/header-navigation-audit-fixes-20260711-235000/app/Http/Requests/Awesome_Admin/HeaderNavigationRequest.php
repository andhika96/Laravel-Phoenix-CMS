<?php

namespace App\Http\Requests\Awesome_Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class HeaderNavigationRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'is_active' => 'required|boolean',
			'config_json' => 'required|array',
			'config_json.source' => 'required|string|max:255',
			'config_json.colors' => 'required|array',
			'config_json.layout' => 'required|array',
			'config_json.behavior' => 'required|array',
			'config_json.effects' => 'required|array',
			'config_json.sizing' => 'required|array'
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
