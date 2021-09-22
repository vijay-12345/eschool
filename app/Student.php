<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    protected $table = 'student';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','name','isProfilePic','profile_pic_url','registration_no','class_section_name','class_section_id','parent_name','email','address','parent_phono_no1','parent_phono_no2','login_id','password','phone_no','roll_no','pass_code','device_token','dob'];
	
	protected $hidden = [];

	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function class_section()
    {
        return $this->hasOne('App\Class_Section','id','class_section_id');
    }

    public function getTable(){
    	return $this->table;
    }

    public function getStudentList($request=""){
        $obj=self::with('file_url','class_section') ;
        $obj->where('student.deleted',0);
        if(session('user_school_id')!=''){
            $obj->where([ 'school_id'=>session('user_school_id')]);
        }
        
        if(!empty($request->class_section_id)){
             $obj->where([ 'class_section_id'=>$request->class_section_id]);
        }
        return $obj->get();
    }
}
