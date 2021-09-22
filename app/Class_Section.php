<?php

namespace App;
use DB;
use App\Teacher;
use Session;
use Illuminate\Database\Eloquent\Model;

class Class_Section extends Model
{
    protected $table = 'class_section';
	protected $softDelete = true;
	protected $primaryKey = 'id';
	
    public $timestamps = false;

	public $fillable = ['id','school_id','class_name','section_name','class_teacher_id'];
	
	protected $hidden = [];	

	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
    }
    
	public function getClassSectionName($id){
        
		$data=  self::select('class_name','section_name');
        if(session('user_school_id')!='')
            $data->where('class_section.school_id',session('user_school_id') );
        $data = $data->find($id);
        if($data)
            $data = $data->toArray();
        else
            return $data;
		return implode("-",$data);
	}


    public function  getclasssectionNameWithsubject($request=""){
        $classSections= Class_Section::select(DB::raw("subject_class.*,class_name, section_name, subject_name"))->leftjoin('subject_class' ,"subject_class.class_section_id","=","class_section.id")

        ->join('subject_master','subject_master.id','=','subject_class.subject_id')
        ->where('class_section.deleted',0);
        if(session('user_school_id')!='')
            $classSections->where('class_section.school_id',session('user_school_id') );
        $classSections=$classSections->get();
        return $classSections;
    }

    public function  getclasssectionNameWithsubjectNotAssignToTeacher($request=""){
        $teacher_class_subject_ids = DB::table('teacher_class_subject')->select('subject_class_id')->where('deleted',0);
        if(session('user_school_id')!='')
            $teacher_class_subject_ids->where('school_id',session('user_school_id') );
        $teacher_class_subject_ids = $teacher_class_subject_ids->get()->pluck('subject_class_id')->toArray();
        // print_r($teacher_class_subject_ids);
        // die;

        $classSections= Class_Section::select(DB::raw("subject_class.*,class_name, section_name, subject_name"))
                        ->where('class_section.deleted',0)
                        ->where('subject_class.deleted',0)
        ->join('subject_class', function($join) use($teacher_class_subject_ids){ 
            $join->on("subject_class.class_section_id","=","class_section.id");
            $join->whereNotIn('subject_class.id',$teacher_class_subject_ids);
            
        })
        
        ->join('subject_master','subject_master.id','=','subject_class.subject_id');
        
        
        if(session('user_school_id')!='')
            $classSections->where('class_section.school_id',session('user_school_id') );
        $classSections=$classSections->get();
        

        return $classSections;
    }

	public function getClass_Section($request){
        if(!empty($request->role) && $request->role == 'student')
        {
            $obj = self::select('class_section.id',
                    DB::raw("CONCAT(class_section.class_name,'-',class_section.section_name) as class_section_name"))
            ->leftJoin('class_section','class_section.id','=','student.class_section_id')
            ->where('student.id',$request->user_id)
            ->where('student.school_id',$request->school_id);               
        }
        else if(!empty($request->role) &&  $request->role == 'teacher')
        {
            $obj=Teacher::select('class_section.id',DB::raw("CONCAT(class_section.class_name,'-',class_section.section_name) as class_section_name"))
                ->Join('teacher_class_subject','teacher_class_subject.teacher_id','=','teacher.id')
                ->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
                ->Join('class_section','class_section.id','=','subject_class.class_section_id')
                ->where('teacher.id',$request->user_id)
                ->where('teacher.school_id',$request->school_id);
        }
        else{
            $obj = self::where('deleted',0);
            if(session('user_school_id')!='')
                $obj->where('school_id','=',session('user_school_id'));
            return $obj->get();
        }
        $data = $obj->groupBy('class_section.id');
        if(!empty($request->page_limit)){
            $data = $obj->paginate($request->page_limit)->toArray();
        }else{
            $data = $obj->get()->toArray();
        }
        return $data;
	}	


    public function inserUpdateData($request){
        
            if(!empty($request->id)){
                $id= $request->id;
                $insertData = self::find($id);
            }

            foreach ($this->fillable as $key => $value) {
                if(!empty($request[$value]))
                    $insertData[$value]=$request[$value];
            }
            //echo '<pre>'; print_r($insertData); exit;
           if(!empty($insertData)){
                if(!empty($insertData["id"])){
                    self::where(['id'=>$insertData["id"]])->update($insertData->toArray());
                }else{
                    // $row = self::where($insertData)->get()->toArray();
                    $row = self::where('school_id',$insertData['school_id'])->where('class_name',$insertData['class_name'])
                                ->where('section_name',$insertData['section_name'])->get()->toArray();
                    if(empty($row))
                        $id = self::create($insertData )->id;
                    else
                    {
                        self::where(['id'=>$row[0]['id']])->update(['deleted'=>0]);
                        $id = $row[0]['id'];
                    }

                }
                (new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
                return compact("id");
            }
    }

}
