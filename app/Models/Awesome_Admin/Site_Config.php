<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site_Config extends Model
{
	use HasFactory;

	protected $table = 'site_config';

	protected $guarded = [
		'id',
	];
}
