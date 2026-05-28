<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use App\Models\Awesome_Admin\Public_Status;

class Custom_Permissions extends Model
{
	use HasFactory;

	protected $table = 'custom_permissions';

	public $timestamps = false;

	protected $guarded = [
		'id',
	];
}
