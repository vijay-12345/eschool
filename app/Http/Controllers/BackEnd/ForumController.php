<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use App\Session_Table, App\Teacher_Class_Subject, App\Subject_Class;
use App\Study_Material, App\Assignment, App\Attachments_Table, App\Forum;
class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'Forum MANAGEMENT');
    }
    public function index()  
    {
        $user_detail = session('user_details');
        $request['teacher_id'] = $user_detail['id'];
        $request['school_id'] = $user_detail['school_id'];

        $objTeacher_Class_Subject = new Teacher_Class_Subject();
        $teacher_class_subjects = $objTeacher_Class_Subject
                                    ->getClassSectionSubject($request['teacher_id'],$request['school_id']);

                                    

        return view('BackEnd/forumManagement.forum',compact("teacher_class_subjects"));
    }

    public function forumdataview(Request $request,$teacher_class_subject_id){
        $request['role'] = session('role');
        $students = [];
        $teachers = [];
        $teacher_class_subject =[];
        $user_detail = session('user_details');
        $request['teacher_id'] = $user_detail['id'];
        $request['school_id'] = $user_detail['school_id'];

        $students = DB::table('student')->where('school_id',$user_detail['school_id'])->get();
        $teachers = DB::table('teacher')->where('school_id',$user_detail['school_id'])->get();

        $datas =  DB::table('forum_table')->where('teacher_class_subject_id','=',$teacher_class_subject_id)->orderBy('date','ASC')->get();
        $teacher_class_subject['id'] =$teacher_class_subject_id;

        $attachments = Attachments_Table::where('table_type','forum_table')->get();




        return view('BackEnd/forumManagement.forumview',compact("request","datas","students","teachers","teacher_class_subject","attachments"));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('assignment')->where('id','=',$id)->delete();
        Session::flash('success_message','Assignment deleted successfully');
        return redirect(session("role").'/assignment');
    }
}
