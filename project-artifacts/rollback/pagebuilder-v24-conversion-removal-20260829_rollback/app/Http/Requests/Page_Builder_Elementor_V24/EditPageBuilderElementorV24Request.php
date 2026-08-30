<?php

namespace App\Http\Requests\Page_Builder_Elementor_V24;

use App\Models\Page_Builder\Page_Builder;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class EditPageBuilderElementorV24Request extends FormRequest
{
	public function authorize(): bool
	{
		$page = $this->resolveRequestedPage();

		return ! $page || $page->editor_version === Page_Builder::EDITOR_VERSION_V24;
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
			'customJs' => ['nullable', 'string', 'max:102400'],
			'customJsMode' => ['nullable', 'string', Rule::in(['disabled', 'exact_sandbox', 'published'])],
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

	protected function failedAuthorization()
	{
		throw new HttpResponseException(response()->json([
			'success' => false,
			'status' => 'failed',
			'message' => t('This page belongs to a different editor version'),
			'editorVersion' => $this->resolveRequestedPage()?->editor_version,
		], 409));
	}

	private function resolveCurrentId(): ?int
	{
		$idOrSlug = $this->route('idOrSlug');

		if ($idOrSlug === null || $idOrSlug === '')
		{
			return null;
		}

		$page = Page_Builder::query()
			->where('editor_version', Page_Builder::EDITOR_VERSION_V24)
			->where(fn ($query) => $query
				->where('uri', $idOrSlug)
				->orWhere('id', $idOrSlug))
			->first();

		return $page?->id;
	}

	private function resolveRequestedPage(): ?Page_Builder
	{
		$idOrSlug = $this->route('idOrSlug');

		if ($idOrSlug === null || $idOrSlug === '')
		{
			return null;
		}

		return Page_Builder::query()
			->where(fn ($query) => $query
				->where('uri', $idOrSlug)
				->orWhere('id', $idOrSlug))
			->first();
	}
}
