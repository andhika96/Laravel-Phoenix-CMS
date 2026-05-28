<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
	use HasFactory;

	protected $table = 'permissions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name'
	];    

	protected $guarded = [
		'id',
	];
}
