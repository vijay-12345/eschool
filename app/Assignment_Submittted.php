<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Session_Table, App\Student, App\Teacher ,App\Attachments_Table;
use Carbon\Carbon;
use App\User,App\Notifaction;
use DB;


class Assignment_Submittted extends Model
{
    protected $table = 'assignment_submittted';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','assignment_id','student_id','submit_date','details','attachment_available'];
	
	protected $hidden = [];	
	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }
    public function student()
    {
        return $this->hasOne('App\Student','id','student_id')->where('deleted','0');
    }

    public function getTable(){
    	return $this->table;
    }

	public function submitAssignment($request){
			$day_date = Carbon::now()->format('Y-m-d H:i:s');
            //$day_date = date('Y-m-d H:i:s');
            
            $assignment_data = Assignment::find($request->assignment_id);
            if($assignment_data->due_date < $day_date)
                return;    
    		$insertDate = [ 'school_id'=>$request->school_id,
                            'assignment_id'=>$request->assignment_id,
                            'details'=>$request->details,
                            'submit_date'=>$day_date,
                            "student_id"=>$request->user_id,
                	];
			if(!empty($request->file_url)){
				$insertDate["attachment_available"]=1;
			 }

		  	if(!empty($request->id)){
                $id =$request->id;
                self::where(['id'=>$id])->update($insertDate ) ; 
                (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'update');
             }else{
                $assignment_submittted_data = self::where(["student_id"=>$request->user_id,
                                                    'assignment_id'=>$request->assignment_id])->first();
                if($assignment_submittted_data){
                    $id = $assignment_submittted_data->id;
                    self::where(['id'=>$assignment_submittted_data->id])->update($insertDate);
                }
    			else
                    $id = self::create($insertDate )->id;
                (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'add');
             }
			  (new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
			 return $id;
    }

    public function getSubmittedAssignment($request){

    	$objUser=new User();
		if(empty($request->teacher_class_subject_id)){
	  		$objTeacher_Class_Subject=new Teacher_Class_Subject();
        	$request->teacher_class_subject_id = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_ids, $request->school_id);
        }
        $select='assignment_submittted.id as id ,details ,submit_date,student_id';

		$obj = self::select(DB::raw($select))->where(['assignment_submittted.school_id'=>$request->school_id,'assignment_submittted.assignment_id'=>$request->assignment_id]);
        if($request->role == 'student' && (!empty($request->user_id)))
            $obj->where('assignment_submittted.student_id',$request->user_id);
        $obj->with("file_url");
		$obj->groupby('assignment_submittted.id');

        if(!empty($request->page_order)){
            $obj->orderby($request->page_order[0],$request->page_order[1]); 
        }
		if(!empty($request->page_limit)){
        	$data = $obj->paginate($request->page_limit)->toArray();
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]["student"]= $objUser->getUserDetails($value["student_id"] ,'student');
            }
        }else{
            $data = $obj->get()->toArray();
    		foreach ($data as $key => $value) {
    			$data[$key]["student"]= $objUser->getUserDetails($value["student_id"] ,'student');
    		}
        }
        return  $data;

    }
   

}
