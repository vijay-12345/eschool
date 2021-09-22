<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Subject_Master, App\Subject_Class, App\Teacher_Class_Subject;
use Auth;
use DB;
use Session;

class subjectManagement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'SUBJECT MASTER');
    }
    public function index()
    {
        $subjects=DB::table('subject_master')->where('deleted',0)->get();
        return view('BackEnd/subjectManagement.subject',compact("subjects"));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        $check = DB::table('subject_class')
//        ->where('subject_id', '=', $id)
//        ->get();
//        if (empty($check[0]->id)) {
        $sub= Subject_Class::select(DB::raw("subject_class.id"))
            		     ->where('subject_id',$id);
	$subject_class_ids=$sub->pluck('id')->toArray();
        
        
            $subjectMaster = Subject_Master::where('id', $id)->update(array('deleted' => 1));
            $subject_class = Subject_Class::where('subject_id', $id)->update(array('deleted' => 1));
            $teacher_class_section = Teacher_Class_Subject::whereIn('subject_class_id', $subject_class_ids)->update(array('deleted' => 1));
            //DB::table('subject_master')->where('id','=',$id)->delete();
            Session::flash('success_message','Subject deleted successfully');
//        } else {
//            Session::flash('error_message',"You can't delete this subject because this is available in subject mapping.");
//        } 
        return redirect(session("role").'/subject');
    }
}
