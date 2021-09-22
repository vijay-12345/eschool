<?php

namespace App;

use App\Class_Section;
use Illuminate\Database\Eloquent\Model;

class Subject_Class extends Model
{
    protected $table = 'subject_class';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','class_section_id','school_id','subject_id'];
	
	protected $hidden = [];	

	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
    }

    public function getSubject($request){
        $var = new Subject_Master();
        $subject_master_fillable = $var->fillable;
        $obj = Subject_Class::select('subject_master.id','subject_master.subject_name')
                    ->Join('subject_master','subject_master.id','=','subject_class.subject_id')
                    ->where('subject_class.class_section_id',$request->class_section_id)
                    ->where('subject_class.deleted',0);

        if(!empty($request->search_string)){
                 $obj->where(function($query) use ($request ,$subject_master_fillable){
                    foreach ($subject_master_fillable as $value) 
                    {
                            $query->orWhere('subject_master.'.$value, 'LIKE', "%".$request->search_string."%");
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
        return $data;
     }


   

    public function class_section()
    {
        return $this->hasOne(Class_Section::class, 'id', 'class_section_id');
    }

    public function subject_master()
    {
        return $this->hasMany(Subject_Master::class,'id','subject_id');
    }

    // public function address()
    // {
    //     return $this->morphOne(Address::class,'addressable')->with(['state','country']);
    // }
    //  public function role()
    // {
    //     return $this->belongsTo(Role::class);
    // }

    public function inserUpdateData($request){
        
        if(!empty($request->id)){
            $id= $request->id;
            $insertData = self::find($id);
        }

        foreach ($this->fillable as $key => $value) {
            if(!empty($request[$value]))
                $insertData[$value]=$request[$value];
        }
        
       if(!empty($insertData)){
            if(!empty($insertData["id"])){

                $row_update = self::where('class_section_id',$insertData["class_section_id"])
                            ->where('school_id',$insertData["school_id"])
                            ->where('subject_id',$insertData["subject_id"])
                            ->get()->toArray();
                if(empty($row_update))
                    self::where(['id'=>$insertData["id"]])->update($insertData->toArray());
            }else{
                $row = self::where($insertData)->get()->toArray();
                if(empty($row))
                    $id = self::create($insertData )->id;
                else
                {
                    $id = $row[0]['id'];
                }

            }
            (new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
            return compact("id");
        }
    }

    public function getAllClassSectionwithSubjects($request){
        $condition =[];
        if(!empty($request->school_id)){
            $condition['school_id']=$request->school_id;
        }if(!empty($request->class_section_id)){
            $condition['class_section_id']=$request->class_section_id;
        }
        $obj = self::with('class_section','subject_master')->where($condition);
               $obj->where('subject_class.deleted',0);
        if(!empty($request->page_order)){
            $obj->orderby($request->page_order[0],$request->page_order[1]); 
        }
        if(!empty($request->page_limit)){
            $data = $obj->paginate($request->page_limit)->toArray();
        }else{
            $data = $obj->get()->toArray();
        }
        return $data;
    }

    public function getAllClassSectionwithSubjects_SearchField($request){
        $condition =[];
        $objClass_Section = new Class_Section();
        $fillable_class_section = $objClass_Section->fillable;
        $objSubject_Master = new Subject_Master();
        $fillable_subject_master = $objSubject_Master->fillable;
        //class_section_id must be required in request
        $obj = self::select('class_section.*','subject_master.*')
                    ->join('subject_master','subject_master.id','=','subject_class.subject_id')
                    ->join('class_section','class_section.id','=','subject_class.class_section_id')
                    ->where('subject_class.class_section_id',$request->class_section_id)
                    ->where('subject_class.school_id',$request->school_id);

        if(!empty($request->search_string)){
                 $obj->where(function($query) use ($request ,$fillable_class_section,$fillable_subject_master){
                    foreach ($fillable_class_section as $value) {
                    $query->orWhere('class_section.'.$value, 'LIKE', "%".$request->search_string."%");
                    }
                    foreach ($fillable_subject_master as $value) {
                    $query->orWhere('subject_master.'.$value, 'LIKE', "%".$request->search_string."%");
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
        return $data;
    }






}
