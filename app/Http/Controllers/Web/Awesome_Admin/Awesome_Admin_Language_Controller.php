<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\SubmitSMTPRequest;
use App\Http\Resources\Awesome_Admin\SMTPListResource;

use App\Models\Awesome_Admin\Language;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Awesome_Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

// use Symfony\Component\HttpFoundation\Cookie;

class Awesome_Admin_Language_Controller extends Controller
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
		$data['getLanguage'] = $this->getLanguage();
		$data['listLanguageHTML'] = $this->listLanguageHTML();

		return view('awesome_admin.awesome_admin_language', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		// Empty
	}

	public function untranslated(Account $user)
	{
		if ($user->isAdmin())
		{
			return view('awesome_admin.awesome_admin_language_untranslated');
		}
 
		abort(403);
	}

	public function translated(Account $user)
	{
		if ($user->isAdmin())
		{
			return view('awesome_admin.awesome_admin_language_translated');
		}
 
		abort(403);
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
				$role = SMTP::create($request->all());

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
	public function update_untranslated(Request $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try 
		{
			if (is_array($request->input('lang')))
			{
				foreach ($request->input('lang') as $key) 
				{
					$getDataLanguage = Language::find($key['id']);
					$getDataLanguage->fill(['lang_to' => $key['to']]);

					if ($getDataLanguage->save())
					{
						// DB Commit if data successfully saved
						DB::commit();
					}
				}

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
			else
			{
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'   => false,
						'status'    => 'failed',
						'message'   => t('Data not updated')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('failed', t('Data not updated'));
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
	 * Update the specified resource in storage.
	 */
	public function update_translated(Request $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try 
		{
			if (is_array($request->input('lang')))
			{
				foreach ($request->input('lang') as $key) 
				{
					$getDataLanguage = Language::find($key['id']);
					$getDataLanguage->fill(['lang_to' => $key['to']]);

					if ($getDataLanguage->save())
					{
						// DB Commit if data successfully saved
						DB::commit();
					}
				}

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'	=> true,
						'status'	=> 'success',
						'message'	=> t('Data successfully updated')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully updated'));
				}
			}
			else
			{
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'	=> false,
						'status'	=> 'failed',
						'message'	=> t('Data not updated')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('failed', t('Data not updated'));
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

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	public function listData()
	{
		$api = new BaseApiController();
		$getSMTPList = Language::paginate();
	
		$formattedResponse = $api->paginateResponse($getSMTPList, SMTPListResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}

	public function listDataUntranslated()
	{
		$api = new BaseApiController();
		$getSMTPList = Language::where('lang', $this->getLanguage())->where('lang_to', '')->paginate();
	
		$formattedResponse = $api->paginateResponse($getSMTPList, SMTPListResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}

	public function listDataTranslated()
	{
		$api = new BaseApiController();
		$getSMTPList = Language::where('lang', $this->getLanguage())->where('lang_to', '!=', '')->paginate();
	
		$formattedResponse = $api->paginateResponse($getSMTPList, SMTPListResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}

	public function detailData($role_id)
	{
		$role = SMTP::find($role_id);

		if ($role)
		{
			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $role]);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
	}

	public function listLanguageHTML()
	{
		$allowed_language = ['english', 'indonesian'];

		foreach ($allowed_language as $key) 
		{
			if ($key == $this->getLanguage())
			{
				$lang_list[] = '<i class="font-weight-bold">'.ucfirst($key).'</i>';
			}
			else
			{
				$lang_list[] = '<a href="'.url('awesome_admin/language/setlanguage/'.$key).'">'.ucfirst($key).'</a>';
			}

			$lang_select = implode(' <span class="mx-1">|</span> ', $lang_list);
		}

		return $lang_select;
	}

	public function getLanguage()
	{
		if (request()->cookie('phoenix_language'))
		{
			$language = request()->cookie('phoenix_language');
		}
		else
		{
			$language = 'english';
		}

		return $language;
	}

	public function setLanguage(Request $request, $language_key = '')
	{
		try
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> true,
					'status'	=> 'success',
					'message' 	=> t('Language successfully updated')

				], 200)->cookie()->forever('phoenix_language', $language_key);
			} 
			else 
			{
				$response = redirect()
				->back()
				->with('success', t('Language successfully updated'))
				->withCookie(cookie()->forever('phoenix_language', $language_key));
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
}
