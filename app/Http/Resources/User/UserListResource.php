<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		// return parent::toArray($request);
	
		return 
		[
			'id'				=> $this->id,
			'uuid'				=> $this->uuid,
			'email'				=> $this->email,
			'username'			=> $this->username,
			'fullname'			=> $this->fullname,
			'status'			=> $this->status,
			'status_formatted'	=> getStatusAccount($this->status),
			'get_status'		=> $this->getStatus,
			'roles'				=> $this->roles,
			'role_name'			=> isset($this->roles[0]) ? $this->roles[0]['name'] : '',
			'suspended_at'		=> $this->suspended_at,
			'updated_at'		=> $this->updated_at,
			'created_at'		=> $this->created_at,
		];
	}
}
