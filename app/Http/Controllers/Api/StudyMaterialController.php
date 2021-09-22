<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Study_Material, App\Attachments_Table, App\Teacher_Class_Subject;
use \DB;


class StudyMaterialController extends \App\Http\Controllers\Controller
{
    public function deleteStudyMaterial(Request $request)
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
            
           $objStudy_Material= new  Study_Material();
           $id= $objStudy_Material->deleteStudyMaterial($request);
           if($id)
                return $this->apiResponse(['message'=>'Study material deleted','id'=>$request['id']]);
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addStudyMaterial(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'teacher_class_subject_id'=>'required',
                    'title'=>'required|max:255'
            ];
             if(!empty($request->role) && $request->role == 'teacher'){
                $rules['teacher_class_subject_id']='required';
            }else{
                $rules['class_section_id']='required';
                $rules['subject_ids']='required';
            }

            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
           $objStudy_Material= new  Study_Material();
           $data= $objStudy_Material->addStudyMaterial($request);
           
            return $this->apiResponse(['message'=>'Study material added',"data"=>$data]);
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addStudyMaterialwebPanel(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'teacher_class_subject_id'=>'required',
                    'title'=>'required|max:255'
            ];
            $customMessages =[];
             if(empty($request->content) && empty($request->image)){
                $rules['content']='required';
                $customMessages = [
                'content.required' => 'Url or attachment atleast one be submitted'
                ];
            }

            $validatedData = Validator::make( $request->all(),$rules,$customMessages);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
           $objStudy_Material= new  Study_Material();
           $data= $objStudy_Material->addStudyMaterialWebPanel($request);
           
            return $this->apiResponse(['message'=>'Study material added',"data"=>$data]);
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function getStudyMaterial(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school'
                ];
                
             if(!empty($request->role) && $request->role == 'teacher'){
                $rules['teacher_class_subject_id']='required';
            }else{
                $rules['class_section_id']='required';
             //   $rules['subject_ids']='required';
            }

            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
           $objStudy_Material= new  Study_Material();
           $data= $objStudy_Material->getStudyMaterial($request);
           
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
    
    public function studymaterialForm(Request $request){
        $schools = [];
        $studymaterial = [];
        $class_sections = [];
        $subjects = [];
        $attachment = [];
        if(Auth::guard('admin')->check())
        {
            $objSchool = new School();
            $schools = $objSchool->getAlldata(); 
        }
        else if(Auth::guard('school')->check())
            $schools = School::where('id',$request->user_id)->get();
        if(!empty($request->id)){ 
           $studymaterial = (new Study_Material())->find($request->id)->toArray();
           $attachment = (new Attachments_Table())->where('reference_id',$request->id)
                                                ->where('table_type','study_material')
                                                ->get()->toArray();
        }
        $user_detail = session('user_details');
        $request['teacher_id'] = $user_detail['id'];
        
        $objTeacher_Class_Subject = new Teacher_Class_Subject();
        $teacher_class_subjects = $objTeacher_Class_Subject->getClassSectionSubject($request['teacher_id'],$request['school_id']);
       
        return view('BackEnd/studymaterialManagement.addeditstudymaterial',
                    compact("teacher_class_subjects","schools","studymaterial","attachment","request"));
    }
    
    public function libraryStudymaterialForm(Request $request){
        $attachments =[];
        $attachments = DB::table('attachments_table')->where('reference_id',$request->id)
                                                    ->where('table_type','study_material')
                                                    ->get();

      // echo '<pre>'; print_r($request->all()); exit;
        return view('BackEnd/libraryStudymaterialManagement.downloadAttachment ',
                    compact("request","attachments"));
    }

    public function StudymaterialDownloadForm(Request $request){
        $attachments =[];
        $attachments = DB::table('attachments_table')->where('reference_id',$request->id)
                                                    ->where('table_type','study_material')
                                                    ->get();

      // echo '<pre>'; print_r($request->all()); exit;
        return view('BackEnd/studymaterialManagement.downloadAttachment ',
                    compact("request","attachments"));
    }

}
