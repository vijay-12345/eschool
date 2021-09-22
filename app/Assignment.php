<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Teacher_Class_Subject, App\Teacher;
use App\Session_Table, App\Student, App\Attachments_Table;
use App\User, App\Notifaction, App\Assignment_Submittted;
use DB;

class Assignment extends Model
{
    protected $table = 'assignment';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','teacher_class_subject_id','title','assignment_description','due_date','created_date',
						'attachment_available'];
	
	protected $hidden = [];	

 	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
    }
    
	public function teacher_class_subject()
    {
        return $this->belongsTo('App\Teacher_Class_Subject','teacher_class_subject_id','id')
        				->with(array('teacher'=>function($query){
                                        $query->select('id','name');
                                    }));
    }


    public function addUpdateAssignmentbyTeacher($request){

    		$insertDate = ['school_id'=>$request->school_id,
                            'teacher_class_subject_id'=>$request->teacher_class_subject_id,
                            'title'=>$request->title,
                            'assignment_description'=>$request->assignment_description,
                            'due_date'=>$request->due_date
                	];
			if(!empty($request->file_url)){
				$insertDate["attachment_available"]=1;
			 }

             if(!empty($request->id)){
                $id =$request->id;
                self::where(['id'=>$id])->update($insertDate )  ;
                (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'update');
             }
             else{		 
                 $id = self::create($insertDate )->id;
                 (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'add');
             }
		    (new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
			return $id;
    }

    public function addUpdateAssignmentbyTeacherWebPanel($request){

            $insertDate = ['school_id'=>$request->school_id,
                            'teacher_class_subject_id'=>$request->teacher_class_subject_id,
                            'title'=>$request->title,
                            'assignment_description'=>$request->assignment_description,
                            'due_date'=>$request->due_date
                    ];
            if(!empty($request->image)){
                $insertDate["attachment_available"]=1;
             }

             if(!empty($request->id)){
                $id =$request->id;
                self::where(['id'=>$id])->update($insertDate );
                (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'update');
             }
             else{       
                 $id = self::create($insertDate )->id;
                 (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'add');
             }

             $urls = (new Attachments_Table())->uploadAttechments($request);

             $array =[];
             foreach ($urls as $key => $url) {
                 $array[$key]['url'] = $url;
                 $array[$key]['file_label'] = $request->file_label[0];
             }
             $request->file_url = $array;
             if(!empty($request->file_url))
                    (new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());

            return $id;
    }


    public function getAssignment($request){

    	$objUser=new User();
		if(empty($request->teacher_class_subject_id)){
	  		$objTeacher_Class_Subject=new Teacher_Class_Subject();
        	$request->teacher_class_subject_id = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_ids, $request->school_id);
        }
        
        $select='assignment.id as id ,attachment_available,teacher_class_subject_id ,title,assignment_description,due_date,created_date, teacher_class_subject.teacher_id ';

		$obj = self::select(DB::raw($select))->where([ 'assignment.school_id'=>$request->school_id]);

        if(!empty($request->teacher_class_subject_id)){
	  		if(!is_array($request->teacher_class_subject_id))
				$request->teacher_class_subject_id = [$request->teacher_class_subject_id];
			$obj->whereIn('teacher_class_subject_id',$request->teacher_class_subject_id);
        }
        if(empty($request->teacher_class_subject_id)){
            return  NULL;
        }

        $obj->join('teacher_class_subject',["teacher_class_subject.id"=>"assignment.teacher_class_subject_id"]);
        $obj->join('teacher',["teacher.id"=>"teacher_class_subject.teacher_id"]);
        $obj->with("file_url");
		$obj->groupby('assignment.id');

        if(!empty($request->page_order)){
            $obj->orderby($request->page_order[0],$request->page_order[1]); 
        }
        if(!empty($request->page_limit)){
        	$data = $obj->paginate($request->page_limit)->toArray();
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]["teacher"]= $objUser->getUserDetails($value["teacher_id"] ,'teacher');
            }

        }else{
	        
            $data = $obj->get()->toArray();
    		foreach ($data as $key => $value) {
    			$data[$key]["teacher"]= $objUser->getUserDetails($value["teacher_id"] ,'teacher');
    		}
        }
        return  $data;

    }
    
    public function getLibraryAssignment(){

    	$assignments= Assignment::select('assignment.id','assignment.title','assignment.attachment_available','assignment.assignment_description','assignment.due_date','assignment.created_date')
                 ->where('assignment.school_id',session('user_school_id'))
                ->orderBy('assignment.created_date','desc')
                ->take(10)
                 ->get()->toArray();
         foreach($assignments as $assignment){
             if($assignment['attachment_available'] ==1){
                 $assignment['total_attachments']=$this->getTotalAttachments($assignment['id']);
             }else{
                 $assignment['total_attachments']=0;
             }
             $data[]=$assignment;
             
         }
        return  $data; 

    }
    
    public function getLibraryAssignmentByFilter($request){
        $data=array();
        $assignments= Assignment::select('assignment.id','assignment.title','assignment.attachment_available','assignment.assignment_description','assignment.due_date','assignment.created_date')
                 ->where('assignment.school_id',session('user_school_id'))
                 ->where('class_section.id',$request->class_section_id)
                 ->where('subject_master.id',$request->subject_id)
                 ->join('teacher_class_subject', 'teacher_class_subject.id','assignment.teacher_class_subject_id')
                 ->join('subject_class', 'teacher_class_subject.subject_class_id','subject_class.id')
                 ->join('class_section', 'subject_class.class_section_id','class_section.id')
                 ->join('subject_master', 'subject_class.subject_id','subject_master.id')
                 ->get()->toArray();
        foreach($assignments as $assignment){
            if($assignment['attachment_available']==1){
                $assignment['total_attachments']=$this->getTotalAttachments($assignment['id']);
            }else{
                $assignment['total_attachments']=0;
            }
            $data[]=$assignment;
        }
        return $data;
    }


     public function getMyCreatedAssingment($request){
        if(empty($request->teacher_class_subject_id)){
            $objTeacher_Class_Subject=new Teacher_Class_Subject();
            $request->teacher_class_subject_id = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_ids, $request->school_id);
        }

        $obj= self::select('assignment.*','teacher.id as teacher_id')
                ->join('teacher_class_subject','teacher_class_subject.id','=','assignment.teacher_class_subject_id')
                ->join('teacher','teacher.id','=','teacher_class_subject.teacher_id')
                ->where(['assignment.school_id'=>$request->school_id]);

        if(!empty($request->teacher_class_subject_id)){
            if(!is_array($request->teacher_class_subject_id))
                $request->teacher_class_subject_id = [$request->teacher_class_subject_id];
            $obj->whereIn('assignment.teacher_class_subject_id',$request->teacher_class_subject_id)->with("file_url");
        }
        if(empty($request->teacher_class_subject_id)){
            return  NULL;
        }        

        
        if(!empty($request->page_order)){
            $obj->orderby($request->page_order[0],$request->page_order[1]); 
        }
        $objUser = new User();
        if(!empty($request->page_limit)){
            $data = $obj->paginate($request->page_limit)->toArray();
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]["teacher"]= $objUser->getUserDetails($value["teacher_id"] ,'teacher');
                if($request->role == 'student')
                    $data['data'][$key]["assignment_submitted_details"]= $this->getsubmittedAssigmentStudentDetailForCreatedAssingment($value,$request);
                $data['data'][$key]["all_students"]= count($this->getAllStudentList($value));
                $data['data'][$key]["submitted_students"]= count($this->getSubmitStudentList($value));
            }
        }else{
            $data = $obj->get()->toArray();
            foreach ($data as $key => $value) {
                $data[$key]["teacher"]= $objUser->getUserDetails($value["teacher_id"] ,'teacher');
                if($request->role == 'student')
                    $data[$key]["assignment_submitted_details"]= $this->getsubmittedAssigmentStudentDetailForCreatedAssingment($value,$request);
                $data[$key]["all_students"]= count($this->getAllStudentList($value));
                $data[$key]["submitted_students"]= count($this->getSubmitStudentList($value));
            }
        }
        return $data;
     }


     public function getAssingmentSubmittedStudentList($request){
        $obj= self::where(['school_id'=>$request->school_id,'teacher_class_subject_id'=>$request->teacher_class_subject_id])->with("file_url")->find($request->assignment_id);
        
        if(!empty($obj))
        {

            $data_student_submitted_assignment = $this->getSubmitStudentList($obj->toArray());
            $submitted_assignment_student_id = [];
            foreach ($data_student_submitted_assignment as $value) {
                $submitted_assignment_student_id[] = $value['student_id'];
            }
            $class_section_details = User::getclass_section($request);
            $data_student_not_submitted_assignment = Student::where('class_section_id',$class_section_details['id']) 
                                                            ->where('school_id',$class_section_details['school_id'])
                                                            ->where('student.deleted',0)
                                                            ->whereNotIn('id',$submitted_assignment_student_id)
                                                            ->get();
            $result = array_merge($data_student_submitted_assignment->toArray(),$data_student_not_submitted_assignment->toArray());                                                
            $data = $result;
            return $data;            
        }
        else
            return $obj;
     }

     public function getAssingmentOnlySubmittedStudentList($request){
        $obj= self::where(['school_id'=>$request->school_id,'teacher_class_subject_id'=>$request->teacher_class_subject_id])->with("file_url")->find($request->assignment_id);
        
        if(!empty($obj))
        {

            $data_student_submitted_assignment = $this->getSubmitStudentList($obj->toArray());

            return $data_student_submitted_assignment;            
        }
        else
            return $obj;
     }

     public function getAssingmentNotSubmittedStudentList($request){
        $obj= self::where(['school_id'=>$request->school_id,'teacher_class_subject_id'=>$request->teacher_class_subject_id])->with("file_url")->find($request->assignment_id);
        
        if(!empty($obj))
        {

            $data_student_submitted_assignment = $this->getSubmitStudentList($obj->toArray());
            $submitted_assignment_student_id = [];
            foreach ($data_student_submitted_assignment as $value) {
                $submitted_assignment_student_id[] = $value['student_id'];
            }
            $class_section_details = User::getclass_section($request);
            $data_student_not_submitted_assignment = Student::where('class_section_id',$class_section_details['id']) 
                                                            ->where('school_id',$class_section_details['school_id'])
                                                            ->whereNotIn('id',$submitted_assignment_student_id)
                                                            ->get();
            $data = $data_student_not_submitted_assignment->toArray();                                                
            return $data;            
        }
        else
            return $obj;

     }   

     public function getsubmittedAssigmentStudentDetailForCreatedAssingment($data,$request){
         $obj= Assignment_Submittted::where("assignment_id",$data["id"])->where('student_id',$request->user_id)->with('student')->with('file_url');
         return $obj->first();
     }

     public function getsubmittedAssigmentStudentDetail($request){
         $obj= Assignment_Submittted::where("id",$request->assignment_submittted_id)->with('student')->with('file_url');
         return $obj->get();
     }

    function getAllStudentList($data){
        $select="student.id as student_id,name,registration_no,roll_no,class_name,section_name";
        $obj = Student::select(DB::raw($select))->where('student.school_id',$data["school_id"]);
        $obj->where('student.deleted',0);
        $obj->Join('class_section','class_section.id','=','student.class_section_id')
        ->Join('subject_class','subject_class.class_section_id','=','class_section.id')
        ->Join('teacher_class_subject','teacher_class_subject.subject_class_id','=','subject_class.id')
        ->where('teacher_class_subject.id',$data["teacher_class_subject_id"])
        ->where('subject_class.deleted',0);
        return $obj->get();
    }

    function getSubmitStudentList($data){
        $select="student.id as student_id,name,registration_no,roll_no,class_name,section_name,submit_date,assignment_submittted.id as assignment_submittted_id";
        $obj =Student::select(DB::raw($select))->where('student.school_id',$data["school_id"])
        ->where('student.deleted',0)
        ->Join('assignment_submittted','assignment_submittted.student_id','=','student.id')
        ->Join('class_section','class_section.id','=','student.class_section_id')
        ->Join('subject_class','subject_class.class_section_id','=','class_section.id')
        ->Join('teacher_class_subject','teacher_class_subject.subject_class_id','=','subject_class.id')
        ->where('teacher_class_subject.id',$data["teacher_class_subject_id"])
        ->where("assignment_submittted.assignment_id",$data["id"])
        ->where("class_section.deleted",0)
        ->where("subject_class.deleted",0);
        return $obj->get();
    }
    
    function getTotalAttachments($assignmentId){
        $total_attachment= Attachments_Table::select(DB::raw("COUNT(attachments_table.id) as total_attachments"))
                 ->where('attachments_table.reference_id',$assignmentId)
                 ->where('attachments_table.table_type','assignment')
                 ->first()->toArray();
        return  $total_attachment['total_attachments'];
        
        
    }
    

}
