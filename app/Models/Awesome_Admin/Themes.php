<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Themes extends Model
{
	use HasFactory;

	protected $table = 'themes';

	protected $guarded = [
		'id',
	];
}
