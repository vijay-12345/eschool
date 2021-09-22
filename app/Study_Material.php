<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use  App\Attachments_Table;
use  App\Teacher_Class_Subject;
use  App\Class_Section;
use App\User;
use DB;

class Study_Material extends Model
{
    protected $table = 'study_material';
    
    protected $primaryKey = 'id';
    
    public $timestamps = false;

    public $fillable = ['id','school_id','teacher_class_subject_id','title','content','attachment_available','date'];
    
    protected $hidden = []; 

    public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
        return $this->table;
    }
    
    public function addStudyMaterial($request){
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
                $datetime = date('Y-m-d H:i:s');
                $insertDate = [ 'school_id'=>$request->school_id,
                        'teacher_class_subject_id'=>$teacher_class_subject_id,
                        'title' => $request->title,
                        'content'=>$request->content,
                        'date'=> $datetime
                            ];
                if(!empty($request->file_url)){
                    $insertDate["attachment_available"]=1;
                 }
                 if(!empty($request->id)){
                    $id =$request->id;
                    self::where(['id'=>$id])->update($insertDate ) ;
                    (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'update'); 
                 }
                 else
                 {
                    $id = self::create($insertDate )->id;
                    (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'add');
                 }
                 
                 (new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
            }
            return ['id'=>$id];
        }
    }

    public function addStudyMaterialwebPanel($request){
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
                $datetime = date('Y-m-d H:i:s');
                $insertDate = [ 'school_id'=>$request->school_id,
                        'teacher_class_subject_id'=>$teacher_class_subject_id,
                        'title' => $request->title,
                        'content'=>$request->content,
                        'date'=> $datetime
                            ];
                if(!empty($request->image)){
                    $insertDate["attachment_available"]=1;
                 }
                 if(!empty($request->id)){
                    $id =$request->id;
                    self::where(['id'=>$id])->update($insertDate ) ; 
                 }
                 else
                 {
                    $id = self::create($insertDate )->id;
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
            }
            return ['id'=>$id];
        }
    }


    public function getStudyMaterial($request){

        $objUser=new User();
        $objClass_Section=new Class_Section();

        if(empty($request->teacher_class_subject_id)){
            $objTeacher_Class_Subject=new Teacher_Class_Subject();
            $request->teacher_class_subject_id = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_ids, $request->school_id);
        }
        $select='study_material.id as id,title,date,attachment_available,teacher_class_subject.teacher_id,content ,class_section_id';

        $obj = self::select(DB::raw($select))->where([ 'study_material.school_id'=>$request->school_id]);
        
        if(!empty($request->teacher_class_subject_id)){
            if(!is_array($request->teacher_class_subject_id))
                $request->teacher_class_subject_id = [$request->teacher_class_subject_id];
            $obj->whereIn('teacher_class_subject_id',$request->teacher_class_subject_id);
        }
        if(empty($request->teacher_class_subject_id)){
            return  NULL;
        }

        $obj->Join('teacher_class_subject','teacher_class_subject.id','=','study_material.teacher_class_subject_id')
        ->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
        ->Join('subject_master','subject_master.id','=','subject_class.subject_id')
        ->Join('class_section','class_section.id','=','subject_class.class_section_id');
        if($request->role=='teacher')
            $obj->where('teacher_class_subject.teacher_id',$request->user_id);
       
         $tabletype=$this->table;
         $obj->with('file_url');   
         $obj->groupby('study_material.id');

        if(!empty($request->page_order)){
            $obj->orderby($request->page_order[0],$request->page_order[1]); 
        }
        if(!empty($request->page_limit)){
            $data = $obj->paginate($request->page_limit)->toArray();
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]["teacher"]= $objUser->getUserDetails($value["teacher_id"] ,'teacher');
                $data['data'][$key]["class_section_id"]= $objClass_Section->getClassSectionName($value["class_section_id"]);
            }
        }else{
            $data = $obj->get()->toArray();
            foreach ($data as $key => $value) {
               $data[$key]["teacher"]= $objUser->getUserDetails($value["teacher_id"] ,'teacher');
                $data[$key]["class_section_id"]= $objClass_Section->getClassSectionName($value["class_section_id"]);
            }
        }
        return  $data;          
    }
    
    public function getLibraryStudyMaterialByFilter($request){
        $data=array();
         $study_metareials= Study_Material::select('study_material.id','study_material.title','study_material.content','subject_master.subject_name','study_material.date','study_material.attachment_available')
                 ->where('study_material.school_id',session('user_school_id'))
                 ->where('class_section.id',$request->class_section_id)
                 ->where('subject_master.id',$request->subject_id)
                 ->where('subject_class.deleted',0)
                 ->where('class_section.deleted',0)
                 ->where('teacher_class_subject.deleted',0)
                 ->join('teacher_class_subject', 'teacher_class_subject.id','study_material.teacher_class_subject_id')
                 ->join('subject_class', 'teacher_class_subject.subject_class_id','subject_class.id')
                 ->join('class_section', 'subject_class.class_section_id','class_section.id')
                 ->join('subject_master', 'subject_class.subject_id','subject_master.id')
                 ->get()->toArray();
        foreach($study_metareials as $study_metareial){
             if($study_metareial['attachment_available'] ==1){
                 
                 $study_metareial['total_attachments']=$this->getTotalAttachments($study_metareial['id']);
             }else{
                 $study_metareial['total_attachments']=0;
             }
             $data[]=$study_metareial;
             
         }
        return  $data;          
    }
    
    public function getTotalAttachments($referenceId){
         $total_attachment= Attachments_Table::select(DB::raw("COUNT(attachments_table.id) as total_attachments"))
                 ->where('attachments_table.reference_id',$referenceId)
                 ->where('attachments_table.table_type','study_material')
                 ->first()->toArray();
        return  $total_attachment['total_attachments'];          
    }
    public function deleteStudyMaterial($request){
        $study_material = Study_Material::find($request['id']);
        attachments_table::where('reference_id', $request['id'])->where('table_type', 'study_material')->delete();
        return $study_material->delete();
    }
    
}
