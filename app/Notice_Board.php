<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notice_Board extends Model
{
    protected $table = 'notice_board';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','title','message','type','date'];
	
	protected $hidden = [];	

    public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
    }

	// public function getNotice($request){
	// 	$obj = self::select($this->fillable);

	// 	if(!empty($request->search_string)){
	// 		foreach ($this->fillable as $value) {
	// 			$obj->orWhere($value, 'LIKE', "%".$request->search_string."%");
	// 		}
	// 	}

	// 	if(in_array($request->role,['student'])){
	// 		$obj->where('type',$request->role);
	// 	}

	// 	if($request->role!='admin'){
	// 		$obj->where('school_id',$request->school_id);
	// 	}

	// 	if(!empty($request->page_order)){
	// 		$obj->orderby($request->page_order[0],$request->page_order[1]);	
	// 	}
		
	// 	if(!empty($request->page_limit)){
	//         	$data = $obj->paginate($request->page_limit)->toArray();
 //        }else{
	//         $data = $obj->get()->toArray();
 //        }

 //        return $data;
	// }

	public function getNotice($request){
            $obj = self::select($this->fillable);
            $fillable=$this->fillable;


            if($request->role!='admin'){
                    $obj->where('school_id',$request->school_id);
            }
            if($request->role == 'student')
            {
                    $obj->where('type','!=','teacher');
            }
            else if($request->role == 'teacher')
            {
                    $obj->where('type','!=','student');
            }

            if(!empty($request->search_string)){
                     $obj->where(function($query) use ($request ,$fillable){
                            foreach ($fillable as $value) {
                            $query->orWhere($value, 'LIKE', "%".$request->search_string."%");
                            }
                    });
            }

            if(!empty($request->page_order)){
                    $obj->orderby($request->page_order[0],$request->page_order[1]);
            }
            if(!empty($request->take))
            {
            	$obj->take($request->take);
            }

            if(!empty($request->page_limit)){
                    $data = $obj->paginate($request->page_limit)->toArray();
            }else{
                    $data = $obj->get()->toArray();
            }
            return $data;
	}
        
		public function addUpdateNotice($request){
		  
	      if(!empty($request->id)){
            $id = $request->id;
            $insertData = self::find($id);
	        }
            foreach ($this->fillable as $key => $value) {
	            if(!empty($request[$value]))
	                $insertData[$value]=$request[$value];
	        }
	        
	        if(!empty($request->id)){

	            self::where(['id'=>$id])->update($insertData->toArray());
	            (new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'update');
	         }
	         else{
         		$id = self::create($insertData)->id;
     			(new Notifaction())->setNewNotificatin($request,$this->getTable(),$id,'add');
	         }
	        return compact("id"); 
		}

}
