<?php

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Status;
use App\Models\Awesome_Admin\Account_Information;
use App\Models\Awesome_Admin\Roles;
use App\Models\Awesome_Admin\Language;
use App\Models\Awesome_Admin\Site_Config;
use App\Models\Awesome_Admin\SMTP_Service;
use App\Models\Awesome_Admin\Sub_Menu_BE_JSON;
use App\Models\Awesome_Admin\Parent_Menu_BE_JSON;
use App\Models\Awesome_Admin\Category_Menu_BE_JSON;
use App\Models\Awesome_Admin\Custom_Permissions;
use App\Models\Awesome_Admin\Themes;
use App\Models\Awesome_Admin\Theme_Settings;
use App\Models\Awesome_Admin\Page_Theme;
use App\Models\Awesome_Admin\Page_Theme_Setting;

use App\Models\Article\Article_Status;

use App\Models\Notification\LPNotification;

use Illuminate\Support\Number;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

if ( ! function_exists('custom_theme'))
{
	function custom_theme($key)
	{
		$themeSettings = Theme_Settings::find(1);

		if ($themeSettings)
		{
			$themes = Themes::find($themeSettings->theme_id);

			if ($key == 'cms')
			{
				return $themes->theme_foldername.'.cms.'.$themes->theme_cms;
			}
			elseif ($key == 'auth')
			{
				return $themes->theme_foldername.'.auth.'.$themes->theme_auth;
			}
			elseif ($key == 'frontend')
			{
				return $themes->theme_foldername.'.frontend.'.$themes->theme_frontend;
			}
			else
			{
				abort(403, 'Undefined Key Theme');
			}
		}

		abort(403, 'Not theme found!');
	}
}

if ( ! function_exists('page_theme'))
{
	function page_theme($key)
	{
		$pageTheme = Page_Theme::where('theme_code', $key)->first();

		if ( ! $pageTheme)
		{
			$pageTheme = Page_Theme::where('theme_code', 'default')->first();
		}

		return $pageTheme;
	}
}

if ( ! function_exists('custom_page_theme'))
{
	function custom_page_theme($key)
	{
		$pageThemeSettings = Page_Theme_Setting::where('uri', $key)->first();

		if ( ! $pageThemeSettings)
		{
			$pageThemeSettings = Page_Theme_Setting::where('uri', 'login')->first();
		}

		return $pageThemeSettings['page_theme'];
	}
}

if ( ! function_exists('custom_page_theme_setting'))
{
	function custom_page_theme_setting($key)
	{
		$pageThemeSettings = Page_Theme_Setting::where('uri', $key)->first();

		if ( ! $pageThemeSettings)
		{
			$pageThemeSettings = Page_Theme_Setting::where('uri', 'login')->first();
		}

		if (isset($pageThemeSettings['page_background_image']))
		{
			$pageThemeSettings['page_background_image'] = 'storage/'.$pageThemeSettings['page_background_image'];
		}

		return $pageThemeSettings;
	}
}

if ( ! function_exists('coverImageContentPositionDesktopH'))
{
	function coverImageContentPositionDesktopH($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "bottom-0 end-0 text-end";
				break;
			case 'center-right':
				echo "top-50 end-0 translate-middle-y text-end";
				break;
			case 'top-right':
				echo "top-0 end-0 text-end";
				break;

			case 'bottom-center':
				echo "bottom-0 start-50 translate-middle-x text-center mb-4 w-75";
				break;
			case 'center-center':
				echo "top-50 start-50 translate-middle text-center w-75";
				break;
			case 'top-center':
				echo "top-0 start-50 translate-middle-x text-center w-75";
				break;
			
			case 'bottom-left':
				echo "bottom-0 start-0 text-start";
				break;
			case 'center-left':
				echo "top-50 start-0 translate-middle-y text-start";
				break;
			case 'top-left':
				echo "top-0 start-0 text-start";
				break;
		}
	}
}

if ( ! function_exists('coverImageContentPositionDesktopV'))
{
	function coverImageContentPositionDesktopV($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "pe-5 bottom-0 end-0 text-end";
				break;
			case 'center-right':
				echo "pe-5 top-50 end-0 translate-middle-y text-end";
				break;
			case 'top-right':
				echo "pe-5 top-0 end-0 text-end";
				break;

			case 'bottom-center':
				echo "bottom-0 start-50 translate-middle-x text-center w-75";
				break;
			case 'center-center':
				echo "top-50 start-50 translate-middle text-center w-75";
				break;
			case 'top-center':
				echo "top-0 start-50 translate-middle-x text-center w-75";
				break;
			
			case 'bottom-left':
				echo "bottom-0 start-0 text-start";
				break;
			case 'center-left':
				echo "top-50 start-0 translate-middle-y text-start";
				break;
			case 'top-left':
				echo "top-0 start-0 text-start";
				break;
		}
	}
}

if ( ! function_exists('coverImageContentPositionSecondContent'))
{
	function coverImageContentPositionSecondContent($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "pe-5 bottom-0 end-0 text-end mb-4";
				break;
			case 'center-right':
				echo "pe-5 top-50 end-0 translate-middle-y text-end";
				break;
			case 'top-right':
				echo "pe-5 top-0 end-0 text-end";
				break;

			case 'bottom-center':
				echo "bottom-0 start-50 translate-middle-x text-center mb-4";
				break;
			case 'center-center':
				echo "top-50 start-50 translate-middle text-center";
				break;
			case 'top-center':
				echo "top-0 start-50 translate-middle-x text-center";
				break;
			
			case 'bottom-left':
				echo "bottom-0 start-0 text-start mb-4";
				break;
			case 'center-left':
				echo "top-50 start-0 translate-middle-y text-start";
				break;
			case 'top-left':
				echo "top-0 start-0 text-start";
				break;
		}
	}
}

if ( ! function_exists('coverImageContentPositionCountDown'))
{
	function coverImageContentPositionCountDown($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "pe-5 bottom-0 end-0 text-end mb-4";
				break;
			case 'center-right':
				echo "pe-5 top-50 end-0 translate-middle-y text-end";
				break;
			case 'top-right':
				echo "pe-5 top-0 end-0 text-end";
				break;

			case 'bottom-center':
				echo "bottom-0 start-50 translate-middle-x text-center mb-4";
				break;
			case 'center-center':
				echo "top-50 start-50 translate-middle text-center";
				break;
			case 'top-center':
				echo "top-0 start-50 translate-middle-x text-center";
				break;
			
			case 'bottom-left':
				echo "bottom-0 start-0 text-start mb-4";
				break;
			case 'center-left':
				echo "top-50 start-0 translate-middle-y text-start";
				break;
			case 'top-left':
				echo "top-0 start-0 text-start";
				break;
		}
	}
}

if ( ! function_exists('coverImageContentPositionCountDownMobile'))
{
	function coverImageContentPositionCountDownMobile($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "bottom-0 end-0 text-end mb-4";
				break;
			case 'center-right':
				echo "top-50 end-0 translate-middle-y text-end";
				break;
			case 'top-right':
				echo "top-0 end-0 text-end";
				break;

			case 'bottom-center':
				echo "bottom-0 start-50 translate-middle-x text-center mb-4";
				break;
			case 'center-center':
				echo "top-50 start-50 translate-middle text-center";
				break;
			case 'top-center':
				echo "top-0 start-50 translate-middle-x text-center";
				break;
			
			case 'bottom-left':
				echo "bottom-0 start-0 text-start mb-4";
				break;
			case 'center-left':
				echo "top-50 start-0 translate-middle-y text-start";
				break;
			case 'top-left':
				echo "top-0 start-0 text-start";
				break;
		}
	}
}

if ( ! function_exists('coverImageContentPositionCountDownDefaultPosition'))
{
	function coverImageContentPositionCountDownDefaultPosition($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "ms-auto";
				break;
			case 'center-right':
				echo "ms-auto";
				break;
			case 'top-right':
				echo "ms-auto";
				break;

			case 'bottom-center':
				echo "mx-auto";
				break;
			case 'center-center':
				echo "mx-auto";
				break;
			case 'top-center':
				echo "mx-auto";
				break;
			
			case 'bottom-left':
				echo "me-auto";
				break;
			case 'center-left':
				echo "me-auto";
				break;
			case 'top-left':
				echo "me-auto";
				break;
		}
	}
}

if ( ! function_exists('coverImageContentGridPosition'))
{
	function coverImageContentGridPosition($position, $theres_content = true)
	{
		switch ($position) 
		{
			case 'bottom-right':
				switch ($theres_content) 
				{
					case true:
						echo "col-7 offset-5";
					break;

					case false:
						echo "col-12";
					break;
				}

				break;
			case 'center-right':
				switch ($theres_content) 
				{
					case true:
						echo "col-7 offset-5";
					break;

					case false:
						echo "col-12";
					break;
				}

				break;
			case 'top-right':
				switch ($theres_content) 
				{
					case true:
						echo "col-7 offset-5";
					break;

					case false:
						echo "col-12";
					break;
				}

				break;

			case 'bottom-center':
				echo "col-12";
				break;
			case 'center-center':
				echo "col-12";
				break;
			case 'top-center':
				echo "col-12";
				break;
			
			case 'bottom-left':
				switch ($theres_content) 
				{
					case true:
						echo "col-7";
					break;

					case false:
						echo "col-12";
					break;
				}

				break;
			case 'center-left':
				switch ($theres_content) 
				{
					case true:
						echo "col-7";
					break;

					case false:
						echo "col-12";
					break;
				}

				break;
			case 'top-left':
				switch ($theres_content) 
				{
					case true:
						echo "col-7";
					break;

					case false:
						echo "col-12";
					break;
				}

				break;
		}
	}
}

if ( ! function_exists('coverImageContentPositionMobileH'))
{
	function coverImageContentPositionMobileH($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "p-4 pb-9 pe-9 bottom-0 end-0 text-end";
				break;
			case 'center-right':
				echo "p-4 pe-9 top-50 end-0 translate-middle-y text-end";
				break;
			case 'top-right':
				echo "p-4 pe-9 top-0 end-0 text-end";
				break;

			case 'bottom-center':
				echo "p-4 pb-9 pe-9 bottom-0 start-50 translate-middle-x text-center w-100";
				break;
			case 'center-center':
				echo "p-4 px-4 top-50 start-50 translate-middle text-center w-100";
				break;
			case 'top-center':
				echo "p-4 px-4 top-0 start-50 translate-middle-x text-center w-100";
				break;
			
			case 'bottom-left':
				echo "p-4 pb-9 pe-9 bottom-0 start-0 text-start";
				break;
			case 'center-left':
				echo "p-4 pe-9 top-50 start-0 translate-middle-y text-start";
				break;
			case 'top-left':
				echo "p-4 pe-9 top-0 start-0 text-start";
				break;
		}
	}
}

if ( ! function_exists('coverImageContentGridPositionMobileH'))
{
	function coverImageContentGridPositionMobileH($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "col-12 offset-2";
				break;
			case 'center-right':
				echo "col-12 offset-2";
				break;
			case 'top-right':
				echo "col-12 offset-2";
				break;

			case 'bottom-center':
				echo "col-12";
				break;
			case 'center-center':
				echo "col-12";
				break;
			case 'top-center':
				echo "col-12";
				break;
			
			case 'bottom-left':
				echo "col-12";
				break;
			case 'center-left':
				echo "col-12";
				break;
			case 'top-left':
				echo "col-12";
				break;
		}
	}
}

if ( ! function_exists('coverImageContentPositionMobileV'))
{
	function coverImageContentPositionMobileV($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "p-4 pe-5 bottom-0 end-0 text-end";
				break;
			case 'center-right':
				echo "p-4 pe-5 top-50 end-0 translate-middle-y text-end";
				break;
			case 'top-right':
				echo "p-4 pe-5 top-0 end-0 text-end";
				break;

			case 'bottom-center':
				echo "p-4 bottom-0 start-50 translate-middle-x text-center w-100";
				break;
			case 'center-center':
				echo "p-4 pe-5 top-50 start-50 translate-middle text-center w-100";
				break;
			case 'top-center':
				echo "p-4 top-0 start-50 translate-middle-x text-center w-100";
				break;
			
			case 'bottom-left':
				echo "p-4 pe-5 bottom-0 start-0 text-start";
				break;
			case 'center-left':
				echo "p-4 pe-5 top-50 start-0 translate-middle-y text-start";
				break;
			case 'top-left':
				echo "p-4 pe-5 top-0 start-0 text-start";
				break;
		}
	}
}

if ( ! function_exists('coverImageContentGridPositionMobileV'))
{
	function coverImageContentGridPositionMobileV($position)
	{
		switch ($position) 
		{
			case 'bottom-right':
				echo "col-12 offset-0";
				break;
			case 'center-right':
				echo "col-12 offset-0";
				break;
			case 'top-right':
				echo "col-12 offset-0";
				break;

			case 'bottom-center':
				echo "col-12";
				break;
			case 'center-center':
				echo "col-12";
				break;
			case 'top-center':
				echo "col-12";
				break;
			
			case 'bottom-left':
				echo "col-12";
				break;
			case 'center-left':
				echo "col-12";
				break;
			case 'top-left':
				echo "col-12";
				break;
		}
	}
}

/*
 * Translate
 * You can translate language by using this function per word
 *
 * Use: {{ t('Hello World!') }}
 *
 * Attribute arguments is optional
 *
 * return String
 */

if ( ! function_exists('t'))
{
	function t($str, $att1 = '', $att2 = '', $att3 = '', $att4 = '')
	{
		$dbstr = addslashes($str);

		$res = Language::where('lang_from', $dbstr)->where('lang', current_language())->limit(1)->first();

		if ( ! $res)
		{
			$insert_lang = 
			[
				'lang_from'	=> $dbstr,
				'lang_to'	=> '',
				'lang'		=> current_language()
			];

			$response = Language::create($insert_lang);
		}
		else
		{
			if (strlen($res->lang_to)) 
			{
				$remove_char[] = "\\";

				$result = str_replace($remove_char, "", $res->lang_to);

				$str = $result;
			}
			else 
			{
				$remove_char[] = "\\";

				$result = str_replace($remove_char, "", $res->lang_from);

				$str = $result;
			}
		}

		return str_replace(['{1}', '{2}', '{3}', '{4}'], [$att1, $att2, $att3, $att4], $str);
	}
}

/*
 * Site Config
 * Get setting site config by using this function
 * 
 * Use: {{ site_config()->site_name }}
 *
 * return Array
 */

if ( ! function_exists('site_config'))
{
	function site_config()
	{
		$site_config = Site_Config::find(1);

		return $site_config;
	}
}

/*
 * SMTP Service
 * Get main setting SMTP Service 
 *
 * Use: {{ smtp_service()->smtp_service }}
 *
 * return Array
 */

if ( ! function_exists('smtp_service'))
{
	function smtp_service()
	{
		$smtp_service = SMTP_Service::with(['smtp_setting'])->find(1);

		return $smtp_service->smtp_setting;
	}
}

/*
 * Get current user roles
 */

if ( ! function_exists('current_role'))
{
	function current_role()
	{
		$user = new Account();

		return $user->getCurrentUserRoles()[0];
	}
}

/*
 * Current Languange
 * Get current language user by using this function
 *
 * return String
 */

if ( ! function_exists('current_language'))
{
	function current_language()
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
}

if ( ! function_exists('get_avatar'))
{
	function get_avatar($avatar_type = 'frame', $border_type = 'circle', $size = 50)
	{
		$getDetailUser = Account::where('id', auth()->user()->id)->first();
		$getDetailUserInformation = Account_Information::where('user_id', auth()->user()->id)->first();

		$get_avatar_type = ($avatar_type == 'frame') ? 'border' : (($avatar_type == 'no_frame') ? 'border-0' : 'border');
		$get_border_type = ($border_type == 'circle') ? 'rounded-circle' : (($border_type == 'square') ? 'rounded' : 'rounded-circle');

		if ($getDetailUserInformation && 
			$getDetailUserInformation['avatar'] !== '' && 
			$getDetailUserInformation['avatar'] !== NULL)
		{
			if (Storage::disk('public')->exists($getDetailUserInformation->avatar))
			{
				$output = '<img src="'.url(getImageAvatarURL($getDetailUserInformation->avatar)).'" alt="Current avatar image" class="ph-avatar '.$get_avatar_type.' '.$get_border_type.'" style="width: '.$size.'px;height: '.$size.'px">';
			}
			else
			{
				$output = '<img src="https://placehold.co/'.$size.'/E01D24/FFF?text='.mb_substr($getDetailUser->fullname, 0, 1).'" alt="Default avatar image" class="rounded-circle">';
			}
		}
		else
		{
			$output = '<img src="https://placehold.co/'.$size.'/E01D24/FFF?text='.mb_substr($getDetailUser->fullname, 0, 1).'" alt="Default avatar image" class="rounded-circle">';
		}

		return $output;
	}
}

if ( ! function_exists('get_avatar_client'))
{
	function get_avatar_client($user_id = 0, $avatar_type = 'frame', $border_type = 'circle', $size = 50)
	{
		$getDetailUserInformation = Account_Information::where('user_id', $user_id)->first();

		$get_avatar_type = ($avatar_type == 'frame') ? 'border' : (($avatar_type == 'no_frame') ? 'border-0' : 'border');
		$get_border_type = ($border_type == 'circle') ? 'rounded-circle' : (($border_type == 'square') ? 'rounded' : 'rounded-circle');

		if ($getDetailUserInformation && 
			$getDetailUserInformation['avatar'] !== '' && 
			$getDetailUserInformation['avatar'] !== NULL)
		{
			if (Storage::disk('public')->exists($getDetailUserInformation->avatar))
			{
				$output = '<img src="'.url(getImageAvatarURL($getDetailUserInformation->avatar)).'" alt="Current avatar image" class="ph-avatar '.$get_avatar_type.' '.$get_border_type.'" style="width: '.$size.'px;height: '.$size.'px">';
			}
			else
			{
				$output = null;
			}
		}
		else
		{
			$output = '<img src="https://placehold.co/'.$size.'/E01D24/FFF" alt="Default avatar image" class="rounded-circle">';
		}

		return $output;
	}
}

if ( ! function_exists('get_gender'))
{
	function get_gender($key)
	{
		switch ($key)
		{
			case 1:
				return 'Male';
			case 2:
				return 'Female';
		}
	}
}

if ( ! function_exists('get_user'))
{
	function get_user($user_id)
	{
		$account_user = Account::where('id', $user_id)->first();

		if ($account_user)
		{
			return $account_user;
		}

		return null;
	}
}

if ( ! function_exists('send_notification'))
{
	function send_notification($to_id = 0, $type = 'general', $icon = '', $title = 'Unknown', $message = 'Unknown')
	{
		$getDetailUserFromUser = Account::where('id', auth()->user()->id)->first();
		$getDetailUserToUser = Account::where('id', $to_id)->first();

		if ($getDetailUserToUser)
		{
			DB::beginTransaction();
			
			try
			{
				$new_data_notification['user_id']		= $getDetailUserFromUser['id'];
				$new_data_notification['from_id']		= $getDetailUserFromUser['id'];
				$new_data_notification['from_fullname']	= $getDetailUserFromUser['fullname'];
				$new_data_notification['to_id']			= $getDetailUserToUser['id'];
				$new_data_notification['to_fullname']	= $getDetailUserToUser['fullname'];

				$new_data_notification['type']			= ($type !== '') ? $type : 'system';
				$new_data_notification['icon']			= ($icon !== '') ? $icon : '<i class="fad fa-bullhorn fa-fw fa-lg"></i>';
				$new_data_notification['title']			= ($title !== '') ? $title : 'From System';
				$new_data_notification['message']		= $message;
				$new_data_notification['hasread']		= 0;

				LPNotification::create($new_data_notification);

				$response = true;

				DB::commit();

				Log::debug('Notification sent to user_id: '.$getDetailUserToUser['id'].' fullname: '.$getDetailUserToUser['fullname']);
			}
			catch (\Throwable $th) 
			{
				$response = false;

				Log::error($th->getMessage());

				DB::rollBack();
			} 
			finally 
			{
				return $response;
			}
		}
		else
		{
			Log::error('Cannot send notification, from_id or to_id does not exist');
		}
	}
}

if ( ! function_exists('get_all_menus'))
{
	function get_all_menus()
	{
		$output 	= [];
		$user 		= new Account();
		$parentMenu = Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		$i = 0;
		foreach ($parentMenu as $key0 => $value0) 
		{
			$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

			if (count($parentMenuVars_decode) > 0)
			{
				foreach ($parentMenuVars_decode as $key1 => $value1) 
				{
					$output[$i]['index'] 				= $i;
					$output[$i]['is_for_parent_menu'] 	= $value1['is_for_parent_menu'];
					$output[$i]['category_code'] 		= $value1['category_code'];
					$output[$i]['parent_code'] 			= $value1['parent_code'];
					$output[$i]['parent_icon'] 			= $value1['parent_icon'];
					$output[$i]['parent_name'] 			= $value1['parent_name'];
					$output[$i]['parent_link'] 			= $value1['parent_link'];
					$output[$i]['parent_type'] 			= $value1['parent_type'];
					$output[$i]['parent_roles'] 		= $value1['parent_roles'];
					$output[$i]['parent_permissions'] 	= $value1['parent_permissions'];

					if (is_array($value1['parent_permissions']))
					{
						foreach ($value1['parent_permissions'] as $key_parent_permissions0 => $value_parent_permissions0) 
						{
							$output[$i]['parent_permissions_alt'][$value_parent_permissions0] = $value_parent_permissions0;
						}
					}

					$output[$i]['parent_icon_type'] 	= $value1['parent_icon_type'];
					$output[$i]['parent_icon_path'] 	= $value1['parent_icon_path'];
					$output[$i]['parent_icon_url'] 		= $value1['parent_icon_url'];
					$output[$i]['parent_icon_custom'] 	= $value1['parent_icon_custom'];

					if ($value1['is_for_parent_menu'] == 'parent')
					{
						$subMenu = Sub_Menu_BE_JSON::where('parent_code', $output[$i]['parent_code'])->limit(1)->get();

						/*
						if ($subMenu)
						{
							// $output_submenu = $subMenu;

							// $output[$i]['parent_submenu']['list']	= $output_submenu;	

							// $i2 = 0;	

							foreach ($subMenu as $key2 => $value2) 
							{
								$subMenuVars_decode = json_decode($value2['menu_vars'], true);

								// $output[$i]['parent_submenu']['list'] = $subMenuVars_decode;

								foreach ($subMenuVars_decode as $key3 => $value3) 
								{
									$output_submenu[$output[$i]['parent_code']][$key3] = $value3;
								}

								// foreach ($subMenuVars_decode as $key_submenu0 => $value_submenu0) 
								// {
								// 	$output_submenu[$key_submenu0] = $value_submenu0;							
								// }

								$output[$i]['parent_submenu']['list'] = $output_submenu[$output[$i]['parent_code']];
							}

							// $output[$i]['parent_submenu']['list'] = $output_submenu;
										
						}
						*/

						if ($subMenu)
						{
							foreach ($subMenu as $key2 => $value2) 
							{
								$i2 = 0;

								$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);
								
								foreach ($subMenuVars_decode as $key_submenu0 => $value_submenu0) 
								{
									$output_submenu[$output[$i]['parent_code']][$i2]['index'] 				= $i2;
									$output_submenu[$output[$i]['parent_code']][$i2]['parent_code'] 		= $output[$i]['parent_code'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_code'] 		= $value_submenu0['submenu_code'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon'] 		= $value_submenu0['submenu_icon'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_name'] 		= $value_submenu0['submenu_name'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_link'] 		= $value_submenu0['submenu_link'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_type'] 		= $value_submenu0['submenu_type'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_roles'] 		= $value_submenu0['submenu_roles'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon_url'] 	= $value_submenu0['submenu_icon_url'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon_path'] 	= $value_submenu0['submenu_icon_path'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon_type'] 	= $value_submenu0['submenu_icon_type'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon_custom'] = $value_submenu0['submenu_icon_custom'];
									$output_submenu[$output[$i]['parent_code']][$i2]['submenu_permissions'] = $value_submenu0['submenu_permissions'];
									
									if (is_array($value_submenu0['submenu_permissions']))
									{
										foreach ($value_submenu0['submenu_permissions'] as $key_submenu_permissions0 => $value_submenu_permissions0) 
										{
											$output_submenu[$output[$i]['parent_code']][$i2]['submenu_permissions_alt'][$value_submenu_permissions0] = $value_submenu_permissions0;
										}
									}

									$i2++;
								}

								$output[$i]['parent_submenu']['code']	= $value2['parent_code'];
								$output[$i]['parent_submenu']['name']	= $value2['parent_name'];
								$output[$i]['parent_submenu']['list']	= $output_submenu[$output[$i]['parent_code']];
							}
						}
					}
				
					$i++;
				}
			}
		}

		return $output;
	}
}

/*
 * Get Menus
 * Get all menu for user based on their roles
 *
 * return Array
 */

if ( ! function_exists('get_menus_with_category_v1'))
{
	function get_menus_with_category_v1()
	{
		$output 			= [];
		$categoryOutput 	= [];
		$user 			= new Account();
		$categoryMenu 	= Category_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();
		$parentMenu 	= Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		// Menu list with category
		$i11 = 0;
		foreach ($categoryMenu as $key0 => $value0) 
		{
			$categoryMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

			if (count($categoryMenuVars_decode) > 0)
			{
				foreach ($categoryMenuVars_decode as $key1 => $value1) 
				{			
					if (checkTotalAccessViewMenuPermissionForMenuCategoryV1($value1['category_code']))
					{	
						$categoryOutput['categorized'][$i11]['category_code'] 	= $value1['category_code'];
						$categoryOutput['categorized'][$i11]['category_name'] 	= $value1['category_name'];
						$categoryOutput['categorized'][$i11]['category_roles'] 	= $value1['category_roles'];

						foreach ($parentMenu as $key2 => $value2) 
						{
							$parentMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

							if (count($parentMenuVars_decode) > 0)
							{
								$i2 = 0;

								foreach ($parentMenuVars_decode as $key3 => $value3) 
								{
									if ($value3['category_code'] == $value1['category_code'] && checkUserViewMenuAccessPermissionForMenuV1($value3['parent_roles']) == true)
									{
										if (checkTotalAccessViewMenuPermissionForMenuV1($value3['parent_code'], $value3['is_for_parent_menu']) > 0)
										{
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['is_for_parent_menu'] 	= $value3['is_for_parent_menu'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_code'] 		= $value3['parent_code'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_name'] 		= $value3['parent_name'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_link'] 		= $value3['parent_link'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_type'] 		= $value3['parent_type'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_roles'] 		= $value3['parent_roles'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_icon_type'] 	= $value3['parent_icon_type'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_icon_path'] 	= $value3['parent_icon_path'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_icon_custom'] 	= $value3['parent_icon_custom'];
										
											if ($value3['is_for_parent_menu'] == 'parent')
											{
												$subMenu = Sub_Menu_BE_JSON::where('parent_code', $value3['parent_code'])->get();

												if ($subMenu)
												{
													foreach ($subMenu as $key4 => $value4) 
													{
														$subMenuVars_decode = json_decode($value4['menu_vars'], TRUE);

														$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['code']	= $value4['parent_code'];
														$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['name']	= $value4['parent_name'];
														
														if (is_array($subMenuVars_decode) && count($subMenuVars_decode) > 0)
														{
															$i3 = 0;

															foreach ($subMenuVars_decode as $key5 => $value5) 
															{
																if (checkUserViewMenuAccessPermissionForMenuV1($value5['submenu_roles']) == true)
																{
																	$submenu[$value4['parent_code']][$i3]['parent_code']			= $value4['parent_code'];
																	$submenu[$value4['parent_code']][$i3]['submenu_code']			= $value5['submenu_code'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon']			= $value5['submenu_icon'];
																	$submenu[$value4['parent_code']][$i3]['submenu_link']			= $value5['submenu_link'];
																	$submenu[$value4['parent_code']][$i3]['submenu_name']			= $value5['submenu_name'];
																	$submenu[$value4['parent_code']][$i3]['submenu_type']			= $value5['submenu_type'];
																	$submenu[$value4['parent_code']][$i3]['submenu_roles']			= $value5['submenu_roles'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon_url']		= $value5['submenu_icon_url'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon_path']		= $value5['submenu_icon_path'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon_type']		= $value5['submenu_icon_type'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon_custom']	= $value5['submenu_icon_custom'];
																	$submenu[$value4['parent_code']][$i3]['submenu_permissions']	= $value5['submenu_permissions'];
																
																	$i3++;
																}
															}
														}

														if (isset($submenu[$value4['parent_code']]))
														{
															$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['list'] = $submenu[$value4['parent_code']];
														}
														else
														{
															$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['list'] = [];
														}
													}
												}
											}

											$i2++;
										}
									}
								}
							}
						}

						$i11++;
					}
				}
			}
		}

		// Menu list without category
		$i12 = 0;
		foreach ($parentMenu as $key2 => $value2) 
		{
			$parentMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

			if (count($parentMenuVars_decode) > 0)
			{
				$i2 = 0;

				foreach ($parentMenuVars_decode as $key3 => $value3) 
				{
					if ($value3['category_code'] == '' && checkUserViewMenuAccessPermissionForMenuV1($value3['parent_roles']) == true)
					{
						if (checkTotalAccessViewMenuPermissionForMenuV1($value3['parent_code'], $value3['is_for_parent_menu']) > 0)
						{
							$categoryOutput['uncategorized'][$i12]['is_for_parent_menu'] 	= $value3['is_for_parent_menu'];
							$categoryOutput['uncategorized'][$i12]['parent_code'] 			= $value3['parent_code'];
							$categoryOutput['uncategorized'][$i12]['parent_name'] 			= $value3['parent_name'];
							$categoryOutput['uncategorized'][$i12]['parent_link'] 			= $value3['parent_link'];
							$categoryOutput['uncategorized'][$i12]['parent_type'] 			= $value3['parent_type'];
							$categoryOutput['uncategorized'][$i12]['parent_roles'] 			= $value3['parent_roles'];
							$categoryOutput['uncategorized'][$i12]['parent_icon_type'] 		= $value3['parent_icon_type'];
							$categoryOutput['uncategorized'][$i12]['parent_icon_path'] 		= $value3['parent_icon_path'];
							$categoryOutput['uncategorized'][$i12]['parent_icon_custom'] 	= $value3['parent_icon_custom'];
						
							if ($value3['is_for_parent_menu'] == 'parent')
							{
								$subMenu = Sub_Menu_BE_JSON::where('parent_code', $value3['parent_code'])->get();

								if ($subMenu)
								{
									foreach ($subMenu as $key4 => $value4) 
									{
										$subMenuVars_decode = json_decode($value4['menu_vars'], TRUE);

										$categoryOutput['uncategorized'][$i12]['parent_submenu']['code']	= $value4['parent_code'];
										$categoryOutput['uncategorized'][$i12]['parent_submenu']['name']	= $value4['parent_name'];
										
										if (is_array($subMenuVars_decode) && count($subMenuVars_decode) > 0)
										{
											$i3 = 0;

											foreach ($subMenuVars_decode as $key5 => $value5) 
											{
												if (checkUserViewMenuAccessPermissionForMenuV1($value5['submenu_roles']) == true)
												{
													$submenu[$value4['parent_code']][$i3]['parent_code']			= $value4['parent_code'];
													$submenu[$value4['parent_code']][$i3]['submenu_code']			= $value5['submenu_code'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon']			= $value5['submenu_icon'];
													$submenu[$value4['parent_code']][$i3]['submenu_link']			= $value5['submenu_link'];
													$submenu[$value4['parent_code']][$i3]['submenu_name']			= $value5['submenu_name'];
													$submenu[$value4['parent_code']][$i3]['submenu_type']			= $value5['submenu_type'];
													$submenu[$value4['parent_code']][$i3]['submenu_roles']			= $value5['submenu_roles'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon_url']		= $value5['submenu_icon_url'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon_path']		= $value5['submenu_icon_path'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon_type']		= $value5['submenu_icon_type'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon_custom']	= $value5['submenu_icon_custom'];
													$submenu[$value4['parent_code']][$i3]['submenu_permissions']	= $value5['submenu_permissions'];
												
													$i3++;
												}
											}
										}

										if (isset($submenu[$value4['parent_code']]))
										{
											$categoryOutput['uncategorized'][$i12]['parent_submenu']['list'] = $submenu[$value4['parent_code']];
										}
										else
										{
											$categoryOutput['uncategorized'][$i12]['parent_submenu']['list'] = [];
										}
									}
								}
							}

							$i2++;
						}

						$i12++;
					}


				}
			}
		}

		return $categoryOutput;
	}
}


// This function from Gading Pro Web which is to create new role with checklist permission in menu list
if ( ! function_exists('get_menus_with_category_v2'))
{
	function get_menus_with_category_v2()
	{
		$output 			= [];
		$categoryOutput 	= [];
		$user 			= new Account();
		$categoryMenu 	= Category_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();
		$parentMenu 	= Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		// Menu list with category
		$i11 = 0;
		foreach ($categoryMenu as $key0 => $value0) 
		{
			$categoryMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

			if (count($categoryMenuVars_decode) > 0)
			{
				foreach ($categoryMenuVars_decode as $key1 => $value1) 
				{			
					if (checkTotalAccessViewMenuPermissionForMenuCategoryV2($value1['category_code']))
					{	
						$categoryOutput['categorized'][$i11]['category_code'] 	= $value1['category_code'];
						$categoryOutput['categorized'][$i11]['category_name'] 	= $value1['category_name'];
						$categoryOutput['categorized'][$i11]['category_roles'] 	= $value1['category_roles'];

						foreach ($parentMenu as $key2 => $value2) 
						{
							$parentMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

							if (count($parentMenuVars_decode) > 0)
							{
								$i2 = 0;

								foreach ($parentMenuVars_decode as $key3 => $value3) 
								{
									if ($value3['category_code'] == $value1['category_code'] || checkUserViewPageAccessPermissionForMenuV2($value1['category_code'], $value3['parent_code'], $value3['parent_link']) == true)
									{
										if (checkTotalAccessViewMenuPermissionForMenuV2($value1['category_code'], $value3['parent_code'], $value3['is_for_parent_menu']) > 0)
										{
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['is_for_parent_menu'] 	= $value3['is_for_parent_menu'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['category_code'] 		= $value1['category_code'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_code'] 		= $value3['parent_code'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_name'] 		= $value3['parent_name'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_link'] 		= $value3['parent_link'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_type'] 		= $value3['parent_type'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_roles'] 		= $value3['parent_roles'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_icon_type'] 	= $value3['parent_icon_type'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_icon_path'] 	= $value3['parent_icon_path'];
											$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_icon_custom'] 	= $value3['parent_icon_custom'];
										
											if ($value3['is_for_parent_menu'] == 'parent')
											{
												$subMenu = Sub_Menu_BE_JSON::where('parent_code', $value3['parent_code'])->get();

												if ($subMenu)
												{
													foreach ($subMenu as $key4 => $value4) 
													{
														$subMenuVars_decode = json_decode($value4['menu_vars'], TRUE);

														$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['code']	= $value4['parent_code'];
														$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['name']	= $value4['parent_name'];
														
														if (is_array($subMenuVars_decode) && count($subMenuVars_decode) > 0)
														{
															$i3 = 0;

															foreach ($subMenuVars_decode as $key5 => $value5) 
															{
																if (checkUserViewPageAccessPermissionForMenuV2($value1['category_code'], $value4['parent_code'], $value5['submenu_link']) == true)
																{
																	$submenu[$value4['parent_code']][$i3]['parent_code']			= $value4['parent_code'];
																	$submenu[$value4['parent_code']][$i3]['submenu_code']			= $value5['submenu_code'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon']			= $value5['submenu_icon'];
																	$submenu[$value4['parent_code']][$i3]['submenu_link']			= $value5['submenu_link'];
																	$submenu[$value4['parent_code']][$i3]['submenu_name']			= $value5['submenu_name'];
																	$submenu[$value4['parent_code']][$i3]['submenu_type']			= $value5['submenu_type'];
																	$submenu[$value4['parent_code']][$i3]['submenu_roles']			= $value5['submenu_roles'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon_url']		= $value5['submenu_icon_url'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon_path']		= $value5['submenu_icon_path'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon_type']		= $value5['submenu_icon_type'];
																	$submenu[$value4['parent_code']][$i3]['submenu_icon_custom']	= $value5['submenu_icon_custom'];
																	$submenu[$value4['parent_code']][$i3]['submenu_permissions']	= $value5['submenu_permissions'];
																
																	$i3++;
																}
															}
														}

														if (isset($submenu[$value4['parent_code']]))
														{
															$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['list'] = $submenu[$value4['parent_code']];
														}
														else
														{
															$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['list'] = [];
														}
													}
												}
											}

											$i2++;
										}
									}
								}
							}
						}

						$i11++;
					}
				}
			}
		}

		// Menu list without category
		$i12 = 0;
		foreach ($parentMenu as $key2 => $value2) 
		{
			$parentMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

			if (count($parentMenuVars_decode) > 0)
			{
				$i2 = 0;

				foreach ($parentMenuVars_decode as $key3 => $value3) 
				{
					if ($value3['category_code'] == '' || checkUserViewPageAccessPermissionForMenuV2(NULL, $value3['parent_code'], $value3['parent_link']) == true)
					{
						if (checkTotalAccessViewMenuPermissionForMenuV2(NULL, $value3['parent_code'], $value3['is_for_parent_menu']) > 0)
						{
							$categoryOutput['uncategorized'][$i12]['is_for_parent_menu'] 	= $value3['is_for_parent_menu'];
							$categoryOutput['uncategorized'][$i12]['category_code'] 		= $value3['category_code'];
							$categoryOutput['uncategorized'][$i12]['parent_code'] 			= $value3['parent_code'];
							$categoryOutput['uncategorized'][$i12]['parent_name'] 			= $value3['parent_name'];
							$categoryOutput['uncategorized'][$i12]['parent_link'] 			= $value3['parent_link'];
							$categoryOutput['uncategorized'][$i12]['parent_type'] 			= $value3['parent_type'];
							$categoryOutput['uncategorized'][$i12]['parent_roles'] 			= $value3['parent_roles'];
							$categoryOutput['uncategorized'][$i12]['parent_icon_type'] 		= $value3['parent_icon_type'];
							$categoryOutput['uncategorized'][$i12]['parent_icon_path'] 		= $value3['parent_icon_path'];
							$categoryOutput['uncategorized'][$i12]['parent_icon_custom'] 	= $value3['parent_icon_custom'];
						
							if ($value3['is_for_parent_menu'] == 'parent')
							{
								$subMenu = Sub_Menu_BE_JSON::where('parent_code', $value3['parent_code'])->get();

								if ($subMenu)
								{
									foreach ($subMenu as $key4 => $value4) 
									{
										$subMenuVars_decode = json_decode($value4['menu_vars'], TRUE);

										$categoryOutput['uncategorized'][$i12]['parent_submenu']['code']	= $value4['parent_code'];
										$categoryOutput['uncategorized'][$i12]['parent_submenu']['name']	= $value4['parent_name'];
										
										if (is_array($subMenuVars_decode) && count($subMenuVars_decode) > 0)
										{
											$i3 = 0;

											foreach ($subMenuVars_decode as $key5 => $value5) 
											{
												if (checkUserViewPageAccessPermissionForMenuV2(NULL, $value4['parent_code'], $value5['submenu_link']) == true)
												{
													$submenu[$value4['parent_code']][$i3]['parent_code']			= $value4['parent_code'];
													$submenu[$value4['parent_code']][$i3]['submenu_code']			= $value5['submenu_code'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon']			= $value5['submenu_icon'];
													$submenu[$value4['parent_code']][$i3]['submenu_link']			= $value5['submenu_link'];
													$submenu[$value4['parent_code']][$i3]['submenu_name']			= $value5['submenu_name'];
													$submenu[$value4['parent_code']][$i3]['submenu_type']			= $value5['submenu_type'];
													$submenu[$value4['parent_code']][$i3]['submenu_roles']			= $value5['submenu_roles'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon_url']		= $value5['submenu_icon_url'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon_path']		= $value5['submenu_icon_path'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon_type']		= $value5['submenu_icon_type'];
													$submenu[$value4['parent_code']][$i3]['submenu_icon_custom']	= $value5['submenu_icon_custom'];
													$submenu[$value4['parent_code']][$i3]['submenu_permissions']	= $value5['submenu_permissions'];
												
													$i3++;
												}
											}
										}

										if (isset($submenu[$value4['parent_code']]))
										{
											$categoryOutput['uncategorized'][$i12]['parent_submenu']['list'] = $submenu[$value4['parent_code']];
										}
										else
										{
											$categoryOutput['uncategorized'][$i12]['parent_submenu']['list'] = [];
										}
									}
								}
							}

							$i2++;
						}

						$i12++;
					}
				}
			}
		}

		return $categoryOutput;
	}
}

/*
 * Get Menus
 * Get all menu for user based on their roles
 *
 * return String
 */

if ( ! function_exists('menu_for_user_v1'))
{
	function menu_for_user_v1()
	{
		$output = '';

		// Menu list with category
		if (isset(get_menus_with_category_v1()['categorized']) &&
			count(get_menus_with_category_v1()['categorized']) > 0)
		{
			foreach (get_menus_with_category_v1()['categorized'] as $key0 => $value0) 
			{
				if (isset($value0['parent_menu']))
				{
					$output .= '
					<div class="arv7-title row">
						<div class="col-auto fw-bold">
							'.$value0['category_name'].'
						</div>

						<div class="col ps-0">
							<hr class="navbar-vertical-divider mb-0">
						</div>
					</div>

					<ul class="list-group ph-list-group-menu list-group-flush">';

					if (isset($value0['parent_menu']))
					{
						foreach ($value0['parent_menu'] as $key1 => $value1) 
						{
							if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
							{
								if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
								{
									$parent_icon_menu = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
								}
								else
								{
									$parent_icon_menu = '';
								}
							}
							elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
							{
								if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
								{
									$parent_icon_menu = $value1['parent_icon_custom'];
								}
								else
								{
									$parent_icon_menu = '';
								}
							}
							else
							{
								$parent_icon_menu = '';
							}

							if ($value1['is_for_parent_menu'] == 'parent')
							{
								if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
								{
									$output .= '
									<a href="javascript:void(0)" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
									</a>

									<div class="multi-collapse ph-list-group-submenu list-group-sub collapse" id="Collapse'.$value1['parent_code'].'">
										<div class="list-group list-group-flush">';

										foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
										{
											$output .= '
											<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center ps-5">
												<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'" style="width: calc(100% - 8px)">'.$value1['submenu_name'].'</span>
											</a>';
										}
										
									$output .= '
										</div>
									</div>';
								}
								else
								{
									$output .= '
									<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center" target="_blank">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
									</a>';
								}
							}
							elseif ($value1['is_for_parent_menu'] == 'single')
							{
								$output .= '
									<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center" target="_blank">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
									</a>';
							}
						}
					}
					else
					{
						$output .= '
							<a class="list-group-item list-group-item-action text-danger">
								No Menu
							</a>';			
					}

					$output .= '</ul>';
				}
			}
		}
		
		// Menu list without category
		if (isset(get_menus_with_category_v1()['uncategorized']) &&
			count(get_menus_with_category_v1()['uncategorized']) > 0)
		{
			$output .= '
			<div class="arv7-title row">
				<div class="col-auto fw-bold">
					Uncategorized
				</div>

				<div class="col ps-0">
					<hr class="navbar-vertical-divider mb-0">
				</div>
			</div>

			<ul class="list-group ph-list-group-menu list-group-flush">';

			foreach (get_menus_with_category_v1()['uncategorized'] as $key1 => $value1) 
			{
				if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
				{
					if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
					{
						$parent_icon_menu = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
					}
					else
					{
						$parent_icon_menu = '';
					}
				}
				elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
				{
					if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
					{
						$parent_icon_menu = $value1['parent_icon_custom'];
					}
					else
					{
						$parent_icon_menu = '';
					}
				}
				else
				{
					$parent_icon_menu = '';
				}

				if ($value1['is_for_parent_menu'] == 'parent')
				{
					if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
					{
						$output .= '
						<a href="javascript:void(0)" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
							<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
						</a>

						<div class="multi-collapse ph-list-group-submenu list-group-sub collapse" id="Collapse'.$value1['parent_code'].'">
							<div class="list-group list-group-flush">';

							foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
							{
								$output .= '
								<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center ps-5" target="_blank">
									<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" style="width: calc(100% - 8px)" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'">'.$value1['submenu_name'].'</span>
								</a>';
							}
							
						$output .= '
							</div>
						</div>';
					}
					else
					{
						$output .= '
						<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center" target="_blank">
							<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
						</a>';
					}
				}
				elseif ($value1['is_for_parent_menu'] == 'single')
				{
					$output .= '
						<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center" target="_blank">
							<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
						</a>';
				}
			}

			$output .= '</ul>';
		}

		return $output;
	}
}

if ( ! function_exists('menu_for_user_v2'))
{
	function menu_for_user_v2()
	{
		$output = '';

		// Menu list with category
		if (isset(get_menus_with_category_v2()['categorized']) &&
			count(get_menus_with_category_v2()['categorized']) > 0)
		{
			foreach (get_menus_with_category_v2()['categorized'] as $key0 => $value0) 
			{
				if (isset($value0['parent_menu']))
				{
					$output .= '
					<div class="arv7-title row">
						<div class="col-auto fw-bold">
							'.$value0['category_name'].'
						</div>

						<div class="col ps-0">
							<hr class="navbar-vertical-divider mb-0">
						</div>
					</div>

					<ul class="list-group ph-list-group-menu list-group-flush">';

					if (isset($value0['parent_menu']))
					{
						foreach ($value0['parent_menu'] as $key1 => $value1) 
						{
							if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
							{
								if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
								{
									$parent_icon_menu = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
								}
								else
								{
									$parent_icon_menu = '';
								}
							}
							elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
							{
								if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
								{
									$parent_icon_menu = $value1['parent_icon_custom'];
								}
								else
								{
									$parent_icon_menu = '';
								}
							}
							else
							{
								$parent_icon_menu = '';
							}

							if ($value1['is_for_parent_menu'] == 'parent')
							{
								if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
								{
									$output .= '
									<a href="javascript:void(0)" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
									</a>

									<div class="multi-collapse ph-list-group-submenu list-group-sub collapse" id="Collapse'.$value1['parent_code'].'">
										<div class="list-group list-group-flush">';

										foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
										{
											$output .= '
											<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center ps-5">
												<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'" style="width: calc(100% - 8px)">'.$value1['submenu_name'].'</span>
											</a>';
										}
										
									$output .= '
										</div>
									</div>';
								}
								else
								{
									$output .= '
									<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center" target="_blank">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
									</a>';
								}
							}
							elseif ($value1['is_for_parent_menu'] == 'single')
							{
								$output .= '
									<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center" target="_blank">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
									</a>';
							}
						}
					}
					else
					{
						$output .= '
							<a class="list-group-item list-group-item-action text-danger">
								No Menu
							</a>';			
					}

					$output .= '</ul>';
				}
			}
		}
		
		// Menu list without category
		if (isset(get_menus_with_category_v2()['uncategorized']) &&
			count(get_menus_with_category_v2()['uncategorized']) > 0)
		{
			$output .= '
			<div class="arv7-title row">
				<div class="col-auto fw-bold">
					Uncategorized
				</div>

				<div class="col ps-0">
					<hr class="navbar-vertical-divider mb-0">
				</div>
			</div>

			<ul class="list-group ph-list-group-menu list-group-flush">';

			foreach (get_menus_with_category_v2()['uncategorized'] as $key1 => $value1) 
			{
				if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
				{
					if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
					{
						$parent_icon_menu = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
					}
					else
					{
						$parent_icon_menu = '';
					}
				}
				elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
				{
					if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
					{
						$parent_icon_menu = $value1['parent_icon_custom'];
					}
					else
					{
						$parent_icon_menu = '';
					}
				}
				else
				{
					$parent_icon_menu = '';
				}

				if ($value1['is_for_parent_menu'] == 'parent')
				{
					if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
					{
						$output .= '
						<a href="javascript:void(0)" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
							<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
						</a>

						<div class="multi-collapse ph-list-group-submenu list-group-sub collapse" id="Collapse'.$value1['parent_code'].'">
							<div class="list-group list-group-flush">';

							foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
							{
								$output .= '
								<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center ps-5" target="_blank">
									<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" style="width: calc(100% - 8px)" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'">'.$value1['submenu_name'].'</span>
								</a>';
							}
							
						$output .= '
							</div>
						</div>';
					}
					else
					{
						$output .= '
						<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center" target="_blank">
							<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
						</a>';
					}
				}
				elseif ($value1['is_for_parent_menu'] == 'single')
				{
					$output .= '
						<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center" target="_blank">
							<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
						</a>';
				}
			}

			$output .= '</ul>';
		}

		return $output;
	}
}

if ( ! function_exists('menu_for_user_versioning'))
{
	function menu_for_user_versioning()
	{
		if (site_config()->management_menu == 'v1')
		{
			return menu_for_user_v1();
		}
		elseif (site_config()->management_menu == 'v2')
		{
			return menu_for_user_v2();
		}

		return false;
	}
}

/**
 * Create a "Random" String
 *
 * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
 * @param	int	number of characters
 * @return	string
 */

if ( ! function_exists('random_string'))
{
	function random_string($type = 'alnum', $len = 8)
	{
		switch ($type)
		{
			case 'basic':
				return mt_rand();
			case 'alnum':
			case 'numeric':
			case 'nozero':
			case 'alpha':
				switch ($type)
				{
					case 'alpha':
						$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum':
						$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric':
						$pool = '0123456789';
						break;
					case 'nozero':
						$pool = '123456789';
						break;
				}
				return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
			case 'unique': // todo: remove in 3.1+
			case 'md5':
				return md5(uniqid(mt_rand()));
			case 'encrypt': // todo: remove in 3.1+
			case 'sha1':
				return sha1(uniqid(mt_rand(), TRUE));
		}
	}
}

/**
 * Add's _1 to a string or increment the ending number to allow _2, _3, etc
 *
 * @param	string	required
 * @param	string	What should the duplicate number be appended with
 * @param	string	Which number should be used for the first dupe increment
 * @return	string
 */

if ( ! function_exists('increment_string'))
{
	function increment_string($str, $separator = '_', $first = 1)
	{
		preg_match('/(.+)' . preg_quote($separator, '/') . '([0-9]+)$/', $str, $match);
		return isset($match[2]) ? $match[1] . $separator . ($match[2] + 1) : $str . $separator . $first;
	}
}

/*
 * Check User Page Access Roles
 * We check roles user is same as role page or not
 *
 * return Boolean
 */

if ( ! function_exists('checkUserPageAccessRoles'))
{
	function checkUserPageAccessRoles()
	{
		$user 		= new Account();
		$currentURI = Route::current()->uri();
		$parentMenu = Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		foreach ($parentMenu as $key0 => $value0) 
		{
			$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

			if (count($parentMenuVars_decode) > 0)
			{
				foreach ($parentMenuVars_decode as $key1 => $value1) 
				{
					if ($value1['parent_link'] == $currentURI)
					{
						$checkAccessUserRoles = array_intersect($user->getCurrentUserRoles()->toArray(), $value1['parent_roles']);

						if (count($checkAccessUserRoles) > 0)
						{
							return true;
						}

						abort(403);
					}
					/*
					else
					{
						$subMenu = Sub_Menu_BE_JSON::where('parent_code', $value1['parent_code'])->get();

						if ($subMenu)
						{
							foreach ($subMenu as $key2 => $value2) 
							{
								$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

								if (count($subMenuVars_decode) > 0)
								{
									foreach ($subMenuVars_decode as $key3 => $value3) 
									{
										if ($value3['submenu_link'] == $currentURI)
										{
											$checkAccessUserRoles2 = array_intersect($user->getCurrentUserRoles()->toArray(), $value3['submenu_roles']);

											if (count($checkAccessUserRoles2) > 0)
											{
												return true;
											}

											abort(403);											
										}
									}
								}
							}
						}
					}
					*/
				}
			}
		}
	}
}

// ------------------------ Menu Feature v1 Start ------------------ //

if ( ! function_exists('checkUserViewPageAccessPermissionsV1'))
{
	function checkUserViewPageAccessPermissionsV1()
	{
		$userAccount = new Account();
		$getCurrentURI = Route::current()->uri();
		$getCurrentRole = $userAccount->getCurrentUserRoles()[0];

		$parentMenu = Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		foreach ($parentMenu as $key0 => $value0) 
		{
			$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

			if (count($parentMenuVars_decode) > 0)
			{
				foreach ($parentMenuVars_decode as $key1 => $value1) 
				{
					if ($value1['is_for_parent_menu'] == 'single' && $value1['parent_link'] == $getCurrentURI)
					{
						$checkAccessUserRoles = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value1['parent_roles']);

						if (count($checkAccessUserRoles) > 0)
						{
							getUserViewPageAccessPermissionsV1($getCurrentRole);
						}
						else
						{
							abort(403);
						}
					}
					elseif ($value1['is_for_parent_menu'] == 'parent')
					{
						$subMenu = Sub_Menu_BE_JSON::where('parent_code', $value1['parent_code'])->get();

						if ($subMenu)
						{
							foreach ($subMenu as $key2 => $value2) 
							{
								$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

								if (count($subMenuVars_decode) > 0)
								{
									foreach ($subMenuVars_decode as $key3 => $value3) 
									{
										if ($value3['submenu_link'] == $getCurrentURI)
										{
											$checkAccessUserRoles2 = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value3['submenu_roles']);

											if (count($checkAccessUserRoles2) > 0)
											{
												getUserViewPageAccessPermissionsV1($getCurrentRole);
											}
											else
											{
												abort(403);											
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists('checkUserDynamicPageAccessPermissionsV1'))
{
	function checkUserDynamicPageAccessPermissionsV1($permission)
	{
		$userAccount = new Account();
		$getCurrentURI = Route::current()->uri();
		$getCurrentRole = $userAccount->getCurrentUserRoles()[0];

		$parentMenu = Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		foreach ($parentMenu as $key0 => $value0) 
		{
			$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

			if (count($parentMenuVars_decode) > 0)
			{
				foreach ($parentMenuVars_decode as $key1 => $value1) 
				{
					if ($value1['is_for_parent_menu'] == 'single' && $value1['parent_link'] == $getCurrentURI)
					{
						$checkAccessUserRoles = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value1['parent_roles']);

						if (count($checkAccessUserRoles) > 0)
						{
							getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission);
						}
						else
						{
							abort(403);
						}
					}
					elseif ($value1['is_for_parent_menu'] == 'parent')
					{
						$subMenu = Sub_Menu_BE_JSON::where('parent_code', $value1['parent_code'])->get();

						if ($subMenu)
						{
							foreach ($subMenu as $key2 => $value2) 
							{
								$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

								if (count($subMenuVars_decode) > 0)
								{
									foreach ($subMenuVars_decode as $key3 => $value3) 
									{
										if ($value3['submenu_link'] == $getCurrentURI)
										{
											$checkAccessUserRoles2 = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value3['submenu_roles']);

											if (count($checkAccessUserRoles2) > 0)
											{
												getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission);
											}
											else
											{
												abort(403);											
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists('getUserViewPageAccessPermissionsV1'))
{
	function getUserViewPageAccessPermissionsV1($getCurrentRole)
	{
		$output = [];
		$role = Role::findByName($getCurrentRole);

		if ($role !== null)
		{
			if (count($role->getAllPermissions()) !== 0)
			{
				foreach ($role->getAllPermissions() as $key => $value) 
				{
					$output[$value['name']] = $value['name'];
				}

				if (isset($output['read data']))
				{
					return true;
				}
			}
		}

		abort(403);
	}
}

if ( ! function_exists('getUserDynamicPageAccessPermissionsV1Old'))
{
	function getUserDynamicPageAccessPermissionsV1Old($getCurrentRole, $permission)
	{
		$output = [];
		$role = Role::findByName($getCurrentRole);

		if ($role !== null)
		{
			if (count($role->getAllPermissions()) !== 0)
			{
				foreach ($role->getAllPermissions() as $key => $value) 
				{
					$output[$value['name']] = $value['name'];
				}

				if (isset($output[$permission]))
				{
					return true;
				}
			}
		}

		abort(403);
	}
}

if ( ! function_exists('getUserDynamicPageAccessPermissionsV1'))
{
	function getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission)
	{
		$output = [];
		$role = Role::findByName($getCurrentRole);

		if ($role !== null)
		{
			if (count($role->getAllPermissions()) !== 0)
			{
				foreach ($role->getAllPermissions() as $key => $value) 
				{
					$output[$value['name']] = $value['name'];
				}

				if (isset($output[$permission]))
				{
					return true;
				}
			}
		}

		// abort(403);
		return false;
	}
}

if ( ! function_exists('checkTotalAccessViewMenuPermissionForMenuCategoryV1'))
{
	function checkTotalAccessViewMenuPermissionForMenuCategoryV1($category_code)
	{
		$increment 	= 0;
		$user 		= new Account();
		$parentMenu = Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		$category_code_static = 'c3TCIKzEkquxl7Ltog2Iuw';

		foreach ($parentMenu as $key0 => $value0) 
		{
			$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

			if (count($parentMenuVars_decode) > 0)
			{
				foreach ($parentMenuVars_decode as $key1 => $value1) 
				{	
					if ($value1['category_code'] == $category_code && checkUserViewMenuAccessPermissionForMenuV1($value1['parent_roles']) == true)
					{
						if (checkTotalAccessViewMenuPermissionForMenuV1($value1['parent_code'], $value1['is_for_parent_menu']) > 0)
						{
							$increment += 1;
						}

						if ($value1['is_for_parent_menu'] == 'parent')
						{
							$subMenu = Sub_Menu_BE_JSON::where('parent_code', $value1['parent_code'])->get();

							if ($subMenu)
							{		
								foreach ($subMenu as $key2 => $value2) 
								{
									$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

									if (is_array($subMenuVars_decode))
									{
										foreach ($subMenuVars_decode as $key3 => $value3) 
										{
											if (checkUserViewMenuAccessPermissionForMenuV1($value3['submenu_roles']) == true)
											{
												$increment += 1;
											}
										}
									}
								}
							}
						}
					}
				}
			}		
		}

		return $increment;
	}
}

if ( ! function_exists('checkTotalAccessViewMenuPermissionForMenuV1'))
{
	function checkTotalAccessViewMenuPermissionForMenuV1($parent_code, $menu_type)
	{
		$increment = 0;
		$user = new Account();

		$parent_code_static = 'FbdIsyyI8ndPZoH9AeEzlR';

		if ($menu_type == 'parent')
		{
			$subMenu = Sub_Menu_BE_JSON::where('parent_code', $parent_code)->get();

			if ($subMenu)
			{		
				foreach ($subMenu as $key0 => $value0) 
				{
					$subMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

					if (is_array($subMenuVars_decode))
					{
						foreach ($subMenuVars_decode as $key1 => $value1) 
						{
							if (checkUserViewMenuAccessPermissionForMenuV1($value1['submenu_roles']) == true)
							{
								$increment += 1;
							}
						}
					}
				}
			}
		}
		elseif ($menu_type == 'single')
		{
			$increment += 1;
		}
		
		return $increment;
	}
}

if ( ! function_exists('checkUserViewMenuAccessPermissionForMenuV1'))
{
	function checkUserViewMenuAccessPermissionForMenuV1($role_name)
	{
		$user = new Account();
		$role_name_static = ['Administrator', 'Super Admin'];

		// $checkAccessUserRolesInSubmenu = array_intersect($user->getCurrentUserRoles()->toArray(), $role_name);
		$checkAccessUserRolesInSubmenu = is_array($role_name) ? array_intersect($user->getCurrentUserRoles()->toArray(), $role_name) : [];

		if (is_array($checkAccessUserRolesInSubmenu) && count($checkAccessUserRolesInSubmenu) > 0)
		{
			$role = Role::findByName($checkAccessUserRolesInSubmenu[0]);

			if ($role !== null)
			{
				if (count($role->getAllPermissions()) !== 0)
				{
					foreach ($role->getAllPermissions() as $key => $value) 
					{
						$permissions[$key] = $value['name'];
					}
				}
				else
				{
					$permissions = [];
				}

				if (is_array($permissions) && count($permissions) > 0)
				{
					foreach ($permissions as $key => $value) 
					{
						$output[$value] = $value;
					}

					if (isset($output['read data']))
					{
						return true;
					}
				}

				return false;

				// if ($user->isAdmin())
				// {
				// 	return true;
				// }
				// else
				// {
				// 	return false;
				// }
			}
		}
		else
		{
			return false;

			// Disabled by Andhika Adhitia N -  07082025
			/*
			if ($user->isAdmin())
			{
				return true;
			}
			else
			{
				return false;
			}
			*/
		}
	}
}

// ------------------------ Menu Feature v1 End ------------------ //

// ------------------------ Menu Feature v2 Start ------------------ //

if ( ! function_exists('checkUserViewPageAccessPermissionForMenuV2'))
{
	function checkUserViewPageAccessPermissionForMenuV2($category_code, $parent_code, $menu_link)
	{
		$user = new Account();

		if ($category_code !== NULL || $category_code !== '')
		{
			$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->where('category_code', $category_code)->where('parent_code', $parent_code)->where('menu_link', $menu_link)->get();
		}
		elseif ($category_code == NULL || $category_code == '')
		{
			$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->whereNull('category_code')->where('parent_code', $parent_code)->where('menu_link', $menu_link)->get();
		}

		if ($custom_permissions !== NULL)
		{
			foreach ($custom_permissions as $key0 => $value0) 
			{
				$json_decode = json_decode($value0['permissions'], true);

				if (is_array($json_decode))
				{
					foreach ($json_decode as $key1 => $value1) 
					{
						$output[$value1] = $value1;
					}
				}
			}

			if (isset($output['read data']))
			{
				return true;
			}
		}

		// if ($user->isAdmin())
		// {
		// 	return true;
		// }
		// else
		// {
		// 	return false;
		// }

		return false;
	}
}

if ( ! function_exists('checkTotalAccessViewMenuPermissionForMenuV2'))
{
	function checkTotalAccessViewMenuPermissionForMenuV2($category_code, $parent_code, $menu_type)
	{
		$increment = 0;
		$user = new Account();

		if ($menu_type == 'parent')
		{
			if ($category_code !== NULL || $category_code !== '')
			{
				$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->where('category_code', $category_code)->where('parent_code', $parent_code)->where('menu_type', 'submenu')->get();
			}
			elseif ($category_code == NULL || $category_code == '')
			{	
				$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->whereNull('category_code')->where('parent_code', $parent_code)->where('menu_type', 'submenu')->get();
			}

			if ($custom_permissions !== NULL)
			{
				foreach ($custom_permissions as $key0 => $value0) 
				{
					$json_decode = json_decode($value0['permissions'], true);

					if (is_array($json_decode))
					{
						foreach ($json_decode as $key1 => $value1) 
						{
							if ($value1 == 'read data')
							{
								$increment += 1;
							}
						}
					}
				}
			}
		}
		elseif ($menu_type == 'single')
		{
			if ($category_code !== NULL || $category_code !== '')
			{
				$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->where('category_code', $category_code)->where('parent_code', $parent_code)->where('menu_type', 'single')->get();
			}
			elseif ($category_code == NULL || $category_code == '')
			{	
				$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->whereNull('category_code')->where('parent_code', $parent_code)->where('menu_type', 'single')->get();
			}

			if ($custom_permissions !== null)
			{
				foreach ($custom_permissions as $key0 => $value0) 
				{
					$json_decode = json_decode($value0['permissions'], true);

					if (is_array($json_decode))
					{
						foreach ($json_decode as $key1 => $value1) 
						{
							if ($value1 == 'read data')
							{
								$increment += 1;
							}
						}
					}
				}
			}
		}

		return $increment;
	}
}

if ( ! function_exists('checkTotalAccessViewMenuPermissionForMenuCategoryV2'))
{
	function checkTotalAccessViewMenuPermissionForMenuCategoryV2($category_code)
	{
		$increment = 0;
		$user = new Account();

		if ($category_code !== NULL || $category_code !== '')
		{
			$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->where('category_code', $category_code)->get();
		}
		elseif ($category_code == NULL || $category_code == '')
		{
			$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->whereNull('category_code')->get();
		}

		if ($custom_permissions !== NULL)
		{
			foreach ($custom_permissions as $key0 => $value0) 
			{
				$json_decode = json_decode($value0['permissions'], true);

				if (is_array($json_decode))
				{
					foreach ($json_decode as $key1 => $value1) 
					{
						if ($value1 == 'read data')
						{
							$increment += 1;
						}
					}
				}
			}
		}

		return $increment;
	}
}

// ------------------------ Menu Feature v2 End ------------------ //

if ( ! function_exists('checkIsAdmin'))
{
	function checkIsAdmin()
	{
		$user = new Account();

		return $user->isAdmin();
	}
}

if ( ! function_exists('getStatusAccount'))
{
	function getStatusAccount($key)
	{
		$getData = Account_Status::find($key);

		if ($getData)
		{
			return '<span class="'.$getData->class_name.'">'.$getData->name.'</span>';
		}

		return null;
	}
}

if ( ! function_exists('getStatusArticle'))
{
	function getStatusArticle($key)
	{
		$getData = Article_Status::where('code_name', $key)->first();

		if ($getData)
		{
			return '<span class="'.$getData->class_name.'">'.$getData->name.'</span>';
		}

		return null;
	}
}

if ( ! function_exists('formatSizeUnits'))
{
	function formatSizeUnits($bytes)
	{
		if ($bytes < 1024) 
		{
			return $bytes.' B';
		} 
		elseif ($bytes < 1048576) 
		{
			return round($bytes / 1024, 2).' KB';
		}
		elseif ($bytes < 1073741824) 
		{
			return round($bytes / 1048576, 2).' MB';
		}
		elseif ($bytes < 1099511627776) 
		{
			return round($bytes / 1073741824, 2).' GB';
		}
		elseif ($bytes < 1125899906842624) 
		{
			return round($bytes / 1099511627776, 2).' TB';
		}
		elseif ($bytes < 1152921504606846976) 
		{
			return round($bytes / 1125899906842624, 2).' PB';
		}
		elseif ($bytes < 1180591620717411303424) 
		{
			return round($bytes / 1152921504606846976, 2).' EB';
		}
		elseif ($bytes < 1208925819614629174706176) 
		{
			return round($bytes / 1180591620717411303424, 2).' ZB';
		}
		else 
		{
			return round($bytes / 1208925819614629174706176, 2).' YB';
		}
	}
}

if ( ! function_exists('formatSizeUnitsOnlyNumber'))
{
	function formatSizeUnitsOnlyNumber($bytes)
	{
		if ($bytes >= 1024) // KB
		{
			$bytes = $bytes / 1024;
		}
		elseif ($bytes > 1) // Bytes
		{
			$bytes = $bytes;
		}
		elseif ($bytes == 1) // Byte
		{
			$bytes = $bytes;
		}
		else
		{
			$bytes = 0;
		}

		return $bytes;
	}
}

if ( ! function_exists('getImageURL'))
{
	function getImageURL($fileImage = '', $pathUrl = '')
	{
		if ($pathUrl !== '')
		{
			if (Storage::disk('public')->exists($pathUrl.'/'.$fileImage))
			{
				return Storage::url($pathUrl.'/'.$fileImage);
			}
		}

		return null;
	}
}

if ( ! function_exists('getImageURLAlt'))
{
	function getImageURLAlt($filePath)
	{
		if ($filePath !== '' && $filePath !== [])
		{
			if (Storage::disk('public')->exists($filePath))
			{
				return Storage::url($filePath);
			}
		}

		return null;
	}
}

if ( ! function_exists('getImageAvatarURL'))
{
	function getImageAvatarURL($filePath)
	{
		if ($filePath !== '')
		{
			if (Storage::disk('public')->exists($filePath))
			{
				return Storage::url($filePath);
			}
		}

		return null;
	}
}

if ( ! function_exists('getDataCustomPermissions'))
{
	function getDataCustomPermissions(string $idOrSlug, string $menuCode)
	{
		$output = [];

		$custom_permissions = Custom_Permissions::where('role_id', $idOrSlug)->where('menu_code', $menuCode)->get();

		if ($custom_permissions !== null)
		{
			foreach ($custom_permissions as $key0 => $value0) 
			{
				$json_decode = json_decode($value0['permissions'], true);

				if (is_array($json_decode))
				{
					foreach ($json_decode as $key1 => $value1) 
					{
						$output[$value1] = $value1;
					}
				}
			}

			return $output;
		}

		return false;
	}
}

if ( ! function_exists('getFileType'))
{
	function getFileType($file): string
	{
		$pos  = strpos($file, ';');

		return explode(':', substr($file, 0, $pos))[1];
	}
}

if ( ! function_exists('decodeBase64'))
{
	function decodeBase64($base64, $path = "media", $filename = "tmp", $ext = null, $maxFileSize = 1): string
	{
		// Check if Intervention Image is available
		if (class_exists('Intervention\Image\Laravel\Facades\Image')) 
		{
			return convertAndCompressWithIntervention($base64, $path, $filename, $ext, $maxFileSize);
		} 
		else 
		{
			// Fall back to original code
			return decodeBase64Original($base64, $path, $filename, $ext);
		}
	}
}

if ( ! function_exists('decodeBase64Original'))
{
	function decodeBase64Original($base64, $path, $filename, $ext)
	{
		$filetype = getFileType($base64);
		$extension = $ext ?? explode('/', $filetype)[1];
		$newFilename = "{$filename}-" . date('YmdHis') . "." . $extension;
		$newPath = $path . '/' . $newFilename;

		if (preg_match('/^data:image\/(\w+);base64,/', $base64) || 
			preg_match('/^data:application\/(\w+);base64,/', $base64)) 
		{
			$data = substr($base64, strpos($base64, ',') + 1);
			$data = base64_decode($data);

			// Save the original image
			Storage::put("public/{$newPath}", $data, 'public');

			return $newFilename;
		}
	}
}

if ( ! function_exists('convertAndCompressWithIntervention'))
{
	function convertAndCompressWithIntervention($base64, $path, $filename, $ext, $maxSize = 1)
	{
		try 
		{
			$filetype = getFileType($base64);
			$extension = $ext ?? explode('/', $filetype)[1];
			$newFilename = "{$filename}-" . date('YmdHis') . ".webp";
			$newPath = $path . '/' . $newFilename;

			$data = substr($base64, strpos($base64, ',') + 1);
			$data = base64_decode($data);

			// Save the original image
			Storage::put("public/{$newPath}", $data, 'public');

			// Convert the image to WebP format
			$image = \Intervention\Image\Laravel\Facades\Image::read($data);

			if ($image->width() > 2048 || $image->height() > 2048) 
			{
				$image->scale(width: 2048, height: 2048);
			}

			$image->toWebp();

			// Compress the image until its size is below or equal to 1MB
			$maxFileSize = $maxSize * 1024 * 1024; // 1MB in bytes by default
			$quality = 100; // Initial quality

			do 
			{
				$image->save(storage_path("app/public/{$newPath}"), $quality);
				$fileSize = filesize(storage_path("app/public/{$newPath}"));
				$quality -= 5;
			} 
			while ($fileSize > $maxFileSize && $quality >= 50);

			Storage::delete("public/{$path}/{$filename}.{$extension}");

			return $newFilename;
		} 
		catch (\Throwable $th) 
		{
			return null;
		}
	}
}

if ( ! function_exists('getVerticalSidebarMenuCollapse'))
{
	function getVerticalSidebarMenuCollapse()
	{
		if (request()->cookie('phoenix_vertical_sidebar_menu') == 'collapsed')
		{
			$output['container'] = 'arv7-with-vertical-menu-collapsed';
			$output['sidebar'] = 'arv7-sidebar-collapsed';
		}
		else
		{
			$output['container'] = '';
			$output['sidebar'] = '';
		}

		return $output;
	}
}

if ( ! function_exists('getIsVerticalSidebarMenuCollapse'))
{
	function getIsVerticalSidebarMenuCollapse()
	{
		if (request()->cookie('phoenix_vertical_sidebar_menu') == 'collapsed')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

if ( ! function_exists('getCurrentURIForPageTheme'))
{
	function getCurrentURIForPageTheme($segment)
	{
		$urlCurrent 		= url()->current();
		$explodeUrlCurrent 	= explode('/', $urlCurrent);
		$currentURI 		= isset($explodeUrlCurrent[$segment]) ? $explodeUrlCurrent[$segment] : 'login';

		$currentURI = ($currentURI == 'forgotpassword') ? 'forgotPassword' : $currentURI;
		$currentURI = ($currentURI == 'resetpassword') ? 'resetPassword' : $currentURI;

		return $currentURI;		
	}
}

if ( ! function_exists('getClassBackgroundImageSize'))
{
	function getClassBackgroundImageSize($backgroundImageSize)
	{
		$output = '';

		if ($backgroundImageSize == 'sm_size')
		{
			$output = 'ph-cover-background-image';
		}
		elseif ($backgroundImageSize == 'md_size')
		{
			$output = 'ph-cover-background-image';
		}
		elseif ($backgroundImageSize == 'lg_size')
		{
			$output = 'ph-cover-background-image';
		}
		elseif ($backgroundImageSize == 'full_size')
		{
			$output = 'ph-cover-background-image';
		}
		elseif ($backgroundImageSize == 'boi_size')
		{
			$output = 'ph-cover-background-image-2';
		}

		return $output;
	}
}

if ( ! function_exists('getBackgroundImageSize'))
{
	function getBackgroundImageSize($backgroundImageSize)
	{
		$output = '';

		if ($backgroundImageSize == 'sm_size')
		{
			$output = 'ph-size-small';
		}
		elseif ($backgroundImageSize == 'md_size')
		{
			$output = 'ph-size-medium';
		}
		elseif ($backgroundImageSize == 'lg_size')
		{
			$output = 'ph-size-large';
		}
		elseif ($backgroundImageSize == 'full_size')
		{
			$output = 'ph-size-full';
		}

		return $output;
	}
}

if ( ! function_exists('getImageSizeFromUrl'))
{
    function getImageSizeFromUrl($imagePath)
    {
        try 
        {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

            // Jika local file path (bukan URL)
            if ( ! preg_match('/^https?:\/\//', $imagePath)) 
            {
                $image = $manager->read($imagePath);
                return [$image->width(), $image->height()];
            }

            // Cek apakah URL berasal dari APP_URL (local app)
            $appUrl     = config('app.url');
            $parsedApp  = parse_url($appUrl);
            $parsedImg  = parse_url($imagePath);
            $appHost    = $parsedApp['host'] ?? '';
            $imgHost    = $parsedImg['host'] ?? '';

            $localHosts = ['localhost', '127.0.0.1', '::1', 'dekayana.id', $appHost];

            if (in_array($imgHost, $localHosts)) 
            {
                // Convert URL path ke local file path
                $urlPath   = $parsedImg['path'] ?? '';
                $localPath = public_path($urlPath);

                if (file_exists($localPath)) 
                {
                    $image = $manager->read($localPath);
                    return [$image->width(), $image->height()];
                }

                return false;
            }

            // Jika URL external, download dulu via Http::
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withHeaders(['Range' => 'bytes=0-32767'])
                ->get($imagePath);

            if ( ! $response->successful()) 
            {
                return false;
            }

            $image = $manager->read($response->body());
            return [$image->width(), $image->height()];

        } 
        catch (\Exception $e) 
        {
            return false;
        }
    }
}

if ( ! function_exists('getFitPaddingFromImageRatio'))
{
	function getFitPaddingFromImageRatio($imagePath)
	{
		$getImageSize = getImageSizeFromUrl($imagePath);

		if ($getImageSize !== false)
		{
			list($width, $height) = $getImageSize;

			if ($width && $height)
			{
				$aspectRatio = $height / $width;

				$percentage = Number::percentage($aspectRatio * 100, precision: 2); 

				return $percentage;
			}
		}

		return 0;
	}
}

if ( ! function_exists('getBackgroundImageSizeForBasedOnImageSize'))
{
    function getBackgroundImageSizeForBasedOnImageSize($backgroundImageSize, $imagePath)
    {
        $output = '';

        if ($backgroundImageSize == 'boi_size')
        {
            $output = ';padding-top: '.getFitPaddingFromImageRatio($imagePath);
        }

        return $output;
    }
}

/*
if ( ! function_exists('getBackgroundImageSizeForBasedOnImageSize'))
{
	function getBackgroundImageSizeForBasedOnImageSize($backgroundImageSize, $imagePath)
	{
		$output = '';

		if ($backgroundImageSize == 'boi_size')
		{
			$output = ';padding-top: '.getFitPaddingFromImageRatio($imagePath);
		}

		return $output;
	}
}

if ( ! function_exists('getFitPaddingFromImageRatio'))
{
	function getFitPaddingFromImageRatio($imagePath)
	{
		$getImageSize = @getimagesize($imagePath);
		
		if ($getImageSize !== false)
		{
			list($width, $height) = $getImageSize;

			if ($width && $height)
			{
				$aspectRatio = $height / $width;

				$percentage = Number::percentage($aspectRatio * 100, precision: 2); 

				return $percentage;
			}
		}

		return 0;
	}
}
*/

?>