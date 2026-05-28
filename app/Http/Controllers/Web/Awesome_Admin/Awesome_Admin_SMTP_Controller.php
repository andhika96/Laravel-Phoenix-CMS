<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Awesome_Admin\SubmitSMTPRequest;
use App\Http\Requests\Awesome_Admin\SubmitSMTPSetServiceRequest;
use App\Http\Resources\Awesome_Admin\SMTPListResource;

use App\Models\Awesome_Admin\SMTP;
use App\Models\Awesome_Admin\SMTP_Service;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Awesome_Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Awesome_Admin_SMTP_Controller extends Controller
{
	public function __construct(Account $user)
	{
		if ( ! $user->isAdmin())
		{
			abort(403);
		}
	}

	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$data['service_list'] = SMTP::get();
		$data['service_set'] = SMTP_Service::find(1);

		return view('awesome_admin.awesome_admin_smtp', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		// Empty
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(SubmitSMTPRequest $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();

		try
		{
			if ($request->validated())
			{
				$smtp = SMTP::create($request->all());

				DB::commit();

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('Data successfully created')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully created'));
				}
			}   
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> false,
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

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	/**
	* Display the specified resource.
	*/
	public function show(Awesome_Admin $awesome_Admin)
	{
		// Empty
	}

	/**
	* Show the form for editing the specified resource.
	*/
	public function edit(string $idOrSlug)
	{
		// Empty
	}

    /**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try 
		{
			$getDetailSMTP = SMTP::find($request->input('data_id'));
			$getDetailSMTP->fill($request->only(['smtp_service', 'smtp_host', 'smtp_username', 'smtp_password', 'smtp_port', 'smtp_encryption', 'smtp_sender_name', 'smtp_sender_address']));

			if ($getDetailSMTP->save())
			{
				// DB Commit if data successfully saved
				DB::commit();
			
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'   => true,
						'status'    => 'success',
						'message'   => t('Data successfully updated')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully updated'));
				}
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
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

	/**
     * Update the specified resource in storage.
     */
	public function update_service(SubmitSMTPSetServiceRequest $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try 
		{
			$getSMTPService = SMTP_Service::find(1);
			
			if ($getSMTPService)
			{
				$getDetailSMTPService = SMTP::find($request->input('service_id'));

				$getSMTPService->fill(['service_id' => $request->input('service_id'), 'service_name' => $getDetailSMTPService->smtp_service]);

				if ($getSMTPService->save())
				{
					// DB Commit if data successfully saved
					DB::commit();
				
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'   => true,
							'status'    => 'success',
							'message'   => t('Data successfully updated')
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('success', t('Data successfully updated'));
					}
				}
			}
			else
			{
				$getDetailSMTPService 		= SMTP::find($request->input('service_id'));
				$createSetNewSMTPService 	= SMTP_Service::create(['service_id' => $request->input('service_id'), 'service_name' => $getDetailSMTPService->smtp_service]);

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'   => true,
						'status'    => 'success',
						'message'   => t('Data successfully updated')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully updated'));
				}

				// DB Commit if data successfully saved
				DB::commit();
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
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

	public function listData()
	{
		$api = new BaseApiController();
		$getSMTPList = SMTP::paginate();
	
		$formattedResponse = $api->paginateResponse($getSMTPList, SMTPListResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Request $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try
		{
			$smtp = SMTP::find($request->route('idOrSlug'));

			if ($smtp)
			{
				$smtp->delete(); 

				DB::commit();

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('Data successfully deleted')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully deleted'));
				}
			}
			else
			{
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> false,
						'status'		=> 'failed',
						'message'		=> t('Data not found')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('failed', t('Data not found'));
				}

				// DB Rollback to get data back if fail to saving
				DB::rollBack();
			}
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

	public function detailData($role_id)
	{
		$role = SMTP::find($role_id);

		if ($role !== null)
		{
			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $role]);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
	}

	public function detailDataSetService()
	{
		$SMTPService = SMTP_Service::find(1);

		if ($SMTPService !== null)
		{
			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $SMTPService->service_name]);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
	}
}
