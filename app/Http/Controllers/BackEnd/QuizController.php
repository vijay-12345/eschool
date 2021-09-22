<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use App\Quiz_Table, App\Quiz_Detail;
class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'Quiz');
    }
    public function index()  
    {
        
         $quiz_tables_data=DB::table('quiz_table')
                 ->select(DB::raw("COUNT(quiz_detail.id) as total_question"),'quiz_table.id as id','quiz_table.name','quiz_table.school_id','quiz_table.publish','quiz_table.total_time','quiz_table.start_time','quiz_table.expired_time','quiz_table.class_section_id','class_section.class_name','class_section.section_name','subject_master.subject_name')
                 ->join('class_section','class_section.id','quiz_table.class_section_id')
                 ->join('subject_master','subject_master.id','quiz_table.subject_id')
                 ->leftJoin('quiz_detail','quiz_detail.quiz_table_id','quiz_table.id')
                 ->where('quiz_table.school_id',session('user_school_id'))
                 ->where('class_section.deleted',0)
                 ->groupBy('quiz_table.id')
                 ->orderBy('quiz_table.start_time','DESC')
                 ->get(); 
         //echo '<pre>'; print_r($quiz_tables_data); exit;
         foreach($quiz_tables_data as $quiz_table){
             $total_attend=DB::table('quiz_result')
                     ->select(DB::raw("COUNT(quiz_result.id) as total_attempt"))
                     ->where('quiz_result.quiz_table_id',$quiz_table->id)
                     ->first();
             $quiz_table->total_attempt= $total_attend->total_attempt;
             $total_student=DB::table('student')
                     ->select(DB::raw("COUNT(student.id) as total_student"))
                     ->where('student.class_section_id',$quiz_table->class_section_id)
                     ->where('student.school_id',session('user_school_id'))
                     ->first();
             $quiz_table->total_students= $total_student->total_student;
             $quiz_tables[]=$quiz_table;
         }

        $class_sections = DB::table('class_section')->get();

        $subjects = DB::table('subject_master')->get();

        $quiz_details = DB::table('quiz_detail')->get();
        
        //echo '<pre>'; print_r($quiz_details); exit;

        $quiz_results = DB::table('quiz_result')->get();
        
        return view('BackEnd/QuizManagement.quiz',compact("quiz_tables"));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('quiz_table')->where('id','=',$id)->delete();
        Session::flash('success_message','Quiz deleted successfully');
        return redirect(session("role").'/quiz');
    }
    public function publish($id)
    {
        $data = Quiz_Table::where('id','=',$id)->first();
        if($data->publish == '1')
            $data->publish = '0';
        else
        {
            $quiz_detail_data = Quiz_Detail::where('quiz_table_id',$id)->get()->toArray();
            if(empty($quiz_detail_data))
            {
                Session::flash('success_message','Quiz publish not change, Atleast 1 Question required in quiz for Publish');
                return redirect(session("role").'/quiz');
            }
            else
                $data->publish = '1';
        }
        $data->save();
        Session::flash('success_message','Quiz publish change successfully');
        return redirect(session("role").'/quiz');
    }
}
