<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use User;
use Illuminate\Validation\Rule;
use App\Session_Table, App\Class_Section, App\Subject_Master, App\Teacher_Class_Subject;


class SessionController extends \App\Http\Controllers\Controller
{
    public function deleteSession(Request $request)
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
            $objSession= new Session_Table();
            $id = $objSession->deleteSession($request);            
            if($id ==1){
                return $this->apiResponse(['message'=>'Session deleted','id'=>$request['id']]);
            }elseif($id==0){
                return $this->apiResponse(['error'=>'Can not remove, session attended by some students'],true);  
            }
            else{
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
            }

            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addSession(Request $request)
    {
        try {
            $rules = [
                'school_id' => 'required|numeric',
                'topic' => 'required',
                'date'=>'required|date',
                'teacher_class_subject_id'=>'required|numeric',
                'start_time'=>'required|date',
                'end_time'=>'required|date'
            ];
            if(!empty($request->meeting_id))
            {
                $rules['meeting_id'] = 'required|unique:session_table';
            }
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            $objSession_Table= new  Session_Table();
            $data= $objSession_Table->addUpdateSession($request);

            return $this->apiResponse(['message'=>'Session added','data'=>$data]);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }


    public function updateSession(Request $request)
    {
        try {
            $rules = [
                    'school_id'=>'required|numeric',
                    'session_id'=>'required|numeric'
            ];
            if(!empty($request->meeting_id))
            {
                $rules['meeting_id'] = [
                        'required',
                        Rule::unique('session_table')->ignore($request->session_id)
                    ];
            }
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            $data = Session_Table::find($request->session_id);
            if(!$data)
                return $this->apiResponse(['message'=>'Data Not Found','error'=>'Data Not Found'],true);
            
            $objSession_Table= new  Session_Table();
            $data= $objSession_Table->addUpdateSession($request);

            return $this->apiResponse(['message'=>'Session Updated','data'=>$data]);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }



    public function getUpcomingClassSessions(Request $request)
    {
        try {

            $rules =[
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                ];
                
            if(!empty($request->role) && $request->role == 'teacher'){
                $rules['teacher_class_subject_id']='required';
            }elseif(!empty($request->role) && $request->role == 'student'){
               $rules['class_section_id']='required';
            //  $rules['subject_ids']='required';
            }

            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            
            $objSession_Table= new  Session_Table();
            $data= $objSession_Table->getTodaysSession($request);
           
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

    public function sessionForm(Request $request){

        
        $schools = [];
        $session = [];
        $class_sections = [];
        $subjects = [];
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
          
           $session = (new Session_Table())->find($request->id)->toArray();
        }
        $user_detail = session('user_details');

        $request['teacher_id'] = $user_detail['id'];
        // $request=$request->all();
        
        $objTeacher_Class_Subject = new Teacher_Class_Subject();
        $teacher_class_subjects = $objTeacher_Class_Subject->getClassSectionSubject($request['teacher_id'],$request['school_id']);
        // print_r($teacher_class_subjects);
        // die;

        // $class_sections = Class_Section::select('id','class_name','section_name')->get();
        // $subjects = Subject_Master::select('id','subject_name')->get();

       
        return view('BackEnd/sessionManagement.addeditsession',compact("teacher_class_subjects","schools","session","request"));
    }
    


}
