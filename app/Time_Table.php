<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
use  App\Teacher_Class_Subject,App\Notifaction,App\Attachments_Table;
use Auth;
class Time_Table extends Model
{
    protected $table = 'time_table';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','type','teacher_id','class_section_id','content','attachment_available','date'];
	
	protected $hidden = [];	

	public function file_url()
    {
        return $this->hasOne('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }
    public function class_section()
    {
        return $this->hasOne('App\Class_Section','id','class_section_id')->where('deleted',0);
    }
    public function getTable(){
    	return $this->table;
    }
    
	public function getTimeTable($request){
			
		if(empty($request->teacher_class_subject_id)){
	  		$objTeacher_Class_Subject=new Teacher_Class_Subject();
        	$request->teacher_class_subject_id = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_ids, $request->school_id);
        }
        $time_table_fillable=$this->fillable;
		$class_section_fillable =['class_name','section_name'];
		// print_r($class_section_fillable);
		// // $filtered = Arr::except($class_section_fillable, ['id.school_id']);
		// $filtered = Arr::except($class_section_fillable, 'school_id');
		// print_r($filtered);
		// die;
		$obj = self::with('file_url','class_section')->groupby('time_table.id');

		if($request->role=='student' && !empty($request->class_section_id)){
			 $obj->where('time_table.class_section_id',$request->class_section_id)
			->where([ 'time_table.school_id'=>$request->school_id]);

		}elseif($request->role=='teacher'){
			$obj->select('time_table.id','time_table.school_id','time_table.type','time_table.teacher_id','time_table.class_section_id','time_table.content','time_table.attachment_available','time_table.date','class_section.class_name','class_section.section_name','class_section.class_teacher_id','class_section.status');
			$obj->where('time_table.type',$request->role);
			$obj->join('class_section','class_section.id',"=",'time_table.class_section_id');
			$obj->where([ 'time_table.teacher_id'=>$request->user_id]);
			$obj->where([ 'time_table.school_id'=>$request->school_id]);
			
		}elseif(session('user_school_id')!='')
                $obj->where('school_id','=',session('user_school_id'));

		if(!empty($request->search_string)){
				 $obj->where(function($query) use ($request ,$time_table_fillable,$class_section_fillable){
					foreach ($time_table_fillable as $value) {
							$query->orWhere('time_table.'.$value, 'LIKE', "%".$request->search_string."%");
					}
					if($request->role == 'teacher')
					{
						foreach ($class_section_fillable as $value) {
							$query->orWhere('class_section.'.$value, 'LIKE', "%".$request->search_string."%");
						}
					}
				});
		}


		if(!empty($request->page_order)){
		    $obj->orderby($request->page_order[0],$request->page_order[1]); 
		}
	
	    if(!empty($request->page_limit)){
        	$data = $obj->paginate($request->page_limit)->toArray();
        }else{
	        $data = $obj->get()->toArray();
        }
        return  $data;  
	}

	

	public function addUpdateTimeTable($request){
		  
		    $objAttachments_Table= new Attachments_Table();	

		    $insertDate=[
                'school_id'	=> $request->school_id,
				'type'		=> $request->type,
			    'class_section_id'=>$request->class_section_id
            ];
		
            if(empty($request->id)){
            	$row= self::where($insertDate)->first();
            	if(!empty($row)){
            		return "Time table already exist with this class for ".$request->type;
            	}
			}
			
		
		   	$insertDate=[
                'school_id'	=> $request->school_id,
				'type'		=> $request->type,
				'content' =>$request->content,
                'class_section_id'=>$request->class_section_id
            ];

			

		   if($request->hasFile('image')){
			   	$file_label = $request->title;
	            foreach ($request->allFiles('images') as $file) {
	        		foreach ($file as $fkey=> $fileobj) {
		            $label     = $fileobj->getClientOriginalName();
		            $file_type = $fileobj->getClientOriginalExtension();
	        		}
	        	}
	        	$url = $objAttachments_Table->uploadAttechments($request);
	            $url = $url[0];
	            
	            $filesize  = 30;
            	$insertDate['attachment_available']=1;
            	$request->file_url=[compact("url","file_label","filesize","file_type")];
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
		  	$objAttachments_Table->insertAttechment_new($request, $id, $this->getTable());
			return $id;

	}
}
