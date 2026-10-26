<?php

namespace App\Http\Resources\Manage_Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Article\Article_Status;
use Illuminate\Support\Str;

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

		$author = $this->relationLoaded('author') ? $this->author : get_user($this->user_id);
		$authorName = $author?->fullname ?: 'Unknown';
		$getTitle = Str::limit((string) $this->title, 100, '...');
		$getAuthorName = Str::limit($authorName, 25, '...');
		$status = $this->getStatus ?: Article_Status::where('code_name', (string) $this->status)->first();
	
		return 
		[
			'id'						=> $this->id,
			'uri'						=> $this->uri,
			'user_id'					=> $this->user_id,
			'category_id'				=> $this->category_id,
			'subcategory_id'			=> $this->subcategory_id,
			'title'						=> $getTitle,
			'title_full'                => $this->title,
			'author_full'               => $authorName,
			'category_name'             => $this->whenLoaded('category', fn () => $this->category?->name),
			'excerpt'                   => Str::limit(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $this->content), ENT_QUOTES | ENT_HTML5, 'UTF-8'))), 220),
			'status_code'               => $status?->code_name,
			'status_name'               => $status?->name,
			'content'					=> $this->content,
			'tags'						=> $this->tags,
			'thumb_s'					=> $this->thumb_s,
			'thumb_l'					=> $this->thumb_l,
			'visibility'				=> $this->visibility,
			'author'					=> $getAuthorName,
			'status'					=> $this->status,
			'status_formatted'           => $status ? '<span class="'.e($status->class_name).'">'.e($status->name).'</span>' : null,
			'get_status'				=> $this->getStatus,
			'scheduled'					=> $this->scheduled,
			'updated_at'				=> $this->updated_at,
			'created_at'				=> $this->created_at,
			'created_at_readable'		=> $this->created_at->format('Y-m-d H:i:s'),
			'created_date_display'      => $this->created_at->translatedFormat('d M Y'),
			'created_time_display'      => $this->created_at->format('H:i'),
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
