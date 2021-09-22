<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\App_Version, App\Quiz_Table, App\Quiz_Detail ,App\Quiz_Result, App\School;
use App\Class_Section, App\Subject_Class, App\Subject_Master;
use DB;


class QuizController extends \App\Http\Controllers\Controller
{
    public function addUpdateQuizTable(Request $request)
    {
        try {
            $rules = [
                    'name' => 'required',
                    'school_id' => 'required',
                    'class_section_id' => 'required|numeric',
                    'subject_id' => 'required|numeric',
                    //'total_time' => 'required',
                    //'expired_time' => 'required',
                    'role' => 'required|in:teacher,admin,user,school'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }

            $objQuiz_Table = new Quiz_Table();
            $data= $objQuiz_Table->inserUpdateData($request);
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

    public function addUpdateQuizDetail(Request $request)
    {
        try {
            $rules = [
                    //'quiz_table_id' => 'required|numeric',
                    'question' => 'required',
                    //'question_number' => 'required|numeric',
                    'option_A' => 'required',
                    'option_B' => 'required',
                    'option_C' => 'required',
                    'option_D' => 'required',
                    'correct_answer' => 'required',
                    'role' => 'required|in:teacher,admin,user,school'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objQuiz_Detail = new Quiz_Detail();
            $data= $objQuiz_Detail->inserUpdateData($request);
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

    public function addUpdateQuizResult(Request $request)
    {
        try {
            $rules = [
                    'user_id' => 'required',
                    'school_id' => 'required',
                    'result' => 'required',
                    'role' => 'required|in:student,teacher,admin,user,school',
                    'quiz_table_id' => 'required|numeric',
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objQuiz_Result = new Quiz_Result();
            $data= $objQuiz_Result->inserUpdateData($request);
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

    public function getTableQuiz(Request $request)
    {
        try {
            $rules = [
                    'role' => 'required|in:student,teacher,admin,user,school',
                    'class_section_id' => 'required|numeric',
                    'subject_id' => 'required|numeric',
                    'school_id' => 'required|numeric'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objQuiz_Table = new Quiz_Table();
            $data = $objQuiz_Table->getTableData($request);
            if(!empty($data)){
                return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
                // if(!empty($data['message']))
                //     return $this->apiResponse(['message'=>$data['message'],'data'=>$data],true);
                // else        
                //     return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
            }
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function getQuiz(Request $request)
    {
        try {
            $rules = [
                    'role' => 'required|in:student,teacher,admin,user,school',
                    'quiz_table_id' => 'required|numeric'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objQuiz_Detail = new Quiz_Detail();
            $data = $objQuiz_Detail->getQuiz($request);
            if(!empty($data)){
                if(!empty($data['message']))
                    return $this->apiResponse(['message'=>$data['message'],'error'=>$data['message']],true);
                else
                    return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
            }
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function getQuizResult(Request $request)
    {
        try {
            $rules = [
                    'role' => 'required|in:student,teacher,admin,user,school',
                    'quiz_table_id' => 'required|numeric'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objQuiz_Result = new Quiz_Result();
            $data = $objQuiz_Result->getQuizResult($request);
            if(!empty($data)){
                return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
            }
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function quizForm(Request $request){
                                  
        $quiz=[];
        $schools = [];
        $class_sections = [];
        $subjects = [];
        if(Auth::guard('admin')->check())
        {
            $objSchool = new School();
            $schools = $objSchool->getAlldata();            
        }
        else if(Auth::guard('school')->check())
            $schools = School::where('id',$request->user_id)->get();
        $class_sections = Class_Section::select('class_section.*',DB::raw("CONCAT(class_section.class_name,'-',class_section.section_name) as class_name_section_name"));
                          $class_sections->where('class_section.deleted',0);
        if(session('user_school_id')!='')
            $class_sections = $class_sections->where('class_section.school_id',session('user_school_id'));
        $class_sections = $class_sections->get();

        // $subjects = Subject_Class::select('subject_master.*')->join('subject_master','subject_master.id','=','subject_class.subject_id');                               
        // if(session('user_school_id')!='')
        //     $subjects = $subjects->where('subject_class.school_id',session('user_school_id'));
        $subjects = Subject_Master::select('*');
        $subjects = $subjects->get();                                    

        if(!empty($request->id)){
           $quiz = (new Quiz_Table())->find($request->id)->toArray();
        }
        $request=$request->all();
        
        return view('BackEnd/QuizManagement.addeditquiz',compact("subjects","class_sections","schools","quiz","request"));  
    }
    
    public function addPublishQuizTable(Request $request)
    {
        try {
            $rules = [
                    'total_time' => 'required',
                    'expired_time' => 'required',
                    'publish' => 'required',
                    'role' => 'required|in:teacher,admin,user,school'
            ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }

            $objQuiz_Table = new Quiz_Table();
            $data= $objQuiz_Table->inserUpdateData($request);
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
    
    public function quizQuestionForm(Request $request,$id){
        $quize_id=$id;
        $request=$request->all();
        $quizs = Quiz_Table::select('quiz_table.*');
        if(session('user_school_id')!='')
            $quizs = $quizs->where('quiz_table.school_id',session('user_school_id'));
        $quizs = $quizs->get();
        return view('BackEnd/QuizDetailManagement.addquizquestion',compact("quizs","request","quize_id"));  
    }
    
    public function quizQuestionEditForm(Request $request,$id){
        $question_id=$id;
        $request=$request->all();
        $quiz_detail = Quiz_Detail::where('id',$question_id)->first();
        $quizs = Quiz_Table::select('quiz_table.*');
        if(session('user_school_id')!='')
            $quizs = $quizs->where('quiz_table.school_id',session('user_school_id'));
        $quizs = $quizs->get();
        return view('BackEnd/QuizDetailManagement.editquestion',compact("quizs","quiz_detail","request","question_id"));
    }
    
    public function quizPublishForm(Request $request,$id){
        $quize_id=$id;
        $request=$request->all();
        $quizs = Quiz_Table::select('quiz_table.*');
        if(session('user_school_id')!='')
            $quizs = $quizs->where('quiz_table.school_id',session('user_school_id'));
        $quizs = $quizs->get();
        return view('BackEnd/QuizManagement.publishquiz',compact("request","quize_id"));  
    }

    public function quizDetailForm(Request $request){
                                  
        $quizs=[];
        $schools = [];
        $quiz_detail = [];
        if(Auth::guard('admin')->check())
        {
            $objSchool = new School();
            $schools = $objSchool->getAlldata();            
        }
        else if(Auth::guard('school')->check())
            $schools = School::where('id',$request->user_id)->get();

        $quizs = Quiz_Table::select('quiz_table.*');
        if(session('user_school_id')!='')
            $quizs = $quizs->where('quiz_table.school_id',session('user_school_id'));
        $quizs = $quizs->get();
                                    

        if(!empty($request->id)){
           $quiz_detail = (new Quiz_Detail())->find($request->id)->toArray();
        }
        $request=$request->all();
        
        return view('BackEnd/QuizDetailManagement.addeditquizdetail',compact("quiz_detail","quizs","schools","request"));  
    }

}
