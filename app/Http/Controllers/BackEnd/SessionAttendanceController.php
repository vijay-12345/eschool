<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use App\Session_Attendance, App\Student, App\Session_Table;
use App\Class_Section, App\School, App\Subject_Master, App\Teacher_Class_Subject;
use App\Exports\StudentsExport;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class SessionAttendanceController extends Controller
{
    public function __construct()
    {
        view()->share('page_title', 'Session Attendance Report');
    }
    
    public function index(Request $request)
    {
        $sessions = [];
        $session_attendances = [];
        $schools = School::all();
        $students = Student::all();
        $class_sections = DB::table('class_section')->where('deleted',0);
        if(session('user_school_id')!='')
            $class_sections = $class_sections->where('school_id',session('user_school_id'));
        $class_sections = $class_sections->get();
        $subjects = Subject_Master::all();
        $dropdown_selected = [];

        if($request->isMethod('post') || (!empty(session('subject')) && !empty(session('class_section')) ))
        {
            
            if(!empty(session('subject')) && !empty(session('class_section')))
            {
                $path = URL::previous();
                $components = explode('/', $path);
                $first_part = $components[4];
                if(!empty($components[5]))
                    $second_part = $components[5];
                else
                    $second_part = '';

                // data show with selected only when back from  'http://127.0.0.1:8000/school/session-attendance/view/1' 
                if($request->isMethod('get') && $first_part == 'session-attendance' && $second_part == 'view') {
                    $request->class_section_id = session('class_section');
                    $request->subject_id = session('subject');
                    $request->date_from = session('date_from');
                    $request->date_to = session('date_to');
                    if(Auth::guard('admin')->check())
                        $request->school_id = session('school');
                    else
                        $request->school_id = session('user_school_id');
                    $dropdown_selected['class_section'] = session('class_section');
                    
                    $dropdown_selected['subject'] = session('subject');
                    $dropdown_selected['date_from'] = session('date_from');
                    $dropdown_selected['date_to'] = session('date_to');
                    if(Auth::guard('admin')->check())
                        $dropdown_selected['school_id'] = session('school');
                    else
                        $dropdown_selected['school_id'] = $request->school_id;
                    session()->forget('class_section');
                    session()->forget('subject');
                    session()->forget('school');
                    session()->forget('date_from');
                    session()->forget('date_to');
                }
                else  // class_section and subject id are remove from session
                {
                    session()->forget('class_section');
                    session()->forget('subject');
                    session()->forget('school');
                    session()->forget('date_from');
                    session()->forget('date_to');
                }
            }
            else{
                $dropdown_selected['class_section'] = $request->class_section_id;
                $dropdown_selected['subject'] = $request->subject_id;
                $dropdown_selected['school_id'] = $request->school_id;
            }

            $objTeacher_Class_Subject =new Teacher_Class_Subject();
            $Teacher_Class_Subject_ids = $objTeacher_Class_Subject
                                ->getTeacherClassSubjectIds($request->class_section_id,$request->subject_id,$request->school_id);
            // print_r(json_encode($Teacher_Class_Subject_ids));
            $session = Session_Table::whereIn('teacher_class_subject_id',$Teacher_Class_Subject_ids);
                                    $session->where('school_id',$request->school_id);
                $start_time=' 00:00:00';
                $end_time=' 23:59:59';
                if($request['date_from'] && $request['date_to']){
                    $session->where('session_table.start_time','>=',$request['date_from'].$start_time);
                    $session->where('session_table.end_time','<=',$request['date_to'].$end_time);
                } 
                  $sessions=  $session->get();
            // print_r(json_encode($sessions));
            // die;
            foreach($sessions as $key => $value) {
                   $sessions[$key]['participate_student_count'] = Session_Attendance::where('session_table_id',$value['id'])
                                                                                    ->where('status','1')->count();
                   $sessions[$key]['total_student_count'] = Student::where('class_section_id',$request->class_section_id)
                                                                    ->count();        
            }
        }
        //echo '<pre>'; print_r($sessions); exit;
        // print_r(json_encode($dropdown_selected));
        // die;
        return view('BackEnd/sessionAttendanceManagement.sessionAttendance',compact("dropdown_selected","request","schools","class_sections","subjects","students","sessions"));
    }
    
    public function sessionAttendanceStudentWise(Request $request)
    {

        $totalsession=array();
        $students=array();
        $schools = School::all();
        $class_sections = DB::table('class_section')->where('deleted',0);
        if(session('user_school_id')!='')
            $class_sections = $class_sections->where('school_id',session('user_school_id'));
        $class_sections = $class_sections->get();
        $subjects = Subject_Master::all();
        $dropdown_selected = [];

        if($request->isMethod('post'))
        {
            $submit_type = $request->post('export');
            $totalsession = Session_Table::select(DB::raw("COUNT(session_table.id) as total_session"));
                $totalsession->join('teacher_class_subject', 'teacher_class_subject.id','session_table.teacher_class_subject_id');
                $totalsession->join('subject_class', 'subject_class.id','teacher_class_subject.subject_class_id');
                $totalsession->where('subject_class.class_section_id',$request['class_section_id']);
                $totalsession->where('subject_class.deleted',0);
                $totalsession->where('teacher_class_subject.deleted',0);
                if($request['subject_id']){
                $totalsession->where('subject_class.subject_id',$request['subject_id']);
                }
                $start_time=' 00:00:00';
                $end_time=' 23:59:59';
                if($request['from'] && $request['to']){
                    $totalsession->where('session_table.start_time','>=',$request['from'].$start_time);
                    $totalsession->where('session_table.end_time','<=',$request['to'].$end_time);
                }    
              $totalsession= $totalsession->first()->toArray();
            $totalsession=$totalsession['total_session'];
            $all_students = Student::select('id','name','profile_pic_url','isProfilePic')->where('class_section_id',$request['class_section_id'])->get();
            foreach($all_students as $student){
                $session_attend = Session_Attendance::select(DB::raw("COUNT(session_attendance.id) as total_attend"));
                    $session_attend->where('session_attendance.user_id',$student->id);
                    $session_attend->where('subject_class.class_section_id',$request['class_section_id']);
                    $session_attend->where('subject_class.deleted',0);
                    $session_attend->where('teacher_class_subject.deleted',0);
                    $session_attend->join('session_table','session_table.id','session_attendance.session_table_id');
                    $session_attend->join('teacher_class_subject','teacher_class_subject.id','session_table.teacher_class_subject_id');
                    $session_attend->join('subject_class','subject_class.id','teacher_class_subject.subject_class_id');
                    if($request['subject_id']){
                    $session_attend->where('subject_class.subject_id',$request['subject_id']);
                    }
                        if($request['from'] && $request['to']){
                            $session_attend->where('session_table.start_time','>=',$request['from'].$start_time);
                            $session_attend->where('session_table.end_time','<=',$request['to'].$end_time);
                        } 
                    $session_attend=$session_attend->first();
                $student['total_attend']= $session_attend->total_attend;
                $students[]=$student;

            }
            if($submit_type == 'export_record'){
                //echo '<pre>'; print_r($request->all()); exit;
                return Excel::download(new StudentsExport($request), 'students_report.xlsx');
            }
        }
        return view('BackEnd/sessionAttendanceManagement.studentSessionAttendance',compact("request","schools","dropdown_selected","class_sections","students",'totalsession'));
    }
    

    public function attendanceView(Request $request,$session_table_id,$date_from='',$date_to='')
    {
        $sessions = [];
        $session_attendances = Session_Attendance::where('session_table_id',$session_table_id)
                                                ->where('status','1')->get();
        $session_tables = DB::table('session_table')->select('teacher_class_subject_id')->where('id',$session_table_id)->first();
        $objTeacher_Class_Subject = new Teacher_Class_Subject();
        $dataTeacher_Class_Subject = $objTeacher_Class_Subject->getClassSubjectUsingTeacherClassSubject($session_tables->teacher_class_subject_id);
        
        session(['subject' => $dataTeacher_Class_Subject['subject_master_id']]);
        session(['class_section' => $dataTeacher_Class_Subject['class_section_id']]);
        session(['school' => $dataTeacher_Class_Subject['school_id']]);
        session(['date_from' => $date_from]);
        session(['date_to' => $date_to]);
        
        $students = DB::table('student');
        if(session('user_school_id')!='')
            $students = $students->where('school_id',session('user_school_id'));

        $students = $students->get();
        return view('BackEnd/sessionAttendanceManagement.sessionAttendanceView',compact("session_attendances","students"));
    }
    
    // public function delete($id)
    // {
    //     DB::table('app_version')->where('id','=',$id)->delete();
    //     Session::flash('success_message','App version deleted successfully');
    //     return redirect(session("role").'/app-version');
    // }
  
}
