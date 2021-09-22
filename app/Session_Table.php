<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use  App\Attachments_Table;
use  App\Teacher_Class_Subject;
use  App\Class_Section;
use App\User,App\Notifaction, App\Session_Attendance;
use DB;

class Session_Table extends Model
{
    protected $table = 'session_table';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','teacher_class_subject_id','topic','start_time','end_time','meeting_id','password',
						'online_class_url','content','date'];
	
	protected $hidden = [];	
	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
    }

 public function addUpdateSession($request){
			
		$insertData=[];

    	if(!empty($request["session_id"])){
    		$id= $request->session_id;
    		$insertData = self::find($id);
		}

    	if(!empty($request["date"])){
			$request["date"]=	date("Y-m-d",strtotime($request["date"]));
    	}
		foreach ($this->fillable as $key => $value) {
			if(!empty($request[$value]))
				$insertData[$value]=$request[$value];
		}

		if(!empty($insertData)){
			if(!empty($insertData["id"])){
				$insertData->save();
				 (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'update');
			}else{
				$id = self::create($insertData )->id;
				 (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'add');
			}
		 	return compact("id");
		}
    }


	public function getTodaysSession($request){
	 	$fillable_session_table = $this->fillable;
	 	$fillable_class_section = ['class_name','section_name'];
	 	$fillable_subject_master = ['subject_name'];
	 	$fillable_teacher = ['name'];
		if(empty($request->teacher_class_subject_id)){
	  		$objTeacher_Class_Subject=new Teacher_Class_Subject();
        	$request->teacher_class_subject_id = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_ids, $request->school_id);
        }
    
     	if(!empty($request->teacher_class_subject_id))
        {
        	if(!is_array($request->teacher_class_subject_id))
        		$request->teacher_class_subject_id=[$request->teacher_class_subject_id];
	
			$obj = DB::table($this->table)->select('session_table.id','session_table.date','session_table.topic','session_table.start_time','session_table.end_time','session_table.online_class_url','session_table.meeting_id','session_table.password','session_table.content','class_section.class_name','class_section.section_name','subject_master.subject_name',
	                                        'teacher.name as teacher_name');
			$todayDate=date('Y-m-d');
			if(!empty($request->school_id))
				$obj->where([ 'session_table.school_id'=>$request->school_id ]);
			$obj->whereIn( 'session_table.teacher_class_subject_id',$request->teacher_class_subject_id );
			$obj->join('teacher_class_subject',function($query){
				$query->on('teacher_class_subject.id','=','session_table.teacher_class_subject_id');
				$query->where('teacher_class_subject.deleted','0');
			});

	        $obj->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
	        ->Join('subject_master','subject_master.id','=','subject_class.subject_id')
	        ->Join('class_section','class_section.id','=','subject_class.class_section_id')
	        ->join('teacher',["teacher.id"=>"teacher_class_subject.teacher_id"]);
	        if(empty($request->type))
	        	$obj->where('session_table.date','>=', $todayDate);

	        if(!empty($request->search_string)){
				 $obj->where(function($query) use ($request ,$fillable_session_table,$fillable_class_section,$fillable_subject_master,$fillable_teacher){
					foreach ($fillable_session_table as $value) {
					$query->orWhere('session_table.'.$value, 'LIKE', "%".$request->search_string."%");
					}
					foreach ($fillable_class_section as $value) {
					$query->orWhere('class_section.'.$value, 'LIKE', "%".$request->search_string."%");
					}
					foreach ($fillable_subject_master as $value) {
					$query->orWhere('subject_master.'.$value, 'LIKE', "%".$request->search_string."%");
					}
					foreach ($fillable_teacher as $value) {
					$query->orWhere('teacher.'.$value, 'LIKE', "%".$request->search_string."%");
					}
				});
			}

			if(!empty($request->from) && !empty($request->to))
	        {
	            $obj->whereBetween('date',[$request->from,$request->to]);
	        }

	    
	        $obj->groupby('session_table.id');
	        $obj->orderby('start_time','ASC');


	        if(!empty($request->page_order)){
	            $obj->orderby($request->page_order[0],$request->page_order[1]); 
	        }
	        if(!empty($request->page_limit)){
	        	$data = $obj->paginate($request->page_limit)->toArray();
	        }else{
		        $data = $obj->get()->toArray();
	        }

	        if(!empty($request->role) == 'student')
	        {
	        	foreach ($data as $key => $value) {
	        		$session_attendance = Session_Attendance::where('session_table_id',$value->id)
	        												->where('user_id',$request->user_id)
	        												->where('status','1')
	        												->first();
	        		if(empty($session_attendance))
	        			$data[$key]->student_session_attendance = '0';
	        		else
	        			$data[$key]->student_session_attendance = '1';
	        	}
	        }

	        return $data;                    
		}
		return NULL;
    }
    
    public function deleteSession($request){
        $session_attendance = Session_Attendance::where('session_table_id',$request['id'])->select(DB::raw("COUNT(session_attendance.id) as total"))
                                                ->first();
        if($session_attendance['total']<1){
        $session = self::find($request['id']);
        Attachments_Table::where('reference_id', $request['id'])->where('table_type', 'assignment')->delete();
        return $session->delete();
        }else{
        return 0;
        }
    }

}
