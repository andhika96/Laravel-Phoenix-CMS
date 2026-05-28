<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme_Settings extends Model
{
	use HasFactory;

	protected $table = 'theme_settings';

	protected $guarded = [
		'id',
	];
}
