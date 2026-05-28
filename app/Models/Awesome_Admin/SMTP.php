<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SMTP extends Model
{
	use HasFactory;

	protected $table = 'smtp_settings';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'smtp_service',
		'smtp_host',
		'smtp_username',
		'smtp_password',
		'smtp_port',
		'smtp_encryption',
		'smtp_sender_name',
		'smtp_sender_address'
	];    

	protected $guarded = [
		'id',
	];
}
