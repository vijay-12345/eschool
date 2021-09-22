<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\App_Version;
use DB;


class AppVersionController extends \App\Http\Controllers\Controller
{
    public function addUpdateAppVersion(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'app_version'=>'required',
                    'mandatory_status'=>'required|in:low,high,medium'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objApp_Version = new App_Version();
            $data= $objApp_Version->inserUpdateData($request);
            if(!empty($data['id'])){
                if(!empty($request->id)){
                    $data['message']='Successfully Updated';
                    return $this->apiResponse($data);
                }
                else
                {
                    $data['message']='Successfully Created';
                    return $this->apiResponse($data);                    
                }
            }
            else
                return $this->apiResponse(['error'=>$data, 'message'=>$data],true);            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }


	public function nextVersion(Request $request){
		try{
            $rules = [
                'school_id' => 'required|numeric',
                'app_version'=>'required',
                'role'=>'required|in:student,teacher,admin,user,school'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                     return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objApp_Version = new App_Version();
            $data = $objApp_Version->nextVersion($request);
            if($data)
                return $this->apiResponse(['message'=>'Response successful','data'=>$data]);
            else
                return $this->apiResponse(['message'=>'Your version is updated','data'=>true]);

        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }
    public function appVersionForm(Request $request){
     
        $schools = DB::table('school')->get();
        $status=['low','medium','high'];
        $appVersion=[];
    
        if(!empty($request->id)){
          
           $appVersion = (new App_Version())->find($request->id)->toArray();
            // ->Join('attachments_table', 'attachments_table.reference_id', '=', 'time_table.id')
            // ->select('time_table.id','time_table.type','attachments_table.file_label','attachments_table.file_url','time_table.class_section_id')
            // ->where('time_table.id','=',$request->id)
            // ->get();
        }
        $request=$request->all();
        return view('BackEnd/appVersionManagement.addeditform',compact("schools",'status','appVersion','request'));  
    }

}
