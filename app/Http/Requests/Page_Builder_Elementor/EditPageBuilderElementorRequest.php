<?php

namespace App\Http\Requests\Page_Builder_Elementor;

use App\Models\Page_Builder\Page_Builder;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class EditPageBuilderElementorRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		$currentId = $this->resolveCurrentId();

		return [
			'pageName' => [
				['required'],
				Rule::unique('page_builder', 'page_name')->ignore($currentId),
			],
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

	private function resolveCurrentId(): ?int
	{
		$idOrSlug = $this->route('idOrSlug');

		if (! $idOrSlug)
		{
			return null;
		}

		$page = Page_Builder::query()
			->where('uri', $idOrSlug)
			->orWhere('id', $idOrSlug)
			->first();

		return $page?->id;
	}
}
