<?php

namespace App\Http\Requests\CoverImage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditCoverImageRequest extends FormRequest
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
		$data = [];

		$data['cover_type'] = 'required';
		$data['cover_page_name'] = 'required';
		// $data['uri'] = [['required'], 'unique:cover_image,uri'];

		// if ($this->visibility == 'password_protected')
		// {
		// 	$data['password_protected'] = 'required';
		// }

		// $data['thumbnail'] = [...$this->isPrecognitive() ? [] : ['image', 'mimes:jpg,png,webp', 'max:15000']];

		return $data;
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array<string, string>
	 */
	public function messages(): array
	{
		return 
		[
			'uri.required' => t('URI or Slug required'),
			'uri.unique' => t('The URI or Slug is already in use by another cover image'),
			'cover_type.required' => t('Cover type required'),
			'cover_page_name.required' => t('Cover page name required'),
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->streamJson(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
