<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class RemainNotifactions extends Model
{
	use Notifiable;
    protected $table = 'remain_notifactions';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','notification_details'];
	
	protected $hidden = [];
}