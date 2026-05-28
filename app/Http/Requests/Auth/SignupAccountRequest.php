<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SignupAccountRequest extends FormRequest
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
		$data['account.email'] = [['required'], 'unique:accounts,email'];
		$data['account.username'] = [['required'], 'unique:accounts,username'];
		$data['account.fullname'] = 'required|string|max:65';
		$data['account.password'] = 'required|min:6';
		// $data['g-recaptcha-response'] = 'required|recaptchav3:signup,0.5';

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
			'account.email.required' => t('Email address required'),
			'account.email.unique' => t('The email address is already in use by another user'),

			'account.username.required' => t('Username required'),
			'account.username.unique' => t('The username is already used by another user'),

			'account.fullname.required' => t('Fullname required'),
			'account.password.required' => t('Password required'),

			'account.password.required' => t('Password required'),
			'account.password.min' => t('The minimal password length is 6'),
			'account.password.regex' => t('Only alphabet, number and underscore is allowed'),

			// 'g-recaptcha-response.recaptchav3' => t('Captcha error message')
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
