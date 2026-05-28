<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RecoveryAccountPasswordRequest extends FormRequest
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
		$output = 
		[
			'password' => 
			[	
				'required',
				'confirmed',
				Password::min(6)
							->mixedCase()
							->numbers()
							->symbols()
							->uncompromised()
			]
		];

		return $output;
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
			'password.required' => t('New password required'),
			'password.confirmed' => t('Re-type new password required'),
			'password.min' => t('Minimal password character is {1}', 6),
			'password.mixed' => t('Require at least one uppercase and one lowercase letter'),
			'password.numbers' => t('Require at least one number'),
			'password.symbols' => t('Require at least one symbol'),
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json(['success' => false, 'status' => 'failed', 'message' => $validator->errors()], 422));
	}
}
