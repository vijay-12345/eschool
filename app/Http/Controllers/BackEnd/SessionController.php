<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use App\Session_Table, App\Teacher_Class_Subject, App\Subject_Class;
use App\School, App\Student, App\Subject_Master;
class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'Session MANAGEMENT');
    }
    public function index(Request $request)  
    {
        
        $teacher_class_subjects = [];
        if(Auth::guard('teacher')->check()){
            $user = session('user_details');
            $objTeacher_Class_Subject = new Teacher_Class_Subject();
            $teacher_class_subjects = $objTeacher_Class_Subject
                                                    ->getClassSectionSubject($user['id'],$user['school_id']);
        }
        $schools = School::all();
        //$students = Student::all();
        $class_sections = DB::table('class_section')->where('deleted',0);
        if(session('user_school_id')!='')
            $class_sections = $class_sections->where('school_id',session('user_school_id'));
        $class_sections = $class_sections->get();
       // $subjects = Subject_Master::all();
        $dropdown_selected = [];

        if(empty($request->class_section_id) && empty($request->subject_id)){
            $objTeacher_Class_Subject = Teacher_Class_Subject::select('id')->where('deleted',0);
            
            if(Auth::guard('teacher')->check()){
                $user_details = session('user_details');
                $array['school_id'] = $user_details->school_id;

                $objTeacher_Class_Subject = $objTeacher_Class_Subject->where('teacher_id',$user_details['id']);

                $dropdown_selected['teacher_class_subject'] = $request['teacher_class_subject_id'];
                // print_r($dropdown_selected);
                // die;
                
            }
            elseif(!(Auth::guard('admin')->check())) {
                $objTeacher_Class_Subject = $objTeacher_Class_Subject->where('school_id',session('user_school_id'));
            }
            if(empty($request->teacher_class_subject_id))
                $array['teacher_class_subject_id'] = $objTeacher_Class_Subject->get()->toArray();
            else
                $array['teacher_class_subject_id'] = $request->teacher_class_subject_id;
        }
        else
        {

            $objTeacher_Class_Subject = new Teacher_Class_Subject();
            $array['class_section_id'] = $request->class_section_id;
            $array['subject_ids'] = $request->subject_id;
            $dropdown_selected['class_section'] = $request->class_section_id;
            $dropdown_selected['subject'] = $request->subject_id;
            $dropdown_selected['school_id'] = $request->school_id;
            $array['school_id'] = $request->school_id;
            $array['teacher_class_subject_id'] = $objTeacher_Class_Subject->getTeacherClassSubjectIds($request->class_section_id,$request->subject_id,$request->school_id);
        }

        $array['from'] = $request->from;
        $array['to'] = $request->to;
        $request = (object) $array;
        // print_r($dropdown_selected);
        // die;

        $objSession_Table = new Session_Table();
        if(Auth::guard('teacher')->check() && empty($request->teacher_class_subject_id))
            $sessions = null;
        else
            $sessions = $objSession_Table->getTodaysSession($request);
        
        return view('BackEnd/sessionManagement.session2',compact("sessions","teacher_class_subjects","request","class_sections","schools","dropdown_selected"));
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('session_table')->where('id','=',$id)->delete();
        Session::flash('success_message','Session deleted successfully');
        return redirect(session("role").'/session');
    }
}
