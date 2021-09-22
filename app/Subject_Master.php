<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject_Master extends Model
{
    protected $table = 'subject_master';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','subject_name'];
	
	protected $hidden = [];	

	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
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
            
           if(!empty($insertData)){
                if(!empty($insertData["id"])){
                    $row_update = self::where('subject_name',$insertData["subject_name"])->get()->toArray();
                    if(empty($row_update))        
                        self::where(['id'=>$insertData["id"]])->update($insertData->toArray());
                }else{
                    $row = self::where($insertData)->get()->toArray();
                    if(empty($row))
                        $id = self::create($insertData)->id;
                    else
                    {
                        $id = $row[0]['id'];
                    }

                }
                (new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
                return compact("id");
            }
    }

}
