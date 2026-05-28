<?php

namespace App\Http\Resources\Manage_CoverImage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class ManageCoverImageEditResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		// return parent::toArray($request);

		$newOutputBgImageVars = null;
		$newOutputSlideshowVars = null;
	
		if (json_validate($this->cover_bgimage_vars))
		{
			foreach (json_decode($this->cover_bgimage_vars, true) as $key0 => $value0) 
			{
				foreach ($value0 as $key1 => $value1) 
				{
					if ($key1 == 'desktop_image')
					{
						$newOutputBgImageVars[$key0][$key1] = $value1;
						$newOutputBgImageVars[$key0]['desktop_image_full_url'] = ($value1 !== null && getImageURLAlt($value1) !== null) ? url(getImageURLAlt($value1)) : '';
					}
					elseif ($key1 == 'mobile_image')
					{
						$newOutputBgImageVars[$key0][$key1] = $value1;
						$newOutputBgImageVars[$key0]['mobile_image_full_url'] = ($value1 !== null && getImageURLAlt($value1) !== null) ? url(getImageURLAlt($value1)) : '';
					}
					else
					{
						$newOutputBgImageVars[$key0][$key1] = $value1;
					}
				}
			}
		}

		if (json_validate($this->cover_slideshow_vars))
		{
			foreach (json_decode($this->cover_slideshow_vars, true) as $key0 => $value0) 
			{
				foreach ($value0 as $key1 => $value1) 
				{
					if ($key1 == 'desktop_image')
					{
						$newOutputSlideshowVars[$key0][$key1] = $value1;
						$newOutputSlideshowVars[$key0]['desktop_image_full_url'] = ($value1 !== null && getImageURLAlt($value1) !== null) ? url(getImageURLAlt($value1)) : '';
					}
					elseif ($key1 == 'mobile_image')
					{
						$newOutputSlideshowVars[$key0][$key1] = $value1;
						$newOutputSlideshowVars[$key0]['mobile_image_full_url'] = ($value1 !== null && getImageURLAlt($value1) !== null) ? url(getImageURLAlt($value1)) : '';
					}
					else
					{
						$newOutputSlideshowVars[$key0][$key1] = $value1;
					}
				}
			}
		}

		return 
		[
			'id'						=> $this->id,
			'uri'						=> $this->uri,
			'user_id'					=> $this->user_id,
			'cover_type'				=> $this->cover_type,
			'cover_page_name'			=> $this->cover_page_name,
			// 'cover_bgimage_vars'		=> json_encode($this->cover_bgimage_vars),
			// 'cover_slideshow_vars'		=> json_encode($this->cover_slideshow_vars),
			// 'cover_bgimage_vars'		=> $this->cover_bgimage_vars,
			// 'cover_slideshow_vars'		=> $this->cover_slideshow_vars,
			'cover_bgimage_vars'			=> json_encode($newOutputBgImageVars),
			// 'cover_bgimage_vars_original'	=> $this->cover_bgimage_vars,
			'cover_slideshow_vars'			=> json_encode($newOutputSlideshowVars),
			// 'cover_slideshow_vars_original'	=> $this->cover_slideshow_vars,
			'cover_slideshow_direction'		=> $this->cover_slideshow_direction,
			'cover_is_active'				=> $this->cover_is_active,
			'updated_at'					=> $this->updated_at,
			'created_at'					=> $this->created_at,
			// 'asd'						=> $newOutputSlideshowVars
		];
	}

	protected function getImageURL($fileImage = '', $path = '')
	{
		if (Storage::disk('public')->exists($fileImage))
		{
			return Storage::url($fileImage);
		}

		return null;
	}
}
