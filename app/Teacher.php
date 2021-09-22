<?php

namespace App;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Teacher_Class_Subject;
class Teacher extends Authenticatable
{
    protected $table = 'teacher';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','name','isProfilePic','profile_pic_url','employee_id','date_of_joining','dob','email','address','login_id','password','phone_no','pass_code','device_token','token','updated_at'];
	
	protected $hidden = [];

	public function teacher()
    {
        return $this->hasOne('App\Teacher_Class_Subject','teacher_id','id');
    }

    public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
    }

     public function getTeacherList($request=""){
        $obj=self::with('file_url') ;
        $obj->where('teacher.deleted',0);
        if(session('user_school_id')!=''){
            $obj->where([ 'school_id'=>session('user_school_id')]);
        }
        return $obj->get();
    }

}
