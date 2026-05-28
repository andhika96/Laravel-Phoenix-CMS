<?php

namespace App\Http\Controllers\Web\Account;

use App\Http\Controllers\Controller;

use App\Http\Requests\Account\UpdateForUserAccountRequest;
use App\Http\Requests\Account\CheckDataAccountRequest;

use App\Models\Awesome_Admin\Roles;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Information;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

use Illuminate\Support\Str;
use Illuminate\Support\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class Account_Controller extends Controller
{
	public function index()
	{
		$getListRoles = Roles::get();
		$getDetailUser = Account::find(auth()->user()->id);
		$getDetailUserInformation = Account_Information::where('user_id', $getDetailUser->id)->first();

		return view('account.account', ['data' => $getDetailUser, 'data_information' => $getDetailUserInformation]);
	}

	public function general()
	{
		$getListRoles = Roles::get();
		$getDetailUser = Account::find(auth()->user()->id);
		$getDetailUserInformation = Account_Information::where('user_id', $getDetailUser->id)->first();

		return view('account.account', ['data' => $getDetailUser, 'data_information' => $getDetailUserInformation]);
	}

	public function profilePicture()
	{
		$getListRoles = Roles::get();
		$getDetailUser = Account::find(auth()->user()->id);
		$getDetailUserInformation = Account_Information::where('user_id', $getDetailUser->id)->first();

		return view('account.account_profile_picture', ['data' => $getDetailUser, 'data_information' => $getDetailUserInformation]);
	}

	/**
	 * Update the specified resource in storage.
	 */

	public function update(UpdateForUserAccountRequest $request)
	{
		$getDetailUser = Account::find(auth()->user()->id);
		$getDetailUserInformation = Account_Information::where('user_id', $getDetailUser->id)->first();
		
		$update_data = [];
		$update_data_information = [];

		DB::beginTransaction();	

		try 
		{
			if ($request->input('submitType') == 'general')
			{
				// $update_data['email']		??= $request->input('email');
				// $update_data['username']	??= $request->input('username');
				$update_data['fullname']	??= $request->input('fullname');

				$update_data_information['birthdate']		??= $request->input('birthdate');
				$update_data_information['gender']			??= $request->input('gender');
				$update_data_information['phone_number']	??= $request->input('phone_number');
			}
			elseif ($request->input('submitType') == 'password')
			{
				if ($request->input('password') != '')
				{
					$update_data['password'] ??= Hash::make($request->input('password'));
				}
			}

			if (count($update_data) > 0)
			{
				$getDetailUser->fill($update_data);
				$getDetailUserInformation->fill($update_data_information);

				if ($getDetailUser->save())
				{
					$getDetailUserInformation->save();

					// DB Commit if data successfully saved
					DB::commit();
				}
				else
				{
					DB::rollBack();
				}
			}

			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> true,
					'status' 	=> 'success',
					'message' 	=> t('Update user successfully')
				]);
			} 
			else 
			{
				$response = redirect()
					->back()
					->with('success', t('Update user successfully'));
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

	public function updateProfilePicture(Request $request)
	{
		$getDetailUser = Account::find(auth()->user()->id);
		$getDetailUserInformation = Account_Information::where('user_id', $getDetailUser->id)->first();
		
		$update_data = [];
		$update_data_information = [];

		DB::beginTransaction();	

		try
		{
			if ($request->input('profilePicture'))
			{
				$update_data_information['avatar'] = $this->uploadProfilePicture($request->input('profilePicture'));

				if ($update_data_information['avatar'] !== NULL)
				{
					// Delete old image and replace with the above one
					if (Storage::disk('public')->exists($getDetailUserInformation->avatar))
					{
						$this->deleteFile($getDetailUserInformation->avatar);
					}
				}

				$getDetailUserInformation->fill($update_data_information);

				if ($getDetailUserInformation->save())
				{
					// DB Commit if data successfully saved
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
						'success'   => true,
						'status'    => 'success',
						'message'   => t('Update profile photo successfully'),
						'image'		=> $update_data_information['avatar']
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Update profile photo successfully'));
				}

				DB::commit();
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

	public function uploadProfilePicture($attribute, $path = 'avatars')
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

?>
