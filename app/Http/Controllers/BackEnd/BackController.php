<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Session_Table;
use App\User,App\Student,App\Teacher, App\School;
use Session;
use Auth;
use Hash;
use DB;

class BackController extends Controller
{
    public function __construct()
    {
      
        view()->share('page_title', 'DASHBOARD');
    }


    public function index(Request $request){
        $role= USER::getPrifix($request);
        $school_name=last($role);
        $role=$role[0];
        $schools = School::select('id','name')->get();
        if(session("is_active")==1){
            {
                if(Auth::guard('teacher')->check())
                {
                    return redirect($role.'/teacherdashboard');
                }
                else        
                    return redirect($role.'/dashboard');
            }
        }
        if($request->isMethod('post')){
             $rules = [
                'login_id' => 'required',
                'password' => 'required|min:3',
                'role' => 'required|in:student,teacher,admin,user,school',
            ];
            if(!empty($request->role) && !in_array($request->role, ['admin','school'])){
                $rules['school_id'] ='required|numeric';
            }
            $validatedData = $request->validate($rules );
            $objUser= new  User();
            $data = $objUser->getUserLogin($request, $request->role);
            if($data)
            {   if(Auth::guard('teacher')->check())
                {
                    return redirect($request->role.'/teacherdashboard');
                }
                else
                    return redirect($request->role.'/dashboard');
            }
            else
            {
                Session::flash('error_message','Invalid Email or Password');
                return redirect()->back();
            }
        }
        return view('BackEnd.login',compact('role','school_name','schools'));
    }
    
    public function dashboard(Request $request){

        $role= USER::getPrifix($request);
        $school_name=last($role);
        $role=$role[0];
       
        $studentCount=(new Student())->getStudentList()->count();

        $teacherCount=(new Teacher())->getTeacherList()->count();

        $obj = DB::table('session_table')->select('session_table.id','session_table.date','session_table.online_class_url','session_table.start_time','session_table.end_time','session_table.meeting_id','session_table.password','session_table.content','class_section.class_name','class_section.section_name','subject_master.subject_name', 'teacher.name as teacher_name');
			$todayDate=date('Y-m-d');
            if(session('user_school_id')!='')
    			$obj->where([ 'session_table.school_id'=>session('user_school_id')]);
	
            $obj->Join('teacher_class_subject','teacher_class_subject.id','=','session_table.teacher_class_subject_id')
	        ->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
	        ->Join('subject_master','subject_master.id','=','subject_class.subject_id')
	        ->Join('class_section','class_section.id','=','subject_class.class_section_id')
	        ->join('teacher',["teacher.id"=>"teacher_class_subject.teacher_id"]);
	        $obj->where('session_table.date','>=', $todayDate);
                $obj->where('teacher_class_subject.deleted',0);
                $obj->where('subject_class.deleted',0);
                $obj->where('subject_master.deleted',0);
                $obj->where('class_section.deleted',0);
            if(Auth::guard('teacher')->check())
            {
                $user_detail = session('user_details');
                $obj->where('teacher_class_subject.teacher_id', $user_detail['id']);
            }
            $obj->orderby('session_table.date',"ASC");

            $obj->groupby('session_table.id');
           $UpcommingSession=$obj->get();
        if(Auth::guard('teacher')->check())
        {
            return view('BackEnd.teacherdashboard',compact("UpcommingSession","role"));
        }
        else         
            return view('BackEnd.dashboard',compact("studentCount","teacherCount","UpcommingSession","role"));
    }

    public function profile(Request $request){
        $user_detail = session('user_details');
        $schools = DB::table('school')->get();
        $objUser = new User();
        $data = $objUser->getUserAllDetails($user_detail['id'],'teacher');

        return view('BackEnd.profile',compact("data","schools"));
    }

    public function changePassword(Request $request){
        $user_detail = session('user_details');
        return view('BackEnd.changePassword',compact("user_detail"));
    }

    public function logout(Request $request){
        Session::flush();
        $role= $request->route()->getPrefix();
        Auth::guard($role)->logout();
        if($_SERVER['HTTP_HOST']=='demo.vclassroom.in'){
         return redirect("");  
        }else{
         return redirect("/$role");   
        }
    }
}
