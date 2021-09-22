<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachments_Table extends Model
{
    protected $table = 'attachments_table';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','school_id','file_label','file_url','reference_id','table_type','upload_data_time','filesize','file_type'];
	
	protected $hidden = [];	

	public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
    	return $this->table;
    }


	public function insertAttechment($school_id, $file_url_list, $reference_id, $table_type,$file_type_value=null, $File_Label=null, $filesize=null ){
		

		if(!is_array($file_url_list)){
			$file_url_list=[$file_url_list];
		}

		foreach($file_url_list as $key => $file_url){
			
			if(empty($file_type_value)){
				$filetype= explode(".",$file_url);
				$file_type=end($filetype);
			}else{
				$file_type=$file_type_value;
			}
			$upload_data_time=date('Y-m-d H:i:s');
			self::create(compact('school_id','file_label','file_url','reference_id','table_type','upload_data_time','filesize'))->id;			
		}
	}

	public function insertAttechment_new($request, $reference_id, $TableName){
	
		if(!empty($request->file_url)){
			self::where(['school_id'=>$request->school_id,'reference_id'=>$reference_id,'table_type'=>$TableName ])->update(['status'=>0]);
			$file_url_list= $request->file_url;
			if(!is_array($file_url_list)){
				$file_url_list=[$file_url_list];
			}
			foreach($file_url_list as $key => $file_url_obj){
				if(!empty($file_url_obj['url'])){
					$file_url   = $file_url_obj['url'];
					$school_id  = $request->school_id;
					$file_label = $file_url_obj['file_label'];
					$table_type = $TableName;
					if(empty($file_url_obj['filesize']))
						$filesize   =  30;
					else
						$filesize =  $file_url_obj['filesize'];
					if(empty($file_url_obj['file_type']))
					{
						$filetype  = explode(".",$file_url);
						$file_type =  end($filetype);
						$file_type  = $file_type;
					}
					else	
						$file_type = $file_url_obj['file_type'];

				}else{
					$filetype  = explode(".",$file_url_obj);
					$file_type =  end($filetype);
					$file_url   = $file_url_obj;
					$school_id  = $request->school_id;
					$file_label = "file_label";
					$table_type = $TableName;
					$filesize   =  30;
					$file_type  = $file_type;
				}
				
				$status   =1;
				$rowcondition =compact('school_id','file_url','reference_id','table_type');
				$rowdata=compact('school_id','file_label','file_url','reference_id','table_type','filesize','file_type','status');
				
				$row =self::where($rowcondition)->get()->toArray();
                if(empty($row)){
                     self::create($rowdata);	
                }else{
            		self::where('id',$row[0]['id'])->update($rowdata);
    		    }		
			}
		}
	}


	public function uploadAttechments($request){
		$file_url=[];
		$Role= ucwords($request->role);
        $destinationPath = public_path()."/$Role"; // upload path
        $path = ['full_path'=>'http://'.$_SERVER['SERVER_NAME']."/$Role"];            

		 foreach ($request->allFiles('images') as $file) {
	        foreach ($file as $fkey=> $fileobj) {
	            $profileImage = date('YmdHis') .$fkey. "." . $fileobj->getClientOriginalExtension();
	            $extension = $fileobj->getClientOriginalExtension();
	            
	            if($extension == 'png' || $extension == 'gif' || $extension == 'jpg' || $extension == 'jpeg')
	            {
	                $fileobj->move($destinationPath.'/Image', $profileImage);
	                $file_url[] = $path['full_path'].'/Image/'.$profileImage;

	            }
	            else if($extension == 'doc' || $extension == 'docx')
	            {
	                $fileobj->move($destinationPath.'/Doc', $profileImage);
	                $file_url[] = $path['full_path'].'/Doc/'.$profileImage;
	            }
	            else if($extension == 'pdf')
	            {
	                $fileobj->move($destinationPath.'/Pdf', $profileImage);
	                $file_url[] = $path['full_path'].'/Pdf/'.$profileImage;
	            }
	            else if($extension == 'mp4' || $extension == 'mkv' || 
	            	    $extension == 'm4p' || $extension == 'm4v' || $extension == '3gp' || 
	            	    $extension == 'mpg' ||$extension == 'mpeg' || $extension == 'mpv')
	            {
	                $fileobj->move($destinationPath.'/Video', $profileImage);
	                $file_url[] = $path['full_path'].'/Video/'.$profileImage;
	            }
	        }
     	}
     	return $file_url;
	}

	public static function removeAttechment($school_id, $reference_id, $id, $table_type){
		self::where(compact('id','school_id','reference_id','table_type'))->update(['status'=>0]);
	}

	public function deleteRecord($id){
		self::find($id)->delete();
		return $id;

	}

}