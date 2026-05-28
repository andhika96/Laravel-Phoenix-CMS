<?php

namespace App\Http\Requests\Setup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;

class SetupRequest extends FormRequest
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

		$data['db_name'] = 'required';
		$data['db_username'] = 'required';
		$data['db_password'] = 'required';
		$data['db_hostname'] = 'required';
		$data['db_port'] = 'required';

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
			'db_name.required' => t('Database name required'),
			'db_username.required' => t('Database username required'),
			'db_password.required' => t('Database password type required'),
			'db_hostname.required' => t('Database hostname required'),
			'db_port.required' => t('Database port required')
		];
	}

	protected function failedValidation(Validator $validator)
	{
		// $output = new Command();
		// $output = new ConsoleOutput();
		// throw new HttpResponseException($output->writeIn('asdasd'));

		$buf = new BufferedOutput();
		$buf->writeln($validator->errors());
		$out = $buf->fetch();

		// http_response_code(422);

		return response()->stream(function() use($validator)
		{
			throw new HttpResponseException($validator->errors());

		}, 422, 
		[
			'Content-Type' => 'text/event-stream',
			'Cache-Control' => 'no-cache, no-transform',
			'X-Accel-Buffering' => 'no',
		]);

		// throw new HttpResponseException(response($out)->setStatusCode(422));
		// return response("<pre>{$out}</pre>");
		// throw new HttpResponseException($out);

		
		// throw new HttpResponseException();

		// throw new HttpResponseException(response()->streamJson(['success' => false, 'status' => 'failed', 'message' => $out], 422));
	}
}
