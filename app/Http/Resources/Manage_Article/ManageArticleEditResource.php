<?php

namespace App\Http\Resources\Manage_Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class ManageArticleEditResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		// return parent::toArray($request);

		$maxLengthTitle 		= 100;
		$maxLengthAuthorName	= 25;
		$getTitle 				= (strlen($this->title) > $maxLengthTitle) ? substr($this->title, 0, $maxLengthTitle).'...' : $this->title;
		$getAuthorName 			= (strlen(get_user($this->user_id)['fullname']) > $maxLengthAuthorName) ? substr(get_user($this->user_id)['fullname'], 0, $maxLengthAuthorName).'...' : get_user($this->user_id)['fullname'];
		$getThumbnailSmallUrl	= ($this->thumb_s !== null && Storage::disk('public')->exists($this->thumb_s)) ? url($this->getImageURL($this->thumb_s)) : null;
		$getThumbnailLargeUrl	= ($this->thumb_l !== null && Storage::disk('public')->exists($this->thumb_l)) ? url($this->getImageURL($this->thumb_l)) : null;
		
		return 
		[
			'id'						=> $this->id,
			'uri'						=> $this->uri,
			'user_id'					=> $this->user_id,
			'category_id'				=> $this->category_id,
			'subcategory_id'			=> $this->subcategory_id,
			'title'						=> $getTitle,
			'content'					=> $this->content,
			'tags'						=> $this->tags,
			'thumb_s'					=> $this->thumb_s,
			'thumb_l'					=> $this->thumb_l,
			'thumbnail_small_url'		=> $getThumbnailSmallUrl,
			'thumbnail_large_url'		=> $getThumbnailLargeUrl,
			'visibility'				=> $this->visibility,
			'author'					=> $getAuthorName,
			'status'					=> $this->status,
			'status_formatted'			=> getStatusArticle($this->status),
			'get_status'				=> $this->getStatus,
			'scheduled'					=> $this->scheduled,
			'updated_at'				=> $this->updated_at,
			'created_at'				=> $this->created_at->format('m-d-Y'),
			'created_at_hours'			=> $this->created_at->format('H'),
			'created_at_minutes'		=> $this->created_at->format('i'),
			'created_at_readforhuman'	=> $this->created_at->diffForHumans(),
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
