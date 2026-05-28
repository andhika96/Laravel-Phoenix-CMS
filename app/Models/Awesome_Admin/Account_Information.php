<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account_Information extends Model
{
	use HasFactory;

	protected $table = 'user_information';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'user_id',
		'avatar',
		'cover_image',
		'cover_image_position',
		'birthdate',
		'gender',
		'phone_number',
		'about'
	];    

	protected $guarded = [
		'id',
	];
}
