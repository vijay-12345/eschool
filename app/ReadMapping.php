<?php

namespace App;
use DB;
use App\User;
use Illuminate\Database\Eloquent\Model;

class ReadMapping extends Model
{
    protected $table = 'read_mapping';
	
	protected $primaryKey = 'id';

	public $fillable = ['id','user_id','table_type','refrance_id','role'];
	public $timestamps = false;
	protected $hidden = [];	

	public function setAsRead($request){
		if(!empty($request->refrance_id)){
			if(!is_array($request->refrance_id)){
				$request->refrance_id = [$request->refrance_id];
			}
			foreach($request->refrance_id as $refrance_id){
				$insertData=[];
				foreach ($this->fillable as $key => $value) {
		            if(!empty($request[$value]))
		                $insertData[$value]=$request[$value];
		        }
		        $insertData['user_id']=$request->user_id;
		        $insertData['refrance_id']=$refrance_id;
		        $row=self::where($insertData)->first();
		        if(empty($row))
			        self::create($insertData );
			}
		}

		return true;
	}

	public function resetOnUpdate($request,$table_type,$refrance_id){
		$user_id=User::getId($request);
		self::where(compact('table_type','refrance_id'))->where('user_id',"<>",$user_id)->delete();
	}

	public function getReadedIds($request,$table_type){
		$role=$request->role;
		$user_id=$request->user_id;
		$data= self::select(DB::raw("refrance_id as id"))->where(compact('table_type','role','user_id'))->pluck('id')->toArray();	
		return  $data;
	}

	public function getUnreadIds($request,$table_type,$allIds){
		$readIds= $this->getReadedIds($request,$table_type);
		$UnreadIds=[];
		foreach($allIds as $id)
			if(!in_array($id,$readIds)){
				$UnreadIds[]=$id;
			}
		return  $UnreadIds;
	}


}
