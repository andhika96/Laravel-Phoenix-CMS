<?php

namespace App\Http\Resources\Manage_Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManageArticleListResource extends JsonResource
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
			'visibility'				=> $this->visibility,
			'author'					=> $getAuthorName,
			'status'					=> $this->status,
			'status_formatted'			=> getStatusArticle($this->status),
			'get_status'				=> $this->getStatus,
			'scheduled'					=> $this->scheduled,
			'updated_at'				=> $this->updated_at,
			'created_at'				=> $this->created_at,
			'created_at_readable'		=> $this->created_at->format('Y-m-d H:i:s'),
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
