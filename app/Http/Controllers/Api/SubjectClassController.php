<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Subject_Class, App\School,App\Class_Section;
use DB;
use Session;


class SubjectClassController extends \App\Http\Controllers\Controller
{
    public function deleteSubjectClass(Request $request)
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
            
            $objSubject_Class = new Subject_Class();
            // $id = $objSubject_Class->;
            
            if($id)
                return $this->apiResponse(['message'=>'Subject class deleted','id'=>$id]);
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);     
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addUpdateSubjectClass(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'class_section_id' => 'required',
                    'subject_id' => 'required'

            ];
            
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                     return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objSubject_Class = new Subject_Class();
            $id = $objSubject_Class->inserUpdateData($request);
            
            if(empty($request->id))
                return $this->apiResponse(['message'=>'Subject class added','id'=>$id]);
            else
                return $this->apiResponse(['message'=>'Subject class updated','id'=>$id]);

            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function subjectClassForm(Request $request){
        $subjects=DB::table('subject_master')->get()->where('deleted',0);
        $classSections=(new Class_Section())->getClass_Section($request);
        $subject_class=array();
        $schools = [];
        if(Auth::guard('admin')->check())
        {
            $objSchool = new School();
            $schools = $objSchool->getAlldata();            
        }
        if(!empty($request->id)){
          
           $subject_class = (new Subject_Class())
                            ->find($request->id)
                            ->toArray();
        }
      
        $request=$request->all();
        return view('BackEnd/subjectManagement.addeditformsubjectMapping',compact('schools','subject_class','subjects','classSections','request'));  
    }
    

}