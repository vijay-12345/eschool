<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use \Validator;
use Importer;
use Session;
use App\Quiz_Table, App\School, App\Quiz_Detail, App\Class_Section;
class QuizDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'Question Management');
    }
    public function index(Request $request)  
    {
        $quiz_details=DB::table('quiz_detail');
        $quize_id='';
        if(!empty($request->quiz_table_id))
        {   $quize_id=$request->quiz_table_id;
            $quiz_details = $quiz_details->where('quiz_table_id',$request->quiz_table_id)->orderBy('question_number','ASC');
            $quiz_details = $quiz_details->get();
        }
        else
            $quiz_details = [];
        $quiz_table=DB::table('quiz_table');
        $quiz_tables = $quiz_table->get();
        return view('BackEnd/QuizDetailManagement.quizdetail',compact("request","quiz_details","quiz_tables","quize_id"));
    }

    public function create(Request $request, $id='')
    {
        $quize_id=$id;
        $quizs=[];
        $schools = [];
        $quiz_detail = [];
        $request = [];
        if(Auth::guard('admin')->check())
        {
            $request['role'] = 'admin';
            $objSchool = new School();
            $schools = $objSchool->getAlldata();            
        }
        else if(Auth::guard('school')->check())
        {
            $request['role'] = 'school';
            $request['school_id'] = session('user_school_id');
            $schools = School::all();
        }

        $quizs = Quiz_Table::select('quiz_table.*');
        if(session('user_school_id')!='')
            $quizs = $quizs->where('quiz_table.school_id',session('user_school_id'));
        $quizs = $quizs->get();
                                    

        if(!empty($request->id)){
           $quiz_detail = (new Quiz_Detail())->find($request->id)->toArray();
        }
        return view('BackEnd/QuizDetailManagement.addquizdetail',compact("quiz_detail","quizs","schools","request","quize_id"));
    }

    public function store(Request $request)
    {
      
        $rules = [
            'question_number' => 'required|numeric',
            'description' => 'required',
            'option_A' => 'email|max:255',
            'option_B' => 'required',
            'option_C'=>'numeric',
            'option_D' => 'required',
            'correct_answer'=>'required',
            'quiz_table_id' => 'required|numeric',
            'role' => 'required|in:teacher,admin,user,school'
        ];
        if($request->role != 'super_admin')
        {
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
             return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
        }
        $objQuiz_Detail= new Quiz_Detail();
        $data = $objQuiz_Detail->inserUpdateData($request);
        if(!empty($data['id'])){
            Session::flash('success_message','Quiz Details Question added successfully');
        }else{        
            Session::flash('error_message',$data);
        }
        return redirect(session("role").'/quiz-detail');
    }


    public function edit($id)
    {
        $quizs=[];
        $schools = [];
        $quiz_detail = [];
        $request = [];
        if(Auth::guard('admin')->check())
        {
            $request['role'] = 'admin';
            $objSchool = new School();
            $schools = $objSchool->getAlldata();            
        }
        else if(Auth::guard('school')->check())
        {
            $request['role'] = 'school';
            $request['school_id'] = session('user_school_id');
            $schools = School::all();
        }

        $quizs = Quiz_Table::select('quiz_table.*');
        if(session('user_school_id')!='')
            $quizs = $quizs->where('quiz_table.school_id',session('user_school_id'));
        $quizs = $quizs->get();
                                    

        if(!empty($id)){
           $quiz_detail = (new Quiz_Detail())->find($id)->toArray();
        }
        return view('BackEnd/QuizDetailManagement.editquizdetail',compact("quiz_detail","quizs","schools","request"));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'question_number' => 'required|numeric',
            'description' => 'required',
            'option_A' => 'email|max:255',
            'option_B' => 'required',
            'option_C'=>'numeric',
            'option_D' => 'required',
            'correct_answer'=>'required',
            'quiz_table_id' => 'required||numeric',
            'role' => 'required|in:teacher,admin,user,school'
        ];
        if($request->role != 'super_admin')
        {
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
             return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
        }
        $request->id = $id;
        $objQuiz_Detail= new Quiz_Detail();
        $data = $objQuiz_Detail->inserUpdateData($request);
        if(!empty($data['id'])){
            Session::flash('success_message','Quiz Details Question edited successfully');
        }else{        
            Session::flash('error_message',$data);
        }
        return redirect(session("role").'/quiz-detail');

    }    

    public function destroy($id)
    {
        DB::table('quiz_detail')->where('id','=',$id)->delete();
        Session::flash('success_message','Quiz Details Question deleted successfully');
        return redirect(session("role").'/quiz-detail');
    }

    public function importExcel(Request $request,$quiz_table_id){
        $rules =[
            'file'=>'required|max:5000|mimes:xlsx',
        ];
        $validatedData = Validator::make( $request->all(),$rules);
        $errors=array();
        if ($validatedData->fails()){
            $errors= $validatedData->errors();
            
            //return Redirect::back()->withErrors($errors);
        }
       
        $path=$request->file('file')->getRealPath();
        $excel = Importer::make('Excel');
        $excel->load($path);
        $data=$excel->getCollection();
        $size_excel = sizeof($data);
        if(sizeof($data['1'])<13){
            $insertRow=0;
            $insert_data[]='';
            for($row=1;$row<$size_excel;$row++){
           
            $rules=[
                $data[$row][0] => 'required',
                $data[$row][1] => 'required',
                $data[$row][2] => 'required',
                $data[$row][3] => 'required',
                $data[$row][4] => 'numeric',
                $data[$row][5] => 'required',
                $data[$row][6] => 'required'
            ];  
            if(empty($erros)){
                $insert_data=array(
                    'question'=>$data[$row][1],
                    'option_A'=>$data[$row][2],
                    'option_B'=> $data[$row][3],   
                    'option_C'=>$data[$row][4], 
                    'option_D'=>$data[$row][5],
                    'correct_answer'=>$data[$row][6],
                    'explaination'=>$data[$row][7], 
                    'quiz_table_id'=>$quiz_table_id, 
                );

                    $objQuiz_Detail= new Quiz_Detail();
                    $response_data = $objQuiz_Detail->inserUpdateData($insert_data);
                    $insertRow ++;
                    if(empty($response_data['id'])){
                    Session::flash('error_message',"$data for detail-question row $row total detail-question created $insertRow ");
                    return redirect(session("role").'/quiz-detail');
                    }
                }
            }
        }else{
            Session::flash('error_message','Please provide data in file according to sample.');
            return redirect(session("role").'/quiz-detail');
        }
        Session::flash('success_message','File uploaded successfully');
        return redirect(session("role").'/quiz-detail/addQuestion/'.$quiz_table_id);
    }
    
    public function addQuestion(Request $request,$id='')  
    {
        $quiz_details=DB::table('quiz_detail');
        if($id){
            $quize_id=$id;
        }else{
            $quize_id=$request->quiz_table_id;
        }
        if(!empty($request->quiz_table_id))
        {   $quize_id=$request->quiz_table_id;
            $quiz_details = $quiz_details->where('quiz_table_id',$request->quiz_table_id)->orderBy('question_number','ASC');
            $quiz_details = $quiz_details->get();
            
            $quiz_table=DB::table('quiz_table');
            $quiz_tables = $quiz_table->get();
        }
        else{
            $quiz_section_subject=Quiz_Table::select('class_section.id','subject_master.subject_name','quiz_table.name',DB::raw("CONCAT(class_section.class_name,'-',class_section.section_name) as class_section_name"))
                //->Join('teacher_class_subject','teacher_class_subject.teacher_id','=','teacher.id')
                ->Join('subject_master','subject_master.id','=','quiz_table.subject_id')
                ->Join('class_section','class_section.id','=','quiz_table.class_section_id')
                ->where('quiz_table.id',$quize_id)
                ->first();
//echo '<pre>'; print_r($quiz_section_subject); exit;
            
        $quiz_table=DB::table('quiz_table');
        $quiz_tables = $quiz_table->get();
        $quiz_details = $quiz_details->where('quiz_table_id',$quize_id)->orderBy('question_number','ASC');
        $quiz_details = $quiz_details->get();
        }
       // echo '<pre>'; print_r($quiz_details); exit;
        return view('BackEnd/QuizDetailManagement.quizdetail',compact("request","quiz_details","quiz_tables","quize_id","quiz_section_subject"));
    }

}
