<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session_Attendance extends Model
{
    protected $table = 'session_attendance';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','user_id','session_table_id','status'];
	
	protected $hidden = [];	
	
 	public function addUpdateSession_Attendance($request){
			
		if(!empty($request->id)){
        $id = $request->id;
        $insertData = self::find($id);
        }

        foreach ($this->fillable as $key => $value) {
            if(isset($request[$value]))
                $insertData[$value]=$request[$value];
        }
        if(!empty($request->id)){

            self::where(['id'=>$id])->update($insertData->toArray());
         }
         else{
         	$row = self::where('user_id',$request['user_id'])
                    ->where('session_table_id',$request['session_table_id'])
                    ->first();
    		if(empty($row))		
     			$id = self::create($insertData)->id;
     		else
     			$id = $row->id;
         }
        return compact("id");
    }

    public function getSession_Attendance($request){
        $data = self::where('user_id',$request['user_id'])
                    ->where('session_table_id',$request['session_table_id'])
                    ->first();
        return $data;           
    }
}
