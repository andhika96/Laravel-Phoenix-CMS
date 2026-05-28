<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddArticleRequest extends FormRequest
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

		$data['title'] = 'required';
		$data['content'] = 'required';

		if ($this->visibility == 'password_protected')
		{
			$data['password_protected'] = 'required';
		}

		$data['thumbnail'] = [...$this->isPrecognitive() ? [] : ['image', 'mimes:jpg,png,webp', 'max:15000']];

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
			'title.required' => t('Title required'),
			'content.required' => t('Content required'),
			'password_protected.required' => t('Password protected required'),
			'thumbnail.image' => t('The uploaded file must be a valid image {1}', '(e.g. JPG, PNG, WEBP).'),
			'thumbnail.max' => t('The {1} must not be larger than 15MB.', ':attribute')
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->streamJson(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
