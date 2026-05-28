<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddAccountRequest extends FormRequest
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
		/*
		return [
			'account.fullname' => 'required|string|max:255',
			'account.username' => 
			[
				['required'], 'unique:accounts,username',
			],
			'account.email' => 
			[
				['required'], 'unique:accounts,email',
			],
			'account.password' => 'min:6'
		];
		*/

		$data['account.fullname'] = 'required|string|max:65';
		
		if ($this->account['autofill_username'] == false)
		{
			$data['account.username'] = [ ['required'], 'unique:accounts,username' ];
		}
		
		$data['account.email'] = [ ['required'], 'unique:accounts,email' ];
		$data['account.password'] = 'min:6';

		return $data;
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array<string, string>
	 */
	public function messages(): array
	{
		// return 
		// [
		// 	'account.email.required' => t('Dibutuhkan alamat email'),
		// 	'account.email.unique' => t('Alamat email sudah digunakan')

		// 	'account.username.required' => t('Dibutuhkan nama pengguna'),
		// 	'account.username.unique' => t('Nama pengguna sudah digunakan'),

		// 	'account.fullname.required' => t('Dibutuhkan nama lengkap'),
		// 	'account.password.required' => t('Kata sandi dibutuhkan'),

		// 	'account.roles.required' => t('Dibutuhkan peran (role) akun'),
		// 	'account.status.required' => t('Dibutuhkan status akun'),
		// ];

		return 
		[
			'account.email.required' => t('Email address required'),
			'account.email.unique' => t('The email address is already in use by another user'),

			'account.username.required' => t('Username required'),
			'account.username.unique' => t('The username is already used by another user'),

			'account.fullname.required' => t('Fullname required'),
			'account.password.required' => t('Password required'),

			'account.roles.required' => t('Role required'),
			'account.status.required' => t('Status required'),
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
