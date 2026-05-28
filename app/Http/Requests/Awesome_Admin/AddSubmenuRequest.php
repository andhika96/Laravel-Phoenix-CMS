<?php

namespace App\Http\Requests\Awesome_Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddSubmenuRequest extends FormRequest
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
		$menuVarsFirst = json_decode($this->submenu_vars_first, true);

		if ($menuVarsFirst['submenu_name'] == '')
		{
			$data['submenu_vars_first.submenu_name'] = 'required';
		}

		if ($menuVarsFirst['submenu_type'] == '')
		{
			$data['submenu_vars_first.submenu_type'] = 'required';
		}

		if ($menuVarsFirst['submenu_link'] == '')
		{
			$data['submenu_vars_first.submenu_link'] = 'required';
		}

		if (site_config()->management_menu == 'v1')
		{
			if (is_array($menuVarsFirst['submenu_roles']) && $menuVarsFirst['submenu_roles'] == [])
			{
				$data['submenu_vars_first.submenu_roles'] = 'required';
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
			'submenu_vars_first.submenu_name.required' => t('Submenu name required'),
			'submenu_vars_first.submenu_type.required' => t('Submenu type required'),
			'submenu_vars_first.submenu_link.required' => t('Submenu link required'),
			'submenu_vars_first.submenu_roles.required' => t('Submenu roles link required'),
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
