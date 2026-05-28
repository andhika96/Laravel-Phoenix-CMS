<?php

namespace App\Http\Controllers\Web\Manage_CoverImage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Base_API_Rev_Controller;

use App\Http\Resources\Manage_CoverImage\ManageCoverImageListResource;
use App\Http\Resources\Manage_CoverImage\ManageCoverImageEditResource;

use App\Models\Awesome_Admin\Account;

use App\Models\CoverImage\CoverImage;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;
use Illuminate\Support\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\CoverImage\AddCoverImageRequest;
use App\Http\Requests\CoverImage\EditCoverImageRequest;

use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManagerStatic as Image;

class Manage_CoverImage_Controller extends Controller
{
	public function __construct()
	{
		// We manually set timezone to Asia Jakarta, Indonesia
		date_default_timezone_set('Asia/Jakarta');
	}

	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return view('manage_coverimage.manage_coverimage');
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function add(Request $request)
	{
		return view('manage_coverimage.manage_coverimage_add');
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(AddCoverImageRequest $request)
	{
		if ($request->validated())
		{
			DB::beginTransaction();	

			try
			{
				$cover_slideshow_vars = [];

				$new_data['uri']								??= $request->input('uri');
				$new_data['cover_type']							??= $request->input('cover_type');
				$new_data['cover_page_name']					??= $request->input('cover_page_name');
				$new_data['cover_slideshow_direction']			??= ($request->input('cover_slideshow_direction') !== null) ? $request->input('cover_slideshow_direction') : 'horizontal';
				$new_data['cover_slideshow_desktop_direction']	??= ($request->input('cover_slideshow_desktop_direction') !== null) ? $request->input('cover_slideshow_desktop_direction') : 'horizontal';
				$new_data['cover_slideshow_mobile_direction']	??= ($request->input('cover_slideshow_mobile_direction') !== null) ? $request->input('cover_slideshow_mobile_direction') : 'horizontal';
				$new_data['cover_autoplay_slideshow']			??= ($request->input('cover_autoplay_slideshow') !== null) ? $request->input('cover_autoplay_slideshow') : 'active';
				$new_data['cover_autoplay_slideshow_interval']	??= ($request->input('cover_autoplay_slideshow_interval') !== null) ? $request->input('cover_autoplay_slideshow_interval') : 3000;
				$new_data['cover_looping_slideshow']			??= ($request->input('cover_looping_slideshow') !== null) ? $request->input('cover_looping_slideshow') : 'active';

				if ($request->input('cover_type') == 'background_image')
				{
					if (is_array($request->input('cover_image')['background_image']))
					{
						foreach ($request->input('cover_image')['background_image'] as $key => $value) 
						{
							$cover_background_image_vars[$key] = $value;
							
							if ($request->hasFile('cover_image.background_image.'.$key.'.desktop_image'))
							{
								$cover_background_image_vars[$key]['desktop_image'] ??= $this->uploadMaterial($request->file('cover_image')['background_image'][$key]['desktop_image']);
							}

							if ($request->hasFile('cover_image.background_image.'.$key.'.mobile_image'))
							{
								$cover_background_image_vars[$key]['mobile_image'] ??= $this->uploadMaterial($request->file('cover_image')['background_image'][$key]['mobile_image']);
							}
						}
					}

					$new_data['cover_bgimage_vars'] ??= json_encode($cover_background_image_vars);
				}
				elseif ($request->input('cover_type') == 'slideshow')
				{
					if (is_array($request->input('cover_image')['slideshow']))
					{
						foreach ($request->input('cover_image')['slideshow'] as $key => $value) 
						{
							$cover_slideshow_vars[$key] = $value;
							
							if ($request->hasFile('cover_image.slideshow.'.$key.'.desktop_image'))
							{
								$cover_slideshow_vars[$key]['desktop_image'] ??= $this->uploadMaterial($request->file('cover_image')['slideshow'][$key]['desktop_image']);
							}

							if ($request->hasFile('cover_image.slideshow.'.$key.'.mobile_image'))
							{
								$cover_slideshow_vars[$key]['mobile_image'] ??= $this->uploadMaterial($request->file('cover_image')['slideshow'][$key]['mobile_image']);
							}
						}
					}

					$new_data['cover_slideshow_vars'] ??= json_encode($cover_slideshow_vars);
				}

				$coverimage = CoverImage::create($new_data);

				if ($coverimage)
				{
					/*
					if ($request->hasFile('thumbnail'))
					{
						$new_data_thumbnail['thumb_l'] ??= $this->uploadMaterial($request->file('thumbnail'));
						$new_data_thumbnail['thumb_s'] ??= $this->uploadThumbnailSmall($request->file('thumbnail'));
					
						$getCoverImageData = CoverImage::find($coverimage->id);
						$getCoverImageData->fill($new_data_thumbnail);
						$getCoverImageData->save();
					}
					*/

					DB::commit();

					// DB::rollBack();
				}
				else
				{
					DB::rollBack();
				}

				// $asd =  json_decode($request->input('cover_image_fromjs'), true);

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('Cover image created successfully'),
						// 'message'		=> $request->hasFile('cover_image.0.desktop_image')
						// 'message'		=> json_encode($request->file('cover_image')[0]['desktop_image']->getClientOriginalName())
						// 'message'		=> $request->input('cover_image_fromjs')
						// 'message'		=> $request->input('cover_image')[0]['background_overlay']
						// 'redirect_url' 	=> url('manage_course_super_trainer/add_content_course/'.$super_trainer->id)
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Cover image created successfully'));
				}
			}
			catch (\Throwable $th) 
			{
				DB::rollBack();

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success' 	=> false,
						'status'	=> 'failed',
						'message' 	=> t('Line error').': '.$th->getLine().' '.t('with message').': '.$th->getMessage()
						// 'message' 	=> $th->getLine().' - '.$th->getMessage()
						// 'message'	=> $request->input('cover_image')

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

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Account $user, Request $request, $idOrSlug)
	{
		if ($user->isAdmin())
		{
			$getCoverImageDetail = CoverImage::where('id', $idOrSlug)->first();
		}
		else
		{
			$getCoverImageDetail = CoverImage::where('user_id', auth()->user()->id)->where('id', $idOrSlug)->first();
		}

		if ($getCoverImageDetail)
		{

			return view('manage_coverimage.manage_coverimage_edit', ['data' => $getCoverImageDetail]);
		}

		abort(404);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(EditCoverImageRequest $request, $idOrSlug)
	{
		$getCoverImageData = CoverImage::find($idOrSlug);

		if ($getCoverImageData)
		{
			if ($request->validated())
			{
				DB::beginTransaction();	

				try
				{
					$cover_slideshow_vars = [];

					$new_data['uri']								??= $request->input('uri');
					$new_data['cover_type']							??= $request->input('cover_type');
					$new_data['cover_page_name']					??= $request->input('cover_page_name');
					$new_data['cover_slideshow_direction']			??= ($request->input('cover_slideshow_direction') !== null) ? $request->input('cover_slideshow_direction') : $getCoverImageData->cover_slideshow_direction;
					$new_data['cover_slideshow_desktop_direction']	??= ($request->input('cover_slideshow_desktop_direction') !== null) ? $request->input('cover_slideshow_desktop_direction') : $getCoverImageData->cover_slideshow_desktop_direction;
					$new_data['cover_slideshow_mobile_direction']	??= ($request->input('cover_slideshow_mobile_direction') !== null) ? $request->input('cover_slideshow_mobile_direction') : $getCoverImageData->cover_slideshow_mobile_direction;
					$new_data['cover_autoplay_slideshow']			??= ($request->input('cover_autoplay_slideshow') !== null) ? $request->input('cover_autoplay_slideshow') : $getCoverImageData->cover_autoplay_slideshow;
					$new_data['cover_autoplay_slideshow_interval']	??= ($request->input('cover_autoplay_slideshow_interval') !== null) ? $request->input('cover_autoplay_slideshow_interval') : $getCoverImageData->cover_autoplay_slideshow_interval;
					$new_data['cover_looping_slideshow']			??= ($request->input('cover_looping_slideshow') !== null) ? $request->input('cover_looping_slideshow') : $getCoverImageData->cover_looping_slideshow;

					if ($request->input('cover_type') == 'background_image')
					{
						$getInputVarsFromJS = json_decode($request->input('cover_image_bgimage_js'), true);

						if (is_array($request->input('cover_image')['background_image']))
						{
							foreach ($request->input('cover_image')['background_image'] as $key => $value) 
							{
								$cover_background_image_vars[$key] = $value;
								
								if ($request->hasFile('cover_image.background_image.'.$key.'.desktop_image'))
								{
									$cover_background_image_vars[$key]['desktop_image'] ??= $this->uploadMaterial($request->file('cover_image')['background_image'][$key]['desktop_image']);
								}
								else
								{
									$cover_background_image_vars[$key]['desktop_image'] = $getInputVarsFromJS[$key]['desktop_image'];
								}

								if ($request->hasFile('cover_image.background_image.'.$key.'.mobile_image'))
								{
									$cover_background_image_vars[$key]['mobile_image'] ??= $this->uploadMaterial($request->file('cover_image')['background_image'][$key]['mobile_image']);
								}
								else
								{
									$cover_background_image_vars[$key]['mobile_image'] = $getInputVarsFromJS[$key]['mobile_image'];
								}
							}
						}

						$new_data['cover_bgimage_vars'] ??= json_encode($cover_background_image_vars);
					}
					elseif ($request->input('cover_type') == 'slideshow')
					{
						$getInputVarsFromJS = json_decode($request->input('cover_image_slideshow_js'), true);

						if (is_array($request->input('cover_image')['slideshow']))
						{
							foreach ($request->input('cover_image')['slideshow'] as $key => $value) 
							{
								$cover_slideshow_vars[$key] = $value;
								
								if ($request->hasFile('cover_image.slideshow.'.$key.'.desktop_image'))
								{
									$cover_slideshow_vars[$key]['desktop_image'] ??= $this->uploadMaterial($request->file('cover_image')['slideshow'][$key]['desktop_image']);
								
									// Delete file
									$this->deleteFile($getInputVarsFromJS[$key]['desktop_image']);
								}
								else
								{
									$cover_slideshow_vars[$key]['desktop_image'] = $getInputVarsFromJS[$key]['desktop_image'];
								}

								if ($request->hasFile('cover_image.slideshow.'.$key.'.mobile_image'))
								{
									$cover_slideshow_vars[$key]['mobile_image'] ??= $this->uploadMaterial($request->file('cover_image')['slideshow'][$key]['mobile_image']);
								
									// Delete file
									$this->deleteFile($getInputVarsFromJS[$key]['mobile_image']);
								}
								else
								{
									$cover_slideshow_vars[$key]['mobile_image'] = $getInputVarsFromJS[$key]['mobile_image'];
								}
							}
						}

						$new_data['cover_slideshow_vars'] ??= json_encode($cover_slideshow_vars);
					}

					// DB::rollBack();

					$getCoverImageData->fill($new_data);

					if ($getCoverImageData->save())
					{
						DB::commit();
					}
					else
					{
						DB::rollBack();
					}

					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> true,
							'status'		=> 'success',
							'message'		=> t('CoverImage edited successfully')
							// 'message'		=> json_encode($cover_slideshow_vars)
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('success', t('CoverImage edited successfully'));
					}
				}
				catch (\Throwable $th) 
				{
					DB::rollBack();

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

		abort(404);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function delete(Account $user, Request $request, $idOrSlug)
	{
		if ($user->isAdmin())
		{
			$getDetailCoverImage = CoverImage::where('id', $idOrSlug)->first();
		}
		else
		{
			$getDetailCoverImage = CoverImage::where('user_id', auth()->user()->id)->where('id', $idOrSlug)->first();
		}

		if ($getDetailCoverImage)
		{
			DB::beginTransaction();	

			try
			{
				// Delete Background Image Data
				if ($getDetailCoverImage->cover_bgimage_vars !== null)
				{
					$getDetailCoverBgImageVars = json_decode($getDetailCoverImage->cover_bgimage_vars, true);

					foreach ($getDetailCoverBgImageVars as $key => $value) 
					{
						// Delete desktop image
						$this->deleteFile($getDetailCoverBgImageVars[$key]['desktop_image']);

						// Delete mobile image
						$this->deleteFile($getDetailCoverBgImageVars[$key]['mobile_image']);
					}
				}

				// Delete Slideshow Image Data
				if ($getDetailCoverImage->cover_slideshow_vars !== null)
				{
					$getDetailCoverSlideshowImageVars = json_decode($getDetailCoverImage->cover_slideshow_vars, true);

					foreach ($getDetailCoverSlideshowImageVars as $key => $value) 
					{
						// Delete desktop image
						$this->deleteFile($getDetailCoverSlideshowImageVars[$key]['desktop_image']);

						// Delete mobile image
						$this->deleteFile($getDetailCoverSlideshowImageVars[$key]['mobile_image']);
					}
				}

				/*
				if ($getDetailCoverImage['thumb_l'] !== NULL)
				{
					if (Storage::disk('public')->exists($getDetailCoverImage['thumb_l']))
					{
						$this->deleteFile($getDetailCoverImage['thumb_l']);
					}
				}

				if ($getDetailCoverImage['thumb_s'] !== NULL)
				{
					if (Storage::disk('public')->exists($getDetailCoverImage['thumb_s']))
					{
						$this->deleteFile($getDetailCoverImage['thumb_s']);
					}
				}
				*/

				$getDetailCoverImage->delete();
				
				DB::commit();

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('CoverImage successfully deleted'),
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('CoverImage successfully deleted'));
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

		abort(404);
	}

	public function deleteSingleCoverImageData(Request $request, $idOrSlug)
	{
		$getCoverImageData = CoverImage::find($idOrSlug);

		if ($getCoverImageData)
		{
			try
			{
				if ($request->input('cover_type') == 'slideshow')
				{
					$getDetailCoverImageVars = json_decode($getCoverImageData->cover_slideshow_vars, true);

					// Delete desktop image
					$this->deleteFile($getDetailCoverImageVars[$request->input('index_key')]['desktop_image']);

					// Delete mobile image
					$this->deleteFile($getDetailCoverImageVars[$request->input('index_key')]['mobile_image']);

					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> true,
							'status'		=> 'success',
							'message'		=> t('CoverImage successfully deleted'),
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('success', t('CoverImage successfully deleted'));
					}

					// $response = response()->json(
					// [
					// 	'success'		=> true,
					// 	'status'		=> 'success',
					// 	'message'		=> t('CoverImage successfully deleted'),
					// ]);
				}
				else
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> false,
							'status'		=> 'failed',
							'message'		=> t('Cover image type is missing from the request.'),
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('error', t('Cover image type is missing from the request.'));
					}
				}

				// abort(404);
			}
			catch (\Throwable $th) 
			{
				// DB::rollBack();

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

				// $response = response()->json(
				// [
				// 	'success' 	=> false,
				// 	'status'	=> 'failed',
				// 	'message' 	=> $th->getMessage()

				// ], 500);
			} 
			finally 
			{
				return $response;
			}
		}

		abort(404);
	}

	public function detailData(Account $user, $idOrSlug)
	{
		if ($user->isAdmin())
		{
			$getCoverImageDetail = CoverImage::where('id', $idOrSlug)->get();
		}
		else
		{
			$getCoverImageDetail = CoverImage::where('user_id', auth()->user()->id)->where('id', $idOrSlug)->get();
		}
		
		$resource = ManageCoverImageEditResource::class;

		if (is_subclass_of($resource, JsonResource::class)) 
		{
			$items = $resource::collection($getCoverImageDetail);
		} 
		else 
		{
			$items = $getCoverImageDetail;
		}

		if ($getCoverImageDetail)
		{
			$response = response()->json(
			[
				'success' 	=> true,
				'status'	=> 'success',
				'message' 	=> 'Data found',
				'data'		=> $items[0]
			]);
		}
		else
		{
			$response = response()->json(
			[
				'success' 	=> false,
				'status'	=> 'failed',
				'message' 	=> 'Data not found'

			], 500);
		}

		return $response;
	}

	public function listData(Request $request)
	{
		$api = new Base_API_Rev_Controller();
		$result = CoverImage::whereLike('cover_page_name', '%'.$request->input('search').'%')->paginate(15);

		$formattedResponse = $api->paginateResponse($result, ManageCoverImageListResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}

	public function uploadMaterial($attribute, $path = 'coverimage')
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

	protected function getImageURL($fileImage = '', $path = 'coverimage')
	{
		if (Storage::disk('public')->exists($path.'/'.$fileImage))
		{
			return Storage::url($path.'/'.$fileImage);
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
