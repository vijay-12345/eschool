<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use App\Subject_Class;
use App\Subject_Master;
use App\Quiz_Table;
class Quiz_ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'Quiz  Reult');
    }

    public function index(Request $request)  
    {
        $quiz_name='';
        $zuiz_table_id='';
        $quiz_tables=DB::table('quiz_table');
        if(session('user_school_id')!='')
            $quiz_tables = $quiz_tables->where('school_id',session('user_school_id'));
        $quiz_tables = $quiz_tables->get(); 
        
        if($request->quiz_table_id){
        $quiz_table=DB::table('quiz_table');
        $quiz_name = $quiz_table->where('id',$request->quiz_table_id);
        $quizName = $quiz_name->first();
        $quiz_name= $quizName->name;
        }

        if(session('user_school_id')!='')
            $request['role'] ='school';    
        else
            $request['role'] ='admin';

        $schools = DB::table('school')->get();   

        $class_sections = DB::table('class_section');
        if(session('user_school_id')!='')
            $class_sections = $class_sections->where('school_id',session('user_school_id'));
        $class_sections = $class_sections->get();

        $subjects = DB::table('subject_master')->get();
//echo '<pre>'; print_r($request->all()); exit;
        $quiz_results=DB::table('quiz_result');
        $quiz_results = $quiz_results->where('quiz_table_id',$request->quiz_table_id)
                                    ->orderBy('result','DESC')
                                    ->orderBy('time_elapsed','ASC')->get();

        $students=DB::table('student');
        if(session('user_school_id')!='')
            $students = $students->where('school_id',session('user_school_id'));
        $students = $students->get();

        
        return view('BackEnd/QuizResultManagement.quiz_result',compact("request","students","quiz_results","schools","quiz_tables","class_sections","subjects","zuiz_table_id","quiz_name"));
    }
    
    public function quizresult(Request $request, $id='')  
    {   
        $quize_id=$id;
        $quiz_tables=DB::table('quiz_table');
        if(session('user_school_id')!='')
            $quiz_tables = $quiz_tables->where('school_id',session('user_school_id'));
        $quiz_tables = $quiz_tables->get(); 
        
        $quiz_section_subject=Quiz_Table::select('quiz_table.id','subject_master.subject_name','quiz_table.name',DB::raw("CONCAT(class_section.class_name,'-',class_section.section_name) as class_section_name"))
                //->Join('teacher_class_subject','teacher_class_subject.teacher_id','=','teacher.id')
                ->Join('subject_master','subject_master.id','=','quiz_table.subject_id')
                ->Join('class_section','class_section.id','=','quiz_table.class_section_id')
                ->where('quiz_table.id',$quize_id)
                ->first();
//echo '<pre>'; print_r($quiz_section_subject); exit;
        
        
        $quiz_results=DB::table('quiz_result');
        $quiz_results = $quiz_results->where('quiz_table_id',$id)
                                    ->orderBy('result','DESC')
                                    ->orderBy('time_elapsed','ASC')->get();

        if(session('user_school_id')!='')
            $request['role'] ='school';    
        else
            $request['role'] ='admin';

        $students=DB::table('student');
        if(session('user_school_id')!='')
            $students = $students->where('school_id',session('user_school_id'));
        $students = $students->get();

        
        return view('BackEnd/QuizResultManagement.quiz_result',compact("request","quiz_tables","quiz_results","students","quiz_section_subject"));
    }
    
    public function getsubject(Request $request){
        $obj_subjectClass= new Subject_Class();
        $responseSubject= $obj_subjectClass->getSubject($request);
        return json_encode($responseSubject);
    }
    
    public function getquize(Request $request){
        $quizs = Quiz_Table::where('class_section_id',$request->classSectionId)
                            ->where('subject_id',$request->subjectId)
                            ->select('id','name')->get()->toArray();
        return json_encode($quizs);
    }
    
    public function getquizeDetail(Request $request){
        $quizDetail = Quiz_Table::where('id',$request->quizeId)
                            ->select('class_section_id','subject_id')->first()->toArray();
        return json_encode($quizDetail);
        
    }

    // public function resultview(Request $request)  
    // {
    //     $quiz_results=DB::table('quiz_result');
    //     $quiz_results = $quiz_results->where('quiz_table_id',$request->quiz_table_id)
    //                                 ->orderBy('result','DESC')
    //                                 ->orderBy('time_elapsed','ASC')->get();

    //     $students=DB::table('student');
    //     if(session('user_school_id')!='')
    //         $students = $students->where('school_id',session('user_school_id'));
    //     $students = $students->get();

    //     return view('BackEnd/QuizResultManagement.quiz_result',compact("students","quiz_results"));
    // }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
}
