<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attachments_Table;
use App\Teacher_Class_Subject;
use App\User, App\Notifaction;
use App\ReadMapping;


use DB;



class Forum extends Model
{
    protected $table = 'forum_table';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','teacher_class_subject_id','reply_from','who_replyed','attachment_available','message_content','date'];
	
	protected $hidden = [];	

	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }



    public function getTable(){
    	return $this->table;
    }
    
	public function addForum($request){
		$objAttachmentsTable= new Attachments_Table();
		if(empty($request->teacher_class_subject_id)){
	  		$objTeacher_Class_Subject=new Teacher_Class_Subject();
        	$request->teacher_class_subject_id = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_ids, $request->school_id);
        }
       
        if(!empty($request->teacher_class_subject_id))
        {
        	if(!is_array($request->teacher_class_subject_id))
        		$request->teacher_class_subject_id=[$request->teacher_class_subject_id];
       
	        foreach ($request->teacher_class_subject_id as  $teacher_class_subject_id) {
	        	
				$insertDate = [ 'school_id'=>$request->school_id,
		                        'teacher_class_subject_id'=>$teacher_class_subject_id,
			                    'reply_from'=>$request->user_id,
		                        'who_replyed'=>$request->role,
		                        "message_content"=>$request->message_content,
		                        'date'=>date('Y-m-d H:i:s')
		                	];
				if(!empty($request->file_url)){
					$insertDate["attachment_available"]=1;
				 }

			  	if(!empty($request->id)){
	                $id =$request->id;
	                self::where(['id'=>$id])->update($insertDate )  ;
	                (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'update');
	             }else{
				 	$id = self::create($insertDate )->id;
				 	(new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'reply');
	             }
				 (new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());      	
	        }
	 		return ['id'=>$id];
	 	}
	}

	public function addForumWebPanel($request){
		$objAttachmentsTable= new Attachments_Table();
		if(empty($request->teacher_class_subject_id)){
	  		$objTeacher_Class_Subject=new Teacher_Class_Subject();
        	$request->teacher_class_subject_id = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_ids, $request->school_id);
        }
       
        if(!empty($request->teacher_class_subject_id))
        {
        	if(!is_array($request->teacher_class_subject_id))
        		$request->teacher_class_subject_id=[$request->teacher_class_subject_id];
       
	        foreach ($request->teacher_class_subject_id as  $teacher_class_subject_id) {
	        	
				$insertDate = [ 'school_id'=>$request->school_id,
		                        'teacher_class_subject_id'=>$teacher_class_subject_id,
			                    'reply_from'=>$request->user_id,
		                        'who_replyed'=>$request->role,
		                        "message_content"=>$request->message_content,
		                        'date'=>date('Y-m-d H:i:s')
		                	];
				
				if(!empty($request->image)){
                    $insertDate["attachment_available"]=1;
                 } 

			  	if(!empty($request->id)){
	                $id =$request->id;
	                self::where(['id'=>$id])->update($insertDate )  ;

	             }else{
				 	$id = self::create($insertDate )->id;
	             }
	             $request->file_url = (new Attachments_Table())->uploadAttechments($request);
                 if(!empty($request->file_url))
                    (new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
     	
	        }
	 		return ['id'=>$id];
	 	}
	}


	public function getForumList($request,$last=''){
		$objUser=new User();
		
		$select='forum_table.id as id ,teacher_class_subject_id ,subject_class.class_section_id,subject_id,class_name,section_name,subject_name,who_replyed, message_content,date ,reply_from,forum_table.school_id';
		
		$obj = self::where([ 'forum_table.school_id'=>$request->school_id]);
		
	  	if(empty($request->teacher_class_subject_id)){
	  		$objTeacher_Class_Subject=new Teacher_Class_Subject();
        	$request->teacher_class_subject_id = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_ids, $request->school_id);
        }

        if(!empty($request->teacher_class_subject_id)){
	  		if(!is_array($request->teacher_class_subject_id))
				$request->teacher_class_subject_id = [$request->teacher_class_subject_id];
			$obj->whereIn('teacher_class_subject_id',$request->teacher_class_subject_id);
        }
        if(empty($request->teacher_class_subject_id)){
            return  NULL;
        }

  		$obj->join('teacher_class_subject',["teacher_class_subject.id"=>"forum_table.teacher_class_subject_id"]);
 		$obj->join('subject_class',["subject_class.id"=>"teacher_class_subject.subject_class_id"]);
		$obj->join('subject_master',["subject_master.id"=>"subject_class.subject_id"]);
		$obj->join('class_section',["class_section.id"=>"subject_class.class_section_id"]);
        
		if(!empty($last)){
			
			$forlast= $this->getUnreadInObj($obj);
			$UnreadIds=[];
			$grouplastids=[];
			foreach($forlast as $ids){
				$ids= explode(",", $ids );
				$grouplastids[]=last($ids);
				$UnreadIds[]=(new ReadMapping())->getUnreadIds($request,$this->getTable(),$ids);
			}
			$obj->whereIn('forum_table.id', $grouplastids);

		}else{
			$obj->groupby('forum_table.id');
		}
		$obj->select(DB::raw($select));
		$obj->with('file_url');

		if(!empty($request->page_order)){
			$obj->orderby($request->page_order[0],$request->page_order[1]);	
		}

		if(!empty($request->page_limit)){
        	$data = $obj->paginate($request->page_limit)->toArray();
            foreach ($data['data'] as $key => $value) {
            	if(!empty($last))
               		$data['data'][$key]["un_read_count"]= count($UnreadIds[$key]);
          		$data['data'][$key]["reply_from"]= $objUser->getUserDetails($value["reply_from"] ,$value["who_replyed"]);
            }
        }else{
            $data = $obj->get()->toArray();
    		foreach ($data as $key => $value) {
    			if(!empty($last))
               		$data[$key]["un_read_count"]= count($UnreadIds[$key]);
               $data[$key]["reply_from"]= $objUser->getUserDetails($value["reply_from"] ,$value["who_replyed"]);
    			
    		}
        }
		return $data;
	}

	public function getUnreadInObj($forlast){
		return $forlast->select(DB::raw("GROUP_CONCAT(forum_table.id) as id"))->groupBy('teacher_class_subject_id')->pluck('GROUP_CONCAT(forum_table.id) as id');
	}
	
}
