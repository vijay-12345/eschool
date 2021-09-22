<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Session_Table, App\Student, App\Teacher, App\Attachments_Table;
use Carbon\Carbon;


class ImageController extends \App\Http\Controllers\Controller
{
    public function uploadImage(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'images'=>'required'
            ];
            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            
            $objAttachments_Table =new Attachments_Table();
            $file_url= $objAttachments_Table->uploadAttechments($request);
             return $this->apiResponse(['message'=>'Response successful','data'=>$file_url]);

        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }
    

}