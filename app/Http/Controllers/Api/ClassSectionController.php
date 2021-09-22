<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Student, App\Class_Section ,App\Subject_Class, App\School;
use \DB;


class ClassSectionController extends \App\Http\Controllers\Controller
{
    public function deleteClassSection(Request $request)
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
            
            $objClass_Section = new Class_Section();
            // $id= $objClass_Section->;

            if($id)
                return $this->apiResponse(['message'=>'Class Section deleted','id'=>$id]);
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function updateClassSection(Request $request)
    {
        try {
            $rules = [
                    'id' => 'required|numeric',
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'class_name'=>'required',
                    'section_name'=>'required'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objClass_Section = new Class_Section();
            $data= $objClass_Section->inserUpdateData($request);
            if(!empty($data['id'])){
                $data['message']='Successfully Updated';
                return $this->apiResponse($data);
            }
            else
                return $this->apiResponse(['error'=>$data, 'message'=>$data],true);            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addClassSection(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'class_name'=>'required',
                    'section_name'=>'required'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objClass_Section = new Class_Section();
            $data= $objClass_Section->inserUpdateData($request);
            if(!empty($data['id'])){
                $data['message']='Successfully Created';
                return $this->apiResponse($data);
            }
            else
                return $this->apiResponse(['error'=>$data, 'message'=>$data],true);            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }    

    public function getClassSection(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school'
            ];
            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            $Subject_Class = new Subject_Class();
            if(!empty($request->search_string))
            {
                $data = $Subject_Class->getAllClassSectionwithSubjects_SearchField($request);
            }
            else
                $data= $Subject_Class->getAllClassSectionwithSubjects($request);
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
    public function classSectinForm(Request $request){
        $teachers=DB::table('teacher')
                        ->where('school_id','=',$request->school_id)->where('deleted',0)
                        ->select('id','name')->get();
        $class_section=[];
        $schools = [];
        if(Auth::guard('admin')->check())
        {
            $objSchool = new School();
            $schools = $objSchool->getAlldata();            
        }
        if(!empty($request->id)){
           $class_section = DB::table('class_section')->where('id','=',$request->id)->first();
        }
        $request=$request->all();
        return view('BackEnd/classManagement.addeditform',compact("schools","teachers",'class_section','request'));  
    }
    

}