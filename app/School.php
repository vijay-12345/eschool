<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class School extends Authenticatable
{
	use Notifiable;
    protected $table = 'school';
	protected $quard = 'school';
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','parent_id','name','school_url','address','phone_no','email','password','logo_url','login_id','pass_code','device_token','updated_at','token'];
	
	protected $hidden = [];

	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
    }

    public function getAlldata(){
    	return self::all();
    }
    public function getSchoolsByParent($request){
    	return self::where('parent_id',$request->id)->select('id','name','address','logo_url')->get();
    }
}