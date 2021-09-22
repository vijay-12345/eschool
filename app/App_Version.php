<?php

namespace App;
use DB;

use Illuminate\Database\Eloquent\Model;

class App_Version extends Model
{
    protected $table = 'app_version';
	protected $primaryKey = 'id';
	
    public $timestamps = false;

	public $fillable = ['id','school_id','app_version','created_date','mandatory_status'];
	
	protected $hidden = [];	

    public function getTable(){
    	return $this->table;
    }

    public function inserUpdateData($request)
    {      
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
         }
         else{
            $id = self::create($insertData)->id;
         }
        return compact("id"); 
    }

    public function nextVersion($request)
    {
        $data = self::where('school_id',$request->school_id)
                    ->where('app_version','>',$request->app_version)
                    ->orderBy('app_version', 'desc')
                    ->first();

        
        if(empty($data)){
            $data['forcefully_update']=0;
            $data["massage"]="You have latest version";
         }
         elseif($data->mandatory_status!='high'){
            $data->forcefully_update=1;
            $data->massage="new version is available we recommend to update your app";
         }
         else{
            $data->forcefully_update=2;
            $data->massage="Please update your app. Its outdated";
         }

        return $data;
    }

}
