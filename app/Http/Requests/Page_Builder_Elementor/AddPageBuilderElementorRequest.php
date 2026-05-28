<?php

namespace App\Http\Requests\Page_Builder_Elementor;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddPageBuilderElementorRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'pageName' => [['required'], 'unique:page_builder,page_name'],
			'pageStatus' => 'required',
		];
	}

	public function messages(): array
	{
		return [
			'pageName.required' => t('Page Name required'),
			'pageName.unique' => t('The Page Name is already in use by another page'),
			'pageStatus.required' => t('Page status required'),
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json([
			'success' => false,
			'status' => 'failed',
			'message' => $validator->errors(),
		], 422));
	}
}
