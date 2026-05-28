<?php

namespace App\Http\Requests\Awesome_Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmitSMTPRequest extends FormRequest
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
		return [
			'smtp_service' => 'required',
			'smtp_host' => 'required',
			'smtp_username' => 'required',
			'smtp_password' => 'required',
			'smtp_port' => 'required'
		];
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
			'smtp_service.required' => t('SMTP service required'),
			'smtp_host.required' => t('SMTP host required'),
			'smtp_username.required' => t('SMTP username required'),
			'smtp_password.required' => t('SMTP password required'),
			'smtp_port.required' => t('SMTP port required'),
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
