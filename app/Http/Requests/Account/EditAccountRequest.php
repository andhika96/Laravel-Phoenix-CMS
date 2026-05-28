<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditAccountRequest extends FormRequest
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
		$user = auth()->user();

		$userId = $user->isAdmin() ? $this->route('idOrSlug') : $user->id;

		return [
			'fullname' => 'required|string|max:255',
			'username' => 'required|string|max:255',
			'email' => [
				'required',
				'string',
				'email',
				'max:255',
				Rule::unique('accounts')->ignore($userId),
			],
			'password' => 'min:6|confirmed'
		];
		*/

		/*
		return [
			'account.fullname' => 'required|string|max:65',
			'account.username' => 
			[
				['required'], 'unique:accounts,username,'.$this->user_id,
			],
			'account.email' => 
			[
				['required'], 'unique:accounts,email,'.$this->user_id,
			],
			'account.roles' => 'required',
			'account.status' => 'required',
			'account.password' => 'min:6'
		];
		*/

		$data['account.email']      = [ ['required'], 'unique:accounts,email,'.$this->user_id.'' ];
		$data['account.username']   = [ ['required'], 'unique:accounts,username,'.$this->user_id.'' ];
		$data['account.fullname']   = 'required|string|max:65';
		$data['account.roles']   	= 'required';
		$data['account.status']  	= 'required';

		if ($this->account['password'] != '')
		{
			$data['account.password']   = 'required|min:6';
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
		// return 
		// [
		//  'account.email.required' => t('Dibutuhkan alamat email'),
		//  'account.email.unique' => t('Alamat email sudah digunakan')

		//  'account.username.required' => t('Dibutuhkan nama pengguna'),
		//  'account.username.unique' => t('Nama pengguna sudah digunakan'),

		//  'account.fullname.required' => t('Dibutuhkan nama lengkap'),
		//  'account.password.required' => t('Kata sandi dibutuhkan'),

		//  'account.roles.required' => t('Dibutuhkan peran (role) akun'),
		//  'account.status.required' => t('Dibutuhkan status akun'),
		// ];

		return 
		[
			'account.email.required' => t('Email address required'),
			'account.email.unique' => t('The email address is already in use by another user'),

			'account.username.required' => t('Username required'),
			'account.username.unique' => t('The username is already used by another user'),

			'account.fullname.required' => t('Fullname required'),
			
			'account.password.required' => t('Password required'),
			'account.password.min' => t('The minimum password character length is 6'),

			'account.roles.required' => t('Role required'),
			'account.status.required' => t('Status required'),
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
