<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditArticleRequest extends FormRequest
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
		$data['thumbnail'] = [...$this->isPrecognitive() ? [] : ['image', 'mimes:jpg,jpeg,png,webp', 'max:15000']];
		$data['remove_thumbnail'] = ['nullable', 'boolean'];
		$data['thumbnail_source'] = ['nullable', Rule::in(['upload', 'ckfinder'])];
		$data['thumbnail_ckfinder_url'] = ['nullable', 'required_if:thumbnail_source,ckfinder', 'string', 'max:2048', 'regex:#^(?:https?://[^/]+)?/storage/ckfinder/articles/[A-Za-z0-9._/-]+$#'];

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
			'content.required' => t('Content required')
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->streamJson(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
