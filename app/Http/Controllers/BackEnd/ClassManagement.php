<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use \Validator;
use App\School, App\Class_Section, App\Teacher_Class_Subject, App\Subject_Class;
class ClassManagement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'CLASS MANAGEMENT');
    }
    public function index()
    {
        $school_id = session('user_school_id');
        $classes = DB::table('class_section')->where('class_section.deleted',0)
        ->select('class_section.*','teacher.name as teacherName')
        ->leftjoin('teacher', function($join)
            {
              $join->on('teacher.id', '=', 'class_section.class_teacher_id')
              ->where('teacher.deleted', '=', 0);
            });
        //->leftjoin('teacher','teacher.id','=','class_section.class_teacher_id');
        if(session('user_school_id')!='')
            $classes->where('class_section.school_id',session('user_school_id') );
      
        $classes=$classes->get();
        $teachers = DB::table('teacher')->select('id','name')->where('deleted',0);
        if(session('user_school_id')!='')
            $teachers->where('teacher.school_id',session('user_school_id'));
        $teachers=$teachers->get();
        return view('BackEnd/classManagement.class',compact("classes","teachers","school_id"));
    }

    public function getClassSection(Request $request)
    {
        $class_sections = DB::table('class_section')->where('deleted',0);
        if(!empty($request->school_id))
            $class_sections = $class_sections->where('school_id',$request->school_id);
        $class_sections = $class_sections->get();
        if($class_sections)
            $class_sections = $class_sections->toArray();
        return json_encode($class_sections);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sub= Subject_Class::select(DB::raw("subject_class.id"))
            		     ->where('class_section_id',$id);
	$subject_class_ids=$sub->pluck('id')->toArray();

        $class_section = Class_Section::where('id', $id)->update(array('deleted' => 1));
        $subject_class = Subject_Class::where('class_section_id', $id)->update(array('deleted' => 1));

        $teacher_class_section = Teacher_Class_Subject::whereIn('subject_class_id', $subject_class_ids)->update(array('deleted' => 1));
        Session::flash('success_message','Class deleted successfully');
        return redirect(session("role").'/class');
      
    }
}
