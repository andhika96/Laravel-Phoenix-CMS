<?php

namespace App\Models\Notification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LPNotification extends Model
{
	use HasFactory;

	protected $table = 'notifications';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'user_id',
		'from_id',
		'from_fullname',
		'to_id',
		'to_fullname',
		'type',
		'icon',
		'title',
		'message',
		'hasread'
	];    

	protected $guarded = [
		'id',
	];
}
