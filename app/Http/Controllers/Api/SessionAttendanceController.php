<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use DB;
use App\Session_Attendance;

class SessionAttendanceController extends \App\Http\Controllers\Controller
{
    // public function deleteSessionAttendance(Request $request)
    // {
    //     try {
    //         $rules = [
    //                 'id'=>'required|numeric',
    //                 'school_id' => 'required|numeric',
    //                 'role'=>'required|in:student,teacher,admin,user,school'
    //             ];
              
    //          if($request->role != 'super_admin')
    //          {
    //             $validatedData = Validator::make( $request->all(),$rules);
    //             if ($validatedData->fails()){          
    //                 return $this->apiResponse(['error' => $validatedData->errors() ], true);
    //             }
    //          } 
            
    //         $objTime_Table= new  Time_Table();
    //         // $id= $objTime_Table->;

    //         if($id)
    //             return $this->apiResponse(['message'=>'Time Table deleted','id'=>$id]);
    //         else
    //             return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
            
    //     } catch(\Exception $e) {
    //         return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
    //     }
    // }

    public function addUpdateSessionAttendance(Request $request)
    {
        try {
            $rules = [
                    'user_id' => 'required|numeric',
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'session_table_id'=>'required|numeric'
                ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                    return $this->apiResponse(['error' => $validatedData->errors() ], true);
                }
            } 
            $objSession_Attendance= new  Session_Attendance();
            $id= $objSession_Attendance->addUpdateSession_Attendance($request);
            if(!empty($request->id))
                return $this->apiResponse(['message'=>'Session Attendance updated','id'=>$id]);
            else
                return $this->apiResponse(['message'=>'Session Attendance added','id'=>$id]);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function getSessionAttendance(Request $request)
    {
        try {
            $rules = [
                    'user_id' => 'required|numeric',
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'session_table_id' => 'required|numeric'
                ];
            if($request->role != 'super_admin'){    
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                    return $this->apiResponse(['error' => $validatedData->errors() ], true);
                }
            }
            $objSession_Attendance= new  Session_Attendance();
            $data= $objSession_Attendance->getSession_Attendance($request);
           
            if($data){
                return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
            }
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }
}
