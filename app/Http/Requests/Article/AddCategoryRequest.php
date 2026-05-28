<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddCategoryRequest extends FormRequest
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

		$data['category_name'] = [ ['required'], 'unique:article_categories,name' ];
		$data['category_status'] = 'required';

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
			'category_name.required' => t('Category name required'),
			'category_name.unique' => t('Category name already exists'),
			'category_status.required' => t('Category status required')
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->streamJson(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
