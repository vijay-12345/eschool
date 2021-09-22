<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\ReadMapping;
use App\Notifaction;



class ClassController extends \App\Http\Controllers\Controller
{
		public function setRead(Request $request){
    		try{
                    $rules = [
                        'table_type' => 'required',
                        'refrance_id'=>'required',
                        'role'=>'required|in:student,teacher,admin,user,school'
                    ];
                    if($request->role != 'super_admin')
                    {
                        $validatedData = Validator::make( $request->all(),$rules);
                        if ($validatedData->fails()){          
                             return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                        }
                    }
        	            
                    $objReadMapping = new ReadMapping();
        			$data= $objReadMapping->setAsRead($request);
        			if($data){
                        return $this->apiResponse(['message'=>'successful','data'=>$data]);
                    }
                   
            } catch(\Exception $e) {
                return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
            }
		}

        public function checkNotification(Request $request){
            try{
                    $objNotifaction = new Notifaction();
                    $data= $objNotifaction->checkNotification();
                    if($data){
                        return $this->apiResponse(['message'=>'successful','data'=> $data]);
                    }
                   
            } catch(\Exception $e) {
                return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
            }
            
        }

}
