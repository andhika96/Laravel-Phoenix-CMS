<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Site_Config;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Awesome_Admin_Config_Controller extends Controller
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
		$getDetailSiteConfig = Site_Config::find(1);

		return view('awesome_admin.awesome_admin_config', ['data' => $getDetailSiteConfig, 'getListFont' => $this->listDataFonts(), 'getListFontCss' => $this->listDataFileFonts()]);
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
	public function store()
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
			$getDetailSiteConfig = Site_Config::find(1);

			if ($getDetailSiteConfig)
			{
				$getRequest = $request->all();			
				$getRequest['enable_ratelimit_login'] = $request->has('enable_ratelimit_login') ? 0 : 1;
				$getRequest['enable_ratelimit_signup'] = $request->has('enable_ratelimit_signup') ? 0 : 1;

				// If you upload a file we can detect
				if ( ! empty($getRequest['file']))
				{
					// Automatic set and upload the file
					if ( ! isset($getRequest['site_thumbnail']))
					{
						$getRequest['site_thumbnail'] = $this->upload_thumbnail($getRequest);
					}

					// Delete old image and replace with the above one
					if (Storage::disk('public')->exists('thumbnail', $getDetailSiteConfig->site_thumbnail))
					{
						$this->deleteFile($getDetailSiteConfig->site_thumbnail, 'thumbnail');
					}
				}

				$getDetailSiteConfig->fill($getRequest);

				if ($getDetailSiteConfig->save())
				{
				    // DB Commit if data successfully saved
				    DB::commit();
				
					if ($request->wantsJson()) 
					{
						if ($request->has('enable_ratelimit_login'))
						{
							$getValue = 0;
						}
						else
						{
							$getValue = 1;
						}

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
		$getDetailSiteConfig = Site_Config::find(1);

		return response()->json(['success' => true, 'status' => 'success', 'data' => $getDetailSiteConfig]);
	}

	public function listDataFonts()
	{
		$directories = Storage::directories('public/fonts');
		$output = [];

		$i = 0;
		foreach ($directories as $key => $value) 
		{
			$fontName[$i] = Str::replace('public/fonts/', '', $value); 
			$fontName[$i] = Str::replace('-', ' ', $fontName[$i]); 
			$fontName[$i] = Str::replace('_', ' ', $fontName[$i]); 
			$fontName[$i] = Str::ucwords($fontName[$i]); 

			$fontCode[$i] = Str::replace('public/fonts/', '', $value); 

			$output[$i]['name'] = $fontName[$i];
			$output[$i]['code'] = $fontCode[$i];

			$i++;
		}

		// return $output;
		return response()->json(['success' => true, 'status' => 'success', 'data' => $output]);
	}

	public function listDataFileFonts()
	{
		$files = Storage::allFiles('public/fonts');
		$output = [];

		$i = 0;
		foreach ($files as $key => $value) 
		{
			if (Str::of($value)->containsAll(['fonts', 'css'], ignoreCase: true) == true)
			{
				$stringChanged = Str::swap(['public' => 'storage'], $value);

				// $output[$i] = $string;
				$output[$i] = '<link href="'.asset($stringChanged.'?v=').time().'" rel="stylesheet">';

				$i++;
			}
		}

		return $output;
		// return response()->json(['success' => true, 'status' => 'success', 'data' => $output]);
	}

	public function listDataFileFontsBySlug($idOrSlug)
	{
		$files = Storage::allFiles('public/fonts/'.$idOrSlug);
		$output = [];

		$i = 0;
		foreach ($files as $key => $value) 
		{
			$output[$i] = $value;

			$i++;
		}

		return response()->json(['success' => true, 'status' => 'success', 'data' => $output]);
	}

	public function listDataFileFontForPreview($idOrSlug)
	{
		$files = Storage::allFiles('public/fonts/'.$idOrSlug);
		$output = [];

		$i = 0;
		foreach ($files as $key => $value) 
		{
			if (Str::of($value)->containsAll(['regular', 'woff2'], ignoreCase: true) == true)
			{
				$output[$i] = $value;

				$i++;
			}
		}

		return response()->json(['success' => true, 'status' => 'success', 'data' => $output]);
	}

	public function upload_thumbnail(array $attr)
	{
		if ( ! empty($attr['file']))
		{
			$file = $attr['file'];
			$path = 'thumbnail';

			if (is_string($attr['file']))
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

				$uploadedFile 	= Helper::decodeBase64($file, $path, $attr['key'], "tmp", null, 2);
				$fileType   	= Helper::getFileType($file);
				$fileExtension  = explode('/', $fileType)[1];
			}
			else
			{
				$uploadedFile 	= $this->uploadFile($file, $path);
				$extension 		= $file->extension();
			}

			return $uploadedFile;
		}
	}

	public function getImageURL($path = 'photos', $fileImage = '')
	{
		if (Storage::disk('public')->exists($path.'/'.$fileImage))
		{
			return Storage::url($path.'/'.$fileImage);
		}

		return null;
	}

	protected function uploadFile($file, $type = "photos")
	{
		if ($file) 
		{
			$fileName = uniqid("{$type}_").'.'.$file->getClientOriginalExtension();
			$file->storeAs("{$type}", $fileName, 'public');

			return $fileName;
		}

		return null;
	}

	protected function deleteFile($file, $type = "photos")
	{
		if ($file) 
		{
			Storage::disk('public')->delete("{$type}/{$file}");
		}

		return null;
	}

	private function isValidImage($data)
	{
		// Write your validation logic here
		// For example, you can use getimagesize or imagecreatefromstring
		// Here's a simple example using getimagesize:
		$imageInfo = getimagesizefromstring($data);

		return $imageInfo !== false;
	}

	public function testURL()
	{
		// $url = Storage::url('thumbnail/thumbnail_668f8eda8ba90.png');

		// if (Storage::disk('public')->exists('thumbnail/thumbnail_668f8eda8ba90.png'))
		// {
		// 	echo '<img src="'.$url.'" style="max-width: 100%">';
		// }
		// else
		// {
		// 	echo 'Image not found';
		// }

		// echo Storage::disk('public')->exists('thumbnail/thumbnail_668f8eda8ba90.png');

		// // echo '<img src="'.$url.'" style="max-width: 100%">';
		// echo $url;
		// exit;

		echo $this->getImageURL('thumbnail', 'thumbnail_668f8eda8ba90.png');
		exit;
	}
}
