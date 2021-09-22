<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Session_Table, App\Student, App\Teacher, App\Assignment, App\Attachments_Table;
use App\Assignment_Submittted, App\Teacher_Class_Subject, App\School, App\Assignment_Test;

use \DB;
use Carbon\Carbon;


class AssignmentController extends \App\Http\Controllers\Controller
{
    public function deleteAssignment(Request $request)
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

            $objAssignment= new Assignment_Test();
            $id = $objAssignment->deleteAssignment($request);
            
            if($id==1){
                return $this->apiResponse(['message'=>'Assignment deleted','id'=>$request['id']]);
            }elseif($id==0){
                return $this->apiResponse(['error'=>'Can not remove, Assignment submitted by some students'],true);
            }else{
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
            }

        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function deleteAssignmentSubmittted(Request $request)
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
        
            $objAssignment= new Assignment_Submittted();
            // $id = $objAssignment->;
            
            if($id)
                return $this->apiResponse(['message'=>'Submittted Assignment deleted','id'=>$id]);
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);

        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addAssignment(Request $request)
    {
        try {

            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'teacher_class_subject_id' => 'required',
                    'title'=>'required|max:255',
                    'assignment_description'=>'required',
                    'due_date'=>'required|date'
            ];
            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }

            $objAssignment= new Assignment();
            $id = $objAssignment->addUpdateAssignmentbyTeacher($request);
            
            return $this->apiResponse(['message'=>'Assignment added','id'=>$id]);
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addAssignmentWebPanel(Request $request)
    {
        try {

            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'teacher_class_subject_id' => 'required',
                    'title'=>'required|max:255',
                    'assignment_description'=>'required',
                    'due_date'=>'required|date',
                    'image.0' => 'image|required'

            ];
            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            

            $objAssignmentTest= new Assignment_Test();
            $id = $objAssignmentTest->addUpdateAssignmentbyTeacherWebPanel($request);
            
            return $this->apiResponse(['message'=>'Assignment added','id'=>$id]);
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function submitAssignmentByStudent(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'assignment_id' => 'required|numeric',
                    'details'=>'required'
            ];
            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }

            $objAssignment_Submittted= new Assignment_Submittted();
            $id=$objAssignment_Submittted->submitAssignment($request);
            if($id)
                return $this->apiResponse(['message'=>'Assignment added','id'=>$id]);
            else
                return $this->apiResponse(['message'=>'Assignment DueDate Has Expired']);
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function getAssignmentList(Request $request)
    {
        try {
            
            $rules=[
                    'school_id' => 'required|numeric',
                    'role' => 'required|in:student,teacher,user'
                ];
            if(!empty($request->role) && $request->role=='teacher'){
                $rules['teacher_class_subject_id']='required';
            }else{
                $rules['class_section_id']='required';
                $rules['subject_ids']='required';
            }
            $validatedData = Validator::make( $request->all(),$rules);

            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            
            $objAssignment= new Assignment();
            $data= $objAssignment->getAssignment($request);

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


 public function getMyCreatedAssingment(Request $request){
    try {
            $rules=[
                'school_id'                 => 'required|numeric',
                'role' => 'required|in:teacher,student,user'
            ];
            if(!empty($request->role) && $request->role=='teacher'){
                $rules['teacher_class_subject_id']='required';
            }else{
                $rules['class_section_id']='required';
                $rules['subject_ids']='required';
            }
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            $objAssignment= new Assignment_Test();
            $data= $objAssignment->getMyCreatedAssingment($request);
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
 public function getAssingmentSubmittedStudentList(Request $request){
    try {
            $rules=[
                'school_id'                 => 'required|numeric',
                "teacher_class_subject_id"  => 'required|numeric',
                'role' => 'required|in:teacher',
                'assignment_id'=>'required|numeric'
            ];
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            $objAssignment= new Assignment();
            $data= $objAssignment->getAssingmentSubmittedStudentList($request);
            if($data){
                // if(!empty($request->page_limit)){
                //     $data['message']='Response Successful';
                //     return $this->apiResponse($data);
                // }
                return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
            }
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);

        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }

 }   
 public function getsubmittedAssigmentStudentDetail(Request $request){
    try{
            $rules=[
                'school_id'                 => 'required|numeric',
                "teacher_class_subject_id"  => 'required|numeric',
                'role'                      => 'required|in:teacher',
                'assignment_submittted_id'  => 'required|numeric'
            ];
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            $objAssignment= new Assignment_Test();
            $data= $objAssignment->getsubmittedAssigmentStudentDetail($request);
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




 public function getSubmittedAssignmentList(Request $request)
    {
        try {
            $rules=[
                    'school_id' => 'required|numeric',
                    'role' => 'required|in:student,teacher,user',
                    "assignment_id"=>'required|numeric'
                ];
          
            $validatedData = Validator::make( $request->all(),$rules);

            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            
            $objAssignment= new Assignment_Submittted();
            $data= $objAssignment->getSubmittedAssignment($request);

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

    public function assignmentForm(Request $request){
        $schools = [];
        $assignment = [];
        $class_sections = [];
        $subjects = [];
        $attachment = [];
        if(Auth::guard('admin')->check())
        {
            $objSchool = new School();
            $schools = $objSchool->getAlldata(); 

        }
        else if(Auth::guard('school')->check())
        {
            $schools = School::where('id',$request->user_id)->get();
        }

        if(!empty($request->id)){ 
           $assignment = (new Assignment())->find($request->id)->toArray();
           $attachment = (new Attachments_Table())->where('reference_id',$request->id)
                                                ->where('table_type','assignment')
                                                ->get()->toArray();
        }
        $user_detail = session('user_details');

        $request['teacher_id'] = $user_detail['id'];
        // $request=$request->all();
        
        $objTeacher_Class_Subject = new Teacher_Class_Subject();
        $teacher_class_subjects = $objTeacher_Class_Subject->getClassSectionSubject($request['teacher_id'],$request['school_id']);
       
        return view('BackEnd/assignmentManagement.addeditassignment',
                    compact("teacher_class_subjects","schools","assignment","attachment","request"));
    }
    
    public function libraryAssignmentForm(Request $request){
        $attachments =[];
        $attachments = DB::table('attachments_table')->where('reference_id',$request->id)
                                                    ->where('table_type','assignment')
                                                    ->get();
        return view('BackEnd/libraryAssignmentManagement.downloadAttachment',compact("request","attachments"));
    }

    public function AssignmentDownloadForm(Request $request){
        $attachments =[];
        $attachments = DB::table('attachments_table')->where('reference_id',$request->id)
                                                    ->where('table_type','assignment')
                                                    ->get();

      // echo '<pre>'; print_r($request->all()); exit;
        return view('BackEnd/assignmentManagement.downloadAttachment ',
                    compact("request","attachments"));
    }
    

}
