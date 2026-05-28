<?php

namespace App\Http\Requests\Api\Auth;

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
		$data['email'] = [['required'], 'unique:accounts,email'];
		$data['username'] = [['required'], 'unique:accounts,username'];
		$data['fullname'] = 'required|string|max:65';
		$data['password'] = 'required|min:6';
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
			'email.required' => t('Email address required'),
			'email.unique' => t('The email address is already in use by another user'),

			'username.required' => t('Username required'),
			'username.unique' => t('The username is already used by another user'),

			'fullname.required' => t('Fullname required'),
			'password.required' => t('Password required'),

			'password.required' => t('Password required'),
			'password.min' => t('The minimal password length is 6'),
			'password.regex' => t('Only alphabet, number and underscore is allowed'),

			// 'g-recaptcha-response.recaptchav3' => t('Captcha error message')
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
