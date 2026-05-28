<?php

namespace App\Http\Requests\Awesome_Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddMenuRequest extends FormRequest
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
		$menuVarsFirst = json_decode($this->menu_vars_first, true);

		if ($menuVarsFirst['parent_type'] == '')
		{
			$data['menu_vars_first.parent_type'] = 'required';
		}

		if ($menuVarsFirst['parent_name'] == '')
		{
			$data['menu_vars_first.parent_name'] = 'required';
		}

		if ($menuVarsFirst['is_for_parent_menu'] == '')
		{
			$data['menu_vars_first.is_for_parent_menu'] = 'required';
		}

		if ($menuVarsFirst['is_for_parent_menu'] == 'single')
		{
			if ($menuVarsFirst['parent_link'] == '')
			{
				$data['menu_vars_first.parent_link'] = 'required';
			}
		}

		if (site_config()->management_menu == 'v1')
		{
			if (is_array($menuVarsFirst['parent_roles']) && $menuVarsFirst['parent_roles'] == [])
			{
				$data['menu_vars_first.parent_roles'] = 'required';
			}
		}

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
			'menu_vars_first.parent_type.required' => t('Parent type required'),
			'menu_vars_first.parent_name.required' => t('Parent name required'),
			'menu_vars_first.is_for_parent_menu.required' => t('Menu is single or parent required'),
			'menu_vars_first.parent_link.required' => t('Parent menu link required'),
			'menu_vars_first.parent_roles.required' => t('Parent roles link required'),
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
