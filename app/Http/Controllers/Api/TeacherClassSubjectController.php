<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Subject_Class, App\Teacher_Class_Subject;


class TeacherClassSubjectController extends \App\Http\Controllers\Controller
{
    public function deleteTeacherClassSubject(Request $request)
    {
        try {
            $rules = [
                    'id'=>'required|numeric',
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school'
            ];
            
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                     return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            
            $objTeacher_Class_Subject = new Teacher_Class_Subject();
            // $id = $objTeacher_Class_Subject->;

            if($id)
                return $this->apiResponse(['message'=>'Teacher_Class_Subject deleted','id'=>$id]);
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);

            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addTeacherClassSubject(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'subject_class_id'=>'required|numeric'
            ];
            
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                     return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            
            $objTeacher_Class_Subject = new Teacher_Class_Subject();
            // $data = $objTeacher_Class_Subject->;

            return $this->apiResponse(['message'=>'Teacher_Class_Subject added','data'=>$data]);

            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    

}