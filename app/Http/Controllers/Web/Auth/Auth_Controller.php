<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginAccountRequest;
use App\Http\Requests\Auth\SignupAccountRequest;
use App\Http\Requests\Auth\RecoveryAccountPasswordRequest;

use App\Mail\ForgotPasswordMail;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Status;
use App\Models\Awesome_Admin\Account_Information;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Validation\ValidationException;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

use Laravel\Socialite\Facades\Socialite;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;

use Mailjet\LaravelMailjet\Facades\Mailjet;
use Mailjet\Resources;

use Carbon\Carbon;

class Auth_Controller extends Controller
{
	public function __construct()
	{
		// We manually set timezone to Asia Jakarta, Indonesia
		date_default_timezone_set('Asia/Jakarta');
	}

	public function login(Request $request)
	{
		if (Auth::check())
		{
			return redirect()->intended('/dashboard');
		}

		if (url()->previous() !== url()->current())
		{
			$urlPrevious 		= url()->previous();
			$explodeUrlPrevious = explode('/', $urlPrevious);
			$getFourthPathUrl 	= isset($explodeUrlPrevious[3]) ? $explodeUrlPrevious[3] : '';
			
			if ($getFourthPathUrl == 'auth' || $getFourthPathUrl == 'awesome_admin')
			{
				session(['link' => url('dashboard')]);
			}
			else
			{
				session(['link' => url()->previous()]);
			}
		}

		$dynamicView = 'auth.'.custom_page_theme('login').'.login';

		return view($dynamicView);
	}

	public function authenticate(LoginAccountRequest $request)
	{	
		if (Auth::attempt($request->validated())) 
		{
			$continueToSignin = 0;

			if (site_config()->enable_recaptcha_signin == 0 &&
				site_config()->recaptcha_site_key !== null &&
				site_config()->recaptcha_secret_key !== null)
			{				
				$score = RecaptchaV3::verify($request->get('g-recaptcha-response'), 'login');

				if ($score > env('RECAPTCHAV3_DEFAULT_SCORE', 0.5))
				{
					$continueToSignin = 0;
				}
				else
				{
					$continueToSignin = 1;

					Auth::logout();

					if (site_config()->enable_ratelimit_login == 0)
					{
						RateLimiter::clear($this->throttleKey($request));
					}
				 
					return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Failed to log in, our reCAPTCHA system detected you as a bot, please try again later or log in with another device')]);
				}
			}

			if ($continueToSignin == 0)
			{
				// Regerenate session for user login
				$request->session()->regenerate();

				// Create session for CKFinder Plugin
				$request->session()->put('LaraCKFinder_UserRole', Auth::user()->roles[0]['name']);

				if (session('link') !== null)
				{
					$getDestinationPage = session('link');
				}
				else
				{
					$getDestinationPage = url('dashboard');
				}

				if (site_config()->enable_ratelimit_login == 0)
				{
					RateLimiter::clear($this->throttleKey($request));
				}

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status' 		=> 'success',
						'message'		=> t('User login successfully'),
						'redirect_url' 	=> url('dashboard')
					]);

					return response()->json(['success' => true, 'status' => 'success', 'message' => 'Success', 'redirect_url' => $getDestinationPage]);
				} 
				else 
				{
					return redirect($getDestinationPage);
				}
			}
		}

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

				return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Too many attempts to login, please wait {1} to try again.', $getTimeRateLimit)]);
			}

			RateLimiter::hit($this->throttleKey($request), site_config()->time_ratelimit_global);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => t('The email address or password you entered is incorrect, please try again')]);
	}

	public function signup()
	{
		$dynamicView = 'auth.'.custom_page_theme('signup').'.signup';

		return view($dynamicView);
	}

	public function signupProcess(SignupAccountRequest $request)
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

					return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Failed to signup, our reCAPTCHA system detected you as a bot, please try again later or signup with another device')]);
					// return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Default Score: '.env('RECAPTCHAV3_DEFAULT_SCORE', '').' Result Score: '.$score]);			
				}
			}

			if ($continueToSignup == 0)
			{
				// Insert to account table
				$attrDataAccount = $request->input('account');

				$new_data['uuid']			??= Str::uuid();
				$new_data['username']		??= $attrDataAccount['username'];
				$new_data['fullname']		??= $attrDataAccount['fullname'];
				$new_data['email']			??= $attrDataAccount['email'];
				$new_data['password'] 		??= Hash::make($attrDataAccount['password']);
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

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status' 		=> 'success',
						'message'		=> t('User created successfully'),
						'redirect_url' 	=> url('dashboard')
					]);
				} 
				else 
				{
					$response = redirect()
						->back()
						->with('success', t('User created successfully'));
				}
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

					return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Too many attempts to signup, please wait {1} to try again.', $getTimeRateLimit)]);
				}

				RateLimiter::hit($this->throttleKey($request), site_config()->time_ratelimit_global);
			}
		} 
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->withInput()
					->with('error', $th->getMessage());
			}
		} 
		finally 
		{
			return $response;
		}
	}

	public function logout(Request $request): RedirectResponse
	{
		Auth::logout();
	 
		$request->session()->invalidate();
	 
		$request->session()->regenerateToken();
	 
		return redirect('/auth');
	}

	public function logoutSSO(Request $request) 
	{
		Auth::guard()->logout();

		$request->session()->flush();

		$azureLogoutUrl = Socialite::driver('azure')->getLogoutUrl(route('login'));

		return redirect($azureLogoutUrl);
	}

	public function forgotpassword(Request $request)
	{
		if (Auth::check())
		{
			return redirect()->intended('/dashboard');
		}

		// Create duration code using carbon nestbot
		// Default duration is 5 minutes
		$createCodeDuration = Carbon::now()->addMinutes(1)->format('Y-m-d H:i:s');
		$createCodeDurationAlt = date("Y-m-d H:i:s", strtotime("5 minutes"));

		$dynamicView = 'auth.'.custom_page_theme('forgotPassword').'.forgotpassword';

		return view($dynamicView, ['durationCode' => strtotime($createCodeDuration)]);
	}

	public function forgotpasswordProcessOnlyMailjetService(Request $request)
	{
		$getDetailUser = Account::where('email', $request->input('email'))->first();

		if ($getDetailUser)
		{
			// Get random code for token reset password
			$randomCode = random_string('alnum', 22);

			// Create duration code using carbon nestbot
			// Default duration is 5 minutes
			$createCodeDuration = Carbon::now()->addMinutes(1)->format('Y-m-d H:i:s');
			$createCodeDurationAlt = date("Y-m-d H:i:s", strtotime("5 minutes"));

			$getDetailUser->fill(['recovery_code' => $randomCode, 'recovery_code_duration' => Carbon::parse($createCodeDuration)->timestamp]);

			if ($getDetailUser->save())
			{
				$mj = Mailjet::getClient();

				$body = 
				[
					'FromEmail' 	=> Config::get('services.mailjet.from_address'),
					'FromName' 		=> t('LaraPhoenix Recovery Account'),
					'Subject' 		=> t('Recovery Account'),
					'Recipients' 	=> [['Email' => $request->input('email')]],
					// 'Text-part' => "Testing email from Pohon Uang 2",
					'HTML-part' => 'Click the link below to reset your password, and your password will expire in 1 minutes, after 1 minutes you will not be able to access the link <br/><br/> '.url('auth/recoveryaccount?code='.$randomCode),
				];

				$response = $mj->post(Resources::$Email, ['body' => $body]);

				if ($response->success())
				{
					// return response()->json(['success' => true, 'status' => 'success', 'message' => $response->getData()]);
					return response()->json(['success' => true, 'status' => 'success', 'message' => t('The password reset link has been sent to your email, please check your inbox or email spam folder')]);
				} 
				else 
				{
					return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Failed to send')]);
				}

				// return response()->json(['success' => true, 'status' => 'success', 'message' => t('The password reset link has been sent to your email, please check your inbox or email spam folder')]);
			}
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Email not found in any account')]);
	}

	public function forgotpasswordProcess(Request $request)
	{
		$getDetailUser = Account::where('email', $request->input('email'))->first();

		if ($getDetailUser)
		{
			// Get random code for token reset password
			$randomCode = random_string('alnum', 22);

			// Create duration code using carbon nestbot
			// Default duration is 5 minutes
			$createCodeDuration = Carbon::now()->addMinutes(5)->format('Y-m-d H:i:s');
			$createCodeDurationAlt = date("Y-m-d H:i:s", strtotime("5 minutes"));

			$getDetailUser->fill(['recovery_code' => $randomCode, 'recovery_code_duration' => Carbon::parse($createCodeDuration)->timestamp]);

			if ($getDetailUser->save())
			{
				$mailer = Mail::build(
				[
					'transport' 	=> 'smtp',
					'host' 			=> smtp_service()->smtp_host,
					'port' 			=> smtp_service()->smtp_port,
					'encryption' 	=> smtp_service()->smtp_encryption,
					'username' 		=> smtp_service()->smtp_username,
					'password' 		=> smtp_service()->smtp_password,
					'timeout' 		=> 5
				]);

				try
				{
					$mailData = 
					[
						'subject'		=> t('Recovery Account'),
						'link_button'	=> url('auth/recoveryaccount?code='.$randomCode)
					];

		 			$mailer->alwaysFrom(smtp_service()->smtp_sender_address, smtp_service()->smtp_sender_name);
					$mailer->to($request->input('email'))->send(new ForgotPasswordMail($mailData));

					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> true,
							'status' 		=> 'success',
							'message'		=> t('The password reset link has been sent to your email, please check your inbox or email spam folder'),
						]);
					} 
					else 
					{
						$response = redirect()
							->back()
							->with('success', t('The password reset link has been sent to your email, please check your inbox or email spam folder'));
					}
				}
				catch (Exception $e) 
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success' 	=> false,
							'status'	=> 'failed',
							'message' 	=> $th->getMessage()

						], 500);
					} 
					else 
					{
						$response = redirect()
							->back()
							->withInput()
							->with('error', $th->getMessage());
					}
				}
				finally 
				{
					return $response;
				}

				/*
				if ($response->success())
				{
					// return response()->json(['success' => true, 'status' => 'success', 'message' => $response->getData()]);
					return response()->json(['success' => true, 'status' => 'success', 'message' => t('The password reset link has been sent to your email, please check your inbox or email spam folder')]);
				} 
				else 
				{
					return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Failed to send')]);
				}
				*/

				// return response()->json(['success' => true, 'status' => 'success', 'message' => t('The password reset link has been sent to your email, please check your inbox or email spam folder')]);
			}
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Email not found in any account')]);
	}

	public function resetpassword(Request $request)
	{
		if (Auth::check())
		{
			return redirect()->intended('/dashboard');
		}
		
		$getDetailUser = Account::where('recovery_code', $request->input('code'))->first();

		if ($getDetailUser)
		{
			if ($getDetailUser['recovery_code_duration'] < time())
			{
				abort(419);
			}

			$dynamicView = 'auth.'.custom_page_theme('resetPassword').'.resetpassword';

			return view($dynamicView, ['data' => $getDetailUser]);
		}

		abort(404);
	}

	public function resetpasswordProcess(RecoveryAccountPasswordRequest $request)
	{
		$getDetailUser = Account::where('recovery_code', $request->input('code'))->first();

		$update_data = [];

		if ($getDetailUser && $getDetailUser['recovery_code'] !== NULL)
		{
			try
			{
				if ($getDetailUser['recovery_code_duration'] < time())
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success' 	=> false,
							'status'	=> 'failed',
							'message' 	=> t('Cannot reset password, because your link is expired, please resend code to your email')
						]);
					} 
					else 
					{
						$response = redirect()
							->back()
							->with('failed', t('Cannot reset password, because your link is expired, please resend code to your email'));
					}

					return $response;
				}

					DB::beginTransaction();

					$update_data['recovery_code']			??= NULL;
					$update_data['recovery_code_duration']	??= NULL;
					$update_data['password'] 				??= Hash::make($request->input('password'));

					$getDetailUser->fill($update_data);

					if ($getDetailUser->save())
					{
						// DB Commit if data successfully saved
						DB::commit();

						if ($request->wantsJson()) 
						{
							$response = response()->json(
							[
								'success' 	=> true,
								'status'	=> 'success',
								'message' 	=> t('Update user password successfully')
							]);
						} 
						else 
						{
							$response = redirect()
								->back()
								->with('success', t('Update user password successfully'));
						}
					}
					else
					{
						DB::rollBack();

						if ($request->wantsJson()) 
						{
							$response = response()->json(
							[
								'success' 	=> false,
								'status'	=> 'failed',
								'message' 	=> t('Cannot reset password, please contact administrator immediately')
							]);
						} 
						else 
						{
							$response = redirect()
								->back()
								->with('failed', t('Cannot reset password, please contact administrator immediately'));
						}
					}
			}
			catch (\Throwable $th) 
			{
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success' 	=> false,
						'status'	=> 'failed',
						'message' 	=> $th->getMessage()

					], 500);
				} 
				else 
				{
					$response = redirect()
						->back()
						->withInput()
						->with('error', $th->getMessage());
				}
			} 
			finally 
			{
				return $response;
			}
		}
		else
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'		=> false,
					'status' 		=> 'failed',
					'message'		=> t('Recovery code not found')
				]);
			} 
			else 
			{
				$response = redirect()
					->back()
					->with('false', t('Recovery code not found'));
			}

			return $response;
		}
	}

	public function checkUserData(Request $request)
	{
		if ($request->input('checkType') !== '')
		{
			$checkType = 
			[
				'email'		=> 'Email',
				'username'  => 'Username'
			];

			try
			{
				if ($request->input('checkType') == 'email')
				{
					$rules = 
					[
						'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/']
					];

					$validator = Validator::make($request->all(), $rules);

					if ($validator->fails()) 
					{
						if ($request->wantsJson()) 
						{
							$response = response()->json(
							[
								'success'	=> false,
								'status' 	=> 'failed',
								'message'	=> t('{1} not available', $checkType[$request->input('checkType')])
							]);
						} 
						else 
						{
							$response = redirect()
								->back()
								->with('success', t('{1} not available', $checkType[$request->input('checkType')]));
						}

						return $response;
					}
					else
					{
						$getData = Account::where('email', $request->input('email'))->first();
					}
				}
				elseif ($request->input('checkType') == 'username')
				{
					$rules = 
					[
						'username' => ['required', 'regex:/^[a-zA-Z0-9_]+$/', 'min:6']
					];

					$validator = Validator::make($request->all(), $rules);

					if ($validator->fails()) 
					{
						if ($request->wantsJson()) 
						{
							$response = response()->json(
							[
								'success'	=> false,
								'status' 	=> 'failed',
								'message'	=> t('{1} not available', $checkType[$request->input('checkType')])
							]);
						} 
						else 
						{
							$response = redirect()
								->back()
								->with('success', t('{1} not available', $checkType[$request->input('checkType')]));
						}

						return $response;
					}
					else
					{
						$getData = Account::where('username', $request->input('username'))->first();
					}
				}

				if ($getData)
				{
					// dd($getData);

					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'	=> false,
							'status' 	=> 'failed',
							'message'	=> t('{1} not available', $checkType[$request->input('checkType')])
						]);
					} 
					else 
					{
						$response = redirect()
							->back()
							->with('success', t('{1} not available', $checkType[$request->input('checkType')]));
					}
				}
				else
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'	=> true,
							'status' 	=> 'success',
							'message'	=> t('{1} available', $checkType[$request->input('checkType')]),
							'data'		=> $getData
						]);
					} 
					else 
					{
						$response = redirect()
							->back()
							->with('success', t('{1} available', $checkType[$request->input('checkType')]));
					}
				}
			}
			catch (\Throwable $th) 
			{
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success' 	=> false,
						'status'	=> 'failed',
						'message' 	=> $th->getMessage(),

					], 500);
				} 
				else 
				{
					$response = redirect()
						->back()
						->withInput()
						->with('error', $th->getMessage());
				}
			} 
			finally 
			{
				return $response;
			}
		}

		return false;
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

		// throw ValidationException::withMessages(
		// [
		// 	'email' => trans('auth.throttle', 
		// 	[
		// 		'seconds' => $seconds,
		// 		'minutes' => ceil($seconds / 60),
		// 	]),
		// ]);

		return $seconds;
	}

	/**
	 * Get the rate limiting throttle key for the request.
	 */

	protected function throttleKey($request): string
	{
		// return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
		// return Str::transliterate(Str::lower($request->ip()));

		return Str::transliterate(Str::lower(url()->current().'|'.$request->ip()));
	}
}

?>