<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Base_API_Rev_Controller;

use App\Http\Requests\Api\Auth\LoginAccountRequest;
use App\Http\Requests\Api\Auth\SignupAccountRequest;
use App\Http\Requests\Api\Auth\RecoveryAccountPasswordRequest;

use App\Mail\ForgotPasswordMail;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Status;
use App\Models\Awesome_Admin\Account_Information;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Validation\ValidationException;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class Auth_Controller extends Controller
{
	public function __construct()
	{
		$this->jsonResponse = new Base_API_Rev_Controller();
	}

	/**
	 * @authenticate
	 */
	public function authenticate(LoginAccountRequest $request): JsonResponse
	{
		try 
		{	
			if (Auth::attempt($request->validated())) 
			{
				if (site_config()->enable_ratelimit_login == 0)
				{
					RateLimiter::clear($this->throttleKey($request));
				}

				$user = $request->user();
				$token = $user->createToken('api')->accessToken;

				$response = $this->jsonResponse->respondOK(
				[
					'success'		=> true,
					'status' 		=> 'success',
					'message'		=> t('User login successfully'),
					'data'			=> $user,
					'token'			=> $token
				]);
			}
			else
			{
				if (site_config()->enable_ratelimit_login == 0)
				{
					if ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_login) !== false)
					{			
						if ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_login) < 2)
						{
							$getTimeRateLimit = $this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_login).' '.t('second');
						}
						elseif ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_login) < 60)
						{
							$getTimeRateLimit = $this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_login).' '.t('seconds');
						}
						elseif ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_login) >= 60)
						{
							$getTimeRateLimit = ceil($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_login) / 60).' '.t('minute');
						}
						elseif ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_login) >= 120)
						{
							$getTimeRateLimit = ceil($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_login) / 60).' '.t('minutes');
						}

						$response = $this->jsonResponse->respondUnauthorized(['message' => t('Too many attempts to login, please wait {1} to try again.', $getTimeRateLimit)]);
					}
					else
					{
						$response = $this->jsonResponse->respondUnauthorized(['message' => t('The email address or password you entered is incorrect, please try again')]);
					}

					RateLimiter::hit($this->throttleKey($request), site_config()->time_ratelimit_global);
				}
				else
				{
					$response = $this->jsonResponse->respondUnauthorized(['message' => t('The email address or password you entered is incorrect, please try again')]);
				}
			}	
		} 
		catch (\Throwable $th) 
		{
			$response = $this->jsonResponse->respondInternalError(['message' => $th->getMessage()]);
		} 
		finally 
		{
			return $response;
		}
	}

	/**
	 * Signup process 
	 */
	
	public function signupProcess(SignupAccountRequest $request): JsonResponse
	{
		DB::beginTransaction();	

		try 
		{
			$continueToSignup = 0;

			if (site_config()->enable_recaptcha_signup == 0 &&
				site_config()->recaptcha_site_key !== null &&
				site_config()->recaptcha_secret_key !== null)
			{
				$score = RecaptchaV3::verify($request->get('g-recaptcha-response'), 'login');

				if ($score > env('RECAPTCHAV3_DEFAULT_SCORE', ''))
				{
					$continueToSignup = 0;
				}
				else
				{
					$continueToSignup = 1;

					Auth::logout();

					$response = $this->jsonResponse->respondUnauthorized(
					[
						'success'		=> false,
						'status' 		=> 'failed',
						'message'		=> t('Failed to signup, our reCAPTCHA system detected you as a bot, please try again later or signup with another device')
					]);

					// return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Default Score: '.env('RECAPTCHAV3_DEFAULT_SCORE', '').' Result Score: '.$score]);			
				}
			}

			if ($continueToSignup == 0)
			{
				$new_data['uuid']			??= Str::uuid();
				$new_data['username']		??= $request->input('username');
				$new_data['fullname']		??= $request->input('fullname');
				$new_data['email']			??= $request->input('email');
				$new_data['password'] 		??= Hash::make($request->input('password'));
				$new_data['status'] 		??= 3;

				$createAccount = Account::create($new_data);

				if ($createAccount)
				{
					$getDetailUser = Account::find($createAccount->id);
					$getDetailUser->syncRoles(['General Member']);

					// Auto create user information
					Account_Information::create(['user_id' => $createAccount->id]);

					// Auth to auto login after signup
					Auth::login($createAccount);

					// DB Commit if data successfully saved
					DB::commit();
				}

				$response = $this->jsonResponse->respondOK(
				[
					'success'		=> true,
					'status' 		=> 'success',
					'message'		=> t('User created successfully')
				]);
			}

			if (site_config()->enable_ratelimit_signup == 0)
			{
				if ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_signup) !== false)
				{			
					if ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_signup) < 2)
					{
						$getTimeRateLimit = $this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_signup).' '.t('second');
					}
					elseif ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_signup) < 60)
					{
						$getTimeRateLimit = $this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_signup).' '.t('seconds');
					}
					elseif ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_signup) >= 60)
					{
						$getTimeRateLimit = ceil($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_signup) / 60).' '.t('minute');
					}
					elseif ($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_signup) >= 120)
					{
						$getTimeRateLimit = ceil($this->ensureIsNotRateLimited($request, site_config()->amount_ratelimit_signup) / 60).' '.t('minutes');
					}

					$response = $this->jsonResponse->respondUnauthorized(['message' => t('Too many attempts to login, please wait {1} to try again.', $getTimeRateLimit)]);
				}

				RateLimiter::hit($this->throttleKey($request), site_config()->time_ratelimit_global);
			}
		} 
		catch (\Throwable $th) 
		{
			$response = $this->jsonResponse->respondInternalError(['line_code' => $th->getLine(), 'message' => $th->getMessage()]);
		} 
		finally 
		{
			return $response;
		}
	}

	/**
	 * Ensure the login request is not rate limited.
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */

	protected function ensureIsNotRateLimited($request, $amount_ratelimit)
	{
		if ( ! RateLimiter::tooManyAttempts($this->throttleKey($request), $amount_ratelimit)) 
		{
			return false;
		}

		event(new Lockout($request));

		$seconds = RateLimiter::availableIn($this->throttleKey($request));

		return $seconds;
	}

	/**
	 * Get the rate limiting throttle key for the request.
	 */

	protected function throttleKey($request): string
	{
		return Str::transliterate(Str::lower(url()->current().'|'.$request->ip()));
	}
}
