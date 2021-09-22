<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use User;
use Illuminate\Validation\Rule;
use App\School;


class SchoolController extends \App\Http\Controllers\Controller
{
    public function getSchoolsByParentSchool(Request $request)
    {      
        try {

            $rules =[
                    'id' => 'required|numeric',
                ];

            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            
            $objSchool= new  School();
            $data= $objSchool->getSchoolsByParent($request);
            if($data){
                if(!empty($request->page_limit)){
                    $data['message']='Response Successful';
                    return $this->apiResponse($data);
                }
                return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
            }
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
  
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }
    


}
