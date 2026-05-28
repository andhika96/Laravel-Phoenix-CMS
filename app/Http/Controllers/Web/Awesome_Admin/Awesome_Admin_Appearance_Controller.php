<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Http\Controllers\Controller;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Page_Theme;
use App\Models\Awesome_Admin\Page_Theme_Setting;

use App\Http\Controllers\Api\Base_API_Rev_Controller;

use App\Http\Resources\Awesome_Admin\PageThemeListResource;
use App\Http\Resources\Awesome_Admin\PageThemeSettingResource;

use Illuminate\Support\Str;
use Illuminate\Support\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

class Awesome_Admin_Appearance_Controller extends Base_API_Rev_Controller
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
		$page_themes = 
		[
			'login' 			=> $this->listDataPageTheme('login'),
			'signup' 			=> $this->listDataPageTheme('signup'),
			'forgotPassword' 	=> $this->listDataPageTheme('forgotpassword'),
			'resetPassword' 	=> $this->listDataPageTheme('resetpassword'),
		];

		$page_theme_settings = 
		[
			'login' 			=> $this->getPageThemeSetting('login'),
			'signup' 			=> $this->getPageThemeSetting('signup'),
			'forgotPassword' 	=> $this->getPageThemeSetting('forgotPassword'),
			'resetPassword' 	=> $this->getPageThemeSetting('resetPassword'),
		];

		$list_page_theme_settings = $this->listDataPageThemeSettings();

		return view('awesome_admin.awesome_admin_appearance', ['page_themes' => $page_themes, 'page_theme_settings' => $page_theme_settings, 'list_page_theme_settings' => $list_page_theme_settings]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 */
	public function show()
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit()
	{
		//
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
			$backgroundImages = [];
			$jsonDecode = json_decode($request->input('interface_theme'), true);
			$jsonDecodeInterfaceThemeOptions = json_decode($request->input('interface_theme_options'), true);

			foreach ($jsonDecode as $key => $value) 
			{
				if ($jsonDecode[$key] !== '')
				{
					Page_Theme_Setting::updateOrCreate(['uri' => $key], ['page_theme' => $jsonDecode[$key]]);
				}
			}

			foreach ($jsonDecodeInterfaceThemeOptions as $key => $value) 
			{
				if ($jsonDecodeInterfaceThemeOptions[$key] !== '')
				{
					Page_Theme_Setting::updateOrCreate(['uri' => $key], ['page_color_nuances' => $jsonDecodeInterfaceThemeOptions[$key]]);
				}
			}

			if ($request->hasFile('background_image'))
			{
				foreach ($request->file('background_image') as $key => $value) 
				{
					$pageThemeSettingDetails = Page_Theme_Setting::where('uri', $key)->first();

					if ($pageThemeSettingDetails['page_background_image'] !== NULL)
					{
						// Delete old image and replace with the above one
						if (Storage::disk('public')->exists($pageThemeSettingDetails['page_background_image']))
						{
							$this->deleteFile($pageThemeSettingDetails['page_background_image']);
						}
					}

					$backgroundImages[$key] = $this->uploadBackgroundImage($value);

					Page_Theme_Setting::updateOrCreate(['uri' => $key], ['page_background_image' => $backgroundImages[$key]]);
				}
			}

			// DB Commit if data successfully saved
			DB::commit();

			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'   => true,
					'status'    => 'success',
					'message'   => t('Data successfully updated')
					// 'message'   => $request->input('interface_theme')
					// 'message' 	=> print_r($request->file('background_image'))
				]);
			} 
			else 
			{
				$response = redirect()
				->back()
				->with('success', t('Data successfully updated'));
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

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy()
	{
		//
	}

	public function getPageThemeSetting($idOrSlug)
	{
		$result = Page_Theme_Setting::where('uri', $idOrSlug)->first();

		return $result;
	}

	public function getPageThemeSettingJson($idOrSlug)
	{
		$result = Page_Theme_Setting::where('uri', $idOrSlug)->first();

		if ($result)
		{
			return response()->json($result);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Color data not found')]);
	}

	public function listDataPageTheme($idOrSlug)
	{
		// $api = new Base_API_Rev_Controller();
		$result = Page_Theme::where('uri', $idOrSlug)->get();

		// $formattedResponse = $api->paginateResponse($result, PageThemeListResource::class);

		// $response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
		// 				->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $result;
	}

	public function listDataPageThemeSettings()
	{
		$result = Page_Theme_Setting::get();

		return $result;
	}

	public function uploadBackgroundImage($attribute, $path = 'images/page_themes')
	{
		if ( ! empty($attribute))
		{
			$file = $attribute;

			$directoryMonthYear	= date("mY", time());
			$subDirectoryDate	= 'date_'.date("d", time());
			
			// For database only without dot and slash at the front folder
			$x_folder = $path.'/'.$directoryMonthYear.'/'.$subDirectoryDate;

			if (is_string($attribute))
			{
				if ( ! preg_match('/^data:image\/(\w+);base64,/', $file))
				{
					return false;
				}

				$imageData 		= substr($file, strpos($file, ',') + 1);
				$decodedImage 	= base64_decode($imageData, true);

				if ( ! $decodedImage || ! $this->isValidImage($decodedImage))
				{
					return false;
				}

				// Get file type and extension before filtering
				$fileType   	= getFileType($file);
				$fileExtension  = explode('/', $fileType)[1];

				// Filtering image base64encode
				list($type, $file)	= explode(';', $file);
				list(, $file)		= explode(',', $file);

				// Get file temporary from temporary directory
	 			$temp_file_path = tempnam(sys_get_temp_dir(), 'contents'); // might not work on some systems, specify your temp path if system temp dir is not writeable
	 			file_put_contents($temp_file_path, base64_decode($file));

	 			// Create new file for image base64encode
				$_FILES['userFile']['name']			= uniqid().'.'.$fileExtension;
				$_FILES['userFile']['tmp_name']		= $temp_file_path;
				$_FILES['userFile']['size']			= filesize($temp_file_path);
				$_FILES['userFile']['type']			= $fileType;
				$_FILES['userFile']['extension']	= $fileExtension;
				$_FILES['userFile']['error']		= 0;

				$newFile = new UploadedFile($_FILES['userFile']['tmp_name'], $_FILES['userFile']['name'], $_FILES['userFile']['type']);
				
				// Create new request file				
				request()->files->set('userFile', $newFile);

				// New file and request file already created, now time to upload to server
				$uploadedFile 	= $this->uploadFile(request()->file('userFile'), $x_folder);
			}
			else
			{
				$uploadedFile 	= $this->uploadFile($file, $x_folder);
				$extension 		= $file->extension();
			}

			return $uploadedFile;
		}
	}

	protected function uploadFile($file, $path)
	{
		if ($file) 
		{
			$fileNameWithExt = $file->getClientOriginalName();
			$fileNameWithoutExt = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

			$fileName = random_string('md5').'.'.$file->getClientOriginalExtension();
			$resultUpload = $file->storeAs($path, $fileName, 'public');

			return $resultUpload;
		}

		return null;
	}

	protected function deleteFile($file)
	{
		if ($file) 
		{
			Storage::disk('public')->delete($file);
		}

		return null;
	}

	protected function isValidImage($data)
	{
		// Write your validation logic here
		// For example, you can use getimagesize or imagecreatefromstring
		// Here's a simple example using getimagesize:
		$imageInfo = getimagesizefromstring($data);

		return $imageInfo !== false;
	}
}
