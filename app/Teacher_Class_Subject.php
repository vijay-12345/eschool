<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Teacher;
use DB;

class Teacher_Class_Subject extends Model
{
    protected $table = 'teacher_class_subject';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','teacher_id','subject_class_id'];
	
	protected $hidden = [];	

	public function teacher()
    {
        return $this->belongsTo('App\Teacher','teacher_id','id');
    }
    
    public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
    }
	
    public function getTeacherClassSubjectIds($class_section_id,$subject_ids, $school_id){
    		
    		$sub= DB::table('subject_class')->select(DB::raw("t.id"))
            		->where(['subject_class.class_section_id'=>$class_section_id,
	                    'subject_class.school_id'=>$school_id,
                            'subject_class.deleted'=>0,
	                ]);
	         if(!empty($subject_ids)){
	            if(!is_array($subject_ids))
					$subject_ids = [$subject_ids];
				$sub->whereIn('subject_class.subject_id',$subject_ids);
	         }       

			// $sub->join('class_section',	'class_section.id','=',"subject_class.class_section_id");

			$sub->Join('teacher_class_subject AS t', function($join) use($school_id) {
		        $join->on(["t.subject_class_id"=>"subject_class.id"]);
		        $join->where('t.school_id', '=', $school_id);
                $join->where('t.deleted', '=', '0');
		    });
		    $sub->where("t.id", ">" ,0);
			$teacher_class_subject_id=	$sub->pluck('id')->toArray();
			return $teacher_class_subject_id;
    }

    public  function addUpdateTeacherClassSubject($teacher_id, $school_id, $teacherClassSection_ids){
    	if(!empty($teacherClassSection_ids) && !empty($teacher_id)){
    		 $row= self::where(compact('teacher_id','school_id'))->update(['deleted'=>1]);
    		 foreach($teacherClassSection_ids as $teacherClassSub){
                $data=[
                    'school_id'=>$school_id,
                    'teacher_id'=>$teacher_id,
                    'subject_class_id'=>$teacherClassSub
                ];
                $row = self::where($data)->get()->toArray();
                if(empty($row)){
                     DB::table('teacher_class_subject')->insert($data);
                }else{
                	self::where(['id'=>$row[0]['id']])->update(['deleted'=>0]);
    		    }
            }
    	}
    }

    public  function getClassSectionSubject($teacher_id, $school_id){
        $data = self::select('teacher_class_subject.*','subject_master.id as subject_master_id','subject_master.subject_name','class_section.id as class_section_id','class_section.class_name','class_section.section_name');
        $data = $data->where('teacher_class_subject.school_id',$school_id)
                    ->where('teacher_class_subject.teacher_id',$teacher_id)
                    ->where('teacher_class_subject.deleted',0);

        $data->Join('subject_class', function($join) use($school_id) {
                $join->on('subject_class.id','=','subject_class_id');
                $join->where('subject_class.school_id', '=', $school_id);
            });
        $data->Join('subject_master', function($join) use($school_id) {
                $join->on('subject_master.id','=','subject_class.subject_id');
            });
        $data->Join('class_section', function($join) use($school_id) {
                $join->on('class_section.id','=','subject_class.class_section_id');
                $join->where('class_section.school_id', '=', $school_id);
            });
        $data->groupby('teacher_class_subject.id');
        $data = $data->get()->toArray();
                    
        return $data;
    }

    public function getClassSubjectUsingTeacherClassSubject($teacher_class_subject_id)
    {
        if(!empty($teacher_class_subject_id))
        {
            $data = self::select('teacher_class_subject.*','subject_master.id as subject_master_id','subject_master.subject_name','class_section.id as class_section_id',
                                'class_section.class_name','class_section.section_name')
                    ->join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
                    ->join('subject_master','subject_master.id','=','subject_class.subject_id')
                    ->join('class_section','class_section.id','=','subject_class.class_section_id')
                    ->where('teacher_class_subject.id',$teacher_class_subject_id)
                    ->first();
            return $data;
        }
    }
}
