<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Base_API_Rev_Controller;

use App\Http\Resources\User\UserListResource;
use App\Http\Resources\User\UserProfileResource;

use App\Http\Requests\Account\AddAccountRequest;
use App\Http\Requests\Account\EditAccountRequest;

use App\Models\Awesome_Admin\Roles;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Status;
use App\Models\Awesome_Admin\Account_Information;

use Carbon\Carbon;

use App\Events\CMS\UserRegistered;
use App\Events\CMS\UserUpdated;

class Awesome_Admin_User_Controller extends Base_API_Rev_Controller
{
	public function __construct(Account $user) 
	{
		$this->Account = new Account();

		if ( ! $user->isAdmin())
		{
			abort(403);
		}
	}

	/**
	 * Display a listing of the resource.
	 */

	public function index(Request $request)
	{
		$getListRoles = Roles::get();
		$getListAccountStatus = Account_Status::get();

		// Auto create folder for CKFinder
		// File::makeDirectory(storage_path('app/public/ckfinder/userfiles/'), 0755, true);

		return view('awesome_admin.awesome_admin_user', ['roles' => $getListRoles, 'account_status' => $getListAccountStatus]);
	}

	/**
	 * Store a newly created resource in storage.
	 */

	public function store(AddAccountRequest $request)
	{
		DB::beginTransaction();

		try 
		{
			// Insert to account table
			$attrDataAccount = $request->input('account');

			$inputAccount['uuid'] 		??= Str::uuid();
			$inputAccount['fullname'] 	??= $attrDataAccount['fullname'];
			$inputAccount['email'] 		??= $attrDataAccount['email'];

			if ($attrDataAccount['autofill_username'] == 'true')
			{
				$inputAccount['username'] ??= "u".random_string('numeric', 7);
			}
			elseif ($attrDataAccount['autofill_username'] == 'false')
			{
				$inputAccount['username'] ??= $attrDataAccount['username'];
			}
			
			if ($attrDataAccount['autoset_password'] == 'true')
			{
				$inputAccount['password'] ??= "LaraPhoenixDev";
			}
			elseif ($attrDataAccount['autoset_password'] == 'false')
			{
				$inputAccount['password'] ??= Hash::make($attrDataAccount['password']);
			}
			
			if ($attrDataAccount['status'] == 3)
			{
				$inputAccount['suspended_at'] ??= Carbon::now();
			}
			elseif ($attrDataAccount['status'] == 4)
			{
				$getDays = (int) $request->input('suspended_until');

				$inputAccount['suspended_until'] ??= Carbon::now()->addDays($getDays);
			}

			$inputAccount['status'] ??= $attrDataAccount['status'];

			$insertDataToAccount = Account::create($inputAccount);

			if ($insertDataToAccount)
			{
				$getDetailUser = Account::find($insertDataToAccount->id);
				$getDetailUser->syncRoles([$attrDataAccount['roles']]);

				// Auto create user information
				Account_Information::create(['user_id' => $insertDataToAccount->id]);

				// Auto create folder for CKFinder
				File::makeDirectory(storage_path('app/public/ckfinder/userfiles/'.$getDetailUser->uuid), 0755, true);

				DB::commit();

				// Broadcast real-time notification
				event(new UserRegistered(
					actorName: auth()->user()->fullname ?? auth()->user()->username,
					newUserName: $getDetailUser->fullname,
					newUserEmail: $getDetailUser->email,
					newUserRole: $attrDataAccount['roles'],
					createdAt: Carbon::now()->toDateTimeString(),
				));

				if ($request->wantsJson())
				{
					$response = response()->json(
					[
						'success'	=> true,
						'status' 	=> 'success',
						'message'	=> t('User successfully created')
					]);
				}
				else
				{
					$response = redirect()
						->back()
						->with('success', t('User successfully created'));
				}

				// return $response;
			}
			else
			{
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'	=> false,
						'status' 	=> 'failed',
						'message'	=> t('Error, cannot save data')
					]);
				} 
				else 
				{
					$response = redirect()
						->back()
						->with('failed', t('Error, cannot save data'));
				}

				DB::rollBack();
			}

			// return $response;
		} 
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> false,
					'status' 	=> 'failed',
					'message'	=> $th->getMessage()
				
				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->with('failed', $th->getMessage());
			}

			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 */

	public function edit(string $idOrSlug)
	{
		$getDetailUser = Account::with(['roles', 'getStatus'])->find($idOrSlug);

		if ( ! $getDetailUser)
		{			
			$getDetailUser = Account::with(['roles', 'getStatus'])->where('username', $idOrSlug)->first();
		}

		if ($getDetailUser)
		{
			return view('awesome_admin.awesome_admin_user_edit', ['data' => $getDetailUser]);
		}
		
		abort(404);
	}

	/**
	 * Update the specified resource in storage.
	 */

	public function update(EditAccountRequest $request)
	{
		try 
		{
			$getUserData = Account::find($request->input('user_id'));

			if ($getUserData)
			{
				// Insert to account table
				$attrDataAccount = $request->input('account');

				if ($getUserData['uuid'] == null)
				{
					$inputAccount['uuid'] ??= Str::uuid();
				}

				$inputAccount['username'] 	??= $attrDataAccount['username'];
				$inputAccount['fullname'] 	??= $attrDataAccount['fullname'];
				$inputAccount['email'] 		??= $attrDataAccount['email'];
				
				if ($attrDataAccount['password'])
				{
					$inputAccount['password'] ??= Hash::make($attrDataAccount['password']);
				}
				
				$inputAccount['status'] ??= $attrDataAccount['status'];

				if ($attrDataAccount['status'] == 3)
				{
					$inputAccount['suspended_at'] ??= Carbon::now();
					$inputAccount['suspended_until'] ??= NULL;
				}
				elseif ($attrDataAccount['status'] == 4)
				{
					$getDays = (int) $request->input('suspended_until');

					$inputAccount['suspended_at'] ??= NULL;
					$inputAccount['suspended_until'] ??= Carbon::now()->addDays($getDays);
				}				

				$getUserData->fill($inputAccount);

				if ($getUserData->save())
				{
					$getUserData->syncRoles([$attrDataAccount['roles']]);

					DB::commit();

					// Broadcast real-time notification
					event(new UserUpdated(
						actorName: auth()->user()->fullname ?? auth()->user()->username,
						targetUserName: $getUserData->fullname,
						targetUserEmail: $getUserData->email,
						updatedAt: Carbon::now()->toDateTimeString(),
						changedFields: array_keys($inputAccount),
					));

					if ($request->wantsJson())
					{
						$response = response()->json(
						[
							'success'	=> true,
							'status' 	=> 'success',
							'message'	=> t('User successfully updated')
						]);
					}
					else
					{
						$response = redirect()
							->back()
							->with('success', t('User successfully updated'));
					}
				}
				else
				{
					if ($request->wantsJson())
					{
						$response = response()->json(
						[
							'success'	=> false,
							'status' 	=> 'failed',
							'message'	=> t('Error, cannot save data')
						]);
					}
					else
					{
						$response = redirect()
							->back()
							->with('failed', t('Error, cannot save data'));
					}

					DB::rollBack();
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

			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	/**
	 * Bulk update the specified resource in storage.
	 */

	public function bulk_update(Request $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();

		try 
		{
			if ($request->input('mode') == 'bulk_update')
			{
				if ($request->input('changeStatus') == '' && $request->input('changeRole') == '')
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'	=> false,
							'status' 	=> 'failed',
							'message'	=> t("You haven't selected the options yet")
						]);
					} 
					else 
					{
						$response = redirect()
							->back()
							->with('failed', t("You haven't selected the options yet"));
					}
				}
				else
				{
					if (is_array($request->input('getSelected')))
					{
						foreach ($request->input('getSelected') as $key) 
						{
							if ($request->input('changeStatus') != '')
							{
								$getDetailUser = Account::find($key);
								
								if ($request->input('changeStatus') == 3)
								{
									$getDetailUser->fill(['status' => $request->input('changeStatus'), 'suspended_at' => Carbon::now(), 'suspended_until' => NULL]);
								}
								else if ($request->input('changeStatus') == 4)
								{
									$getDays = (int) $request->input('suspended_until');
									$getDetailUser->fill(['status' => $request->input('changeStatus'), 'suspended_at' => NULL, 'suspended_until' => Carbon::now()->addDays($getDays)]);
								}
								else
								{
									$getDetailUser->fill(['status' => $request->input('changeStatus'), 'suspended_at' => null]);
								}

								if ($getDetailUser->save())
								{
									// DB Commit if data successfully saved
									DB::commit();
								}

								// DB Rollback to get data back if fail to saving
								DB::rollBack();
							}

							if ($request->input('changeRole') != '')
							{
								$getDetailUser = Account::find($key);
								$getDetailUser->syncRoles([$request->input('changeRole')]);

								if ($getDetailUser->save())
								{
									// DB Commit if data successfully saved
									DB::commit();
								}

								// DB Rollback to get data back if fail to saving
								DB::rollBack();
							}
						}
					}

					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'	=> true,
							'status' 	=> 'success',
							'message'	=> t('User successfully updated')
						]);
					} 
					else 
					{
						$response = redirect()
							->back()
							->with('success', t('User successfully updated'));
					}
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

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		} 
		finally 
		{
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
					$getData = Account::where('username', $request->input('username'))->first();
				}

				if ($getData || $request->input('email') == '' && $request->input('username') == '')
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
					$response = response()->json(
					[
						'success'	=> true,
						'status' 	=> 'success',
						'message'	=> t('{1} available', $checkType[$request->input('checkType')]),
						'data'		=> $getData
					]);
				}
			}
			catch (\Throwable $th) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage(),

				], 500);
			} 
			finally 
			{
				return $response;
			}
		}

		return false;
	}

	public function checkUserDataForUpdate(Request $request)
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
					$getData = Account::where('username', $request->input('username'))->first();
				}

				if ($getData)
				{
					// dd($getData);

					if ($getData['email'] == $request->input('currentUserEmail') || $getData['username'] == $request->input('currentUsername') && $request->input('username'))
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
						$response = response()->json(
						[
							'success'	=> false,
							'status' 	=> 'failed',
							'message'	=> t('{1} not available', $checkType[$request->input('checkType')])
						]);
					}
				}
				else
				{
					if ($request->input('email') != '' || $request->input('username') != '')
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
						$response = response()->json(
						[
							'success'	=> false,
							'status' 	=> 'failed',
							'message'	=> t('{1} not available', $checkType[$request->input('checkType')])
						]);
					}
				}
			}
			catch (\Throwable $th) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage(),

				], 500);
			} 
			finally 
			{
				return $response;
			}
		}

		return false;
	}

	public function getUserData(Request $request)
	{
		$result = Account::with(['roles', 'getStatus'])->find($request->input('user_id'));

		if ($result)
		{
			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $result]);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => 'No data']);
	}

	public function listData(Request $request)
	{
		$api = new Base_API_Rev_Controller();
		$result = Account::with(['roles', 'getStatus'])->whereLike('email', '%'.$request->input('search').'%')->orWhereLike('fullname', '%'.$request->input('search').'%')->paginate(15);

		$formattedResponse = $api->paginateResponse($result, UserListResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}
}
