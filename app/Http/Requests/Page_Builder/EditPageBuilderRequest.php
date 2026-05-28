<?php

namespace App\Http\Requests\Page_Builder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditPageBuilderRequest extends FormRequest
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
		$getPageName = $this->pageName;

		$data = [];

		$data['pageName'] = [ ['required'], Rule::unique('page_builder', 'page_name')->ignore($this->idOrSlug) ];
		$data['pageStatus'] = 'required';

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
			'pageName.required' 	=> t('Page Name required'),
			'pageName.unique' 		=> t('The Page Name is already in use by another page'),
			'pageStatus.required' 	=> t('Page status required')
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->streamJson(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
