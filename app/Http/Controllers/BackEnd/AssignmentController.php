<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use App\Session_Table, App\Teacher_Class_Subject, App\Subject_Class;
use App\Study_Material, App\Assignment, App\Attachments_Table, App\Student;
class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'Assignment MANAGEMENT');
    }
    public function index(Request $request)  
    {
        $teacher_class_subjects = [];
        $dropdown_selected = [];
        if(Auth::guard('teacher')->check()){
            $user = session('user_details');
            $objTeacher_Class_Subject = new Teacher_Class_Subject();
            $teacher_class_subjects = $objTeacher_Class_Subject
                                                    ->getClassSectionSubject($user['id'],$user['school_id']);
        }
        $attachments = [];
        $user_details = session('user_details');
        $array['user_id'] = $user_details->id;
        $array['role'] = 'teacher';    
        $array['school_id'] = $user_details->school_id;
        if(empty($request->teacher_class_subject_id)){
            $objTeacher_Class_Subject = Teacher_Class_Subject::select('id')->where('deleted','0');
            $objTeacher_Class_Subject = $objTeacher_Class_Subject->where('school_id',session('user_school_id'));
            $objTeacher_Class_Subject = $objTeacher_Class_Subject->where('teacher_id',$user_details['id']);
            $array['teacher_class_subject_id'] = $objTeacher_Class_Subject->get()->toArray();
        }
        else
        {
            $array['teacher_class_subject_id'] = $request->teacher_class_subject_id;
            $dropdown_selected['teacher_class_subject'] = $request->teacher_class_subject_id;            
        }
        $request = (object) $array;
        
        $objAssignment = new Assignment();
        if(Auth::guard('teacher')->check() && empty($request->teacher_class_subject_id))
            $assignments = null;
        else
            $assignments = $objAssignment->getAssignment($request);
        if(!empty($assignments))
            foreach ($assignments as $key => $value) {
                
                $request->assignment_id = $value['id'];
                $request->teacher_class_subject_id = $value['teacher_class_subject_id'];
                $data_submitted_student = $objAssignment->getAssingmentOnlySubmittedStudentList($request);
                $data_not_submitted_student = $objAssignment->getAssingmentNotSubmittedStudentList($request);

                $assignments[$key]['submittedbystudent'] = count($data_submitted_student);
                $assignments[$key]['totalstudent'] = count($data_submitted_student) + count($data_not_submitted_student);
                $assignments[$key]['total_attachments'] = $objAssignment->getTotalAttachments($value['id']);         

            }

        $attachments = Attachments_Table::where('table_type','assignment')->get();



        return view('BackEnd/assignmentManagement.assignment',compact("assignments","teacher_class_subjects","dropdown_selected","attachments"));
    }

    // public function getsubject(Request $request){
        
    //     $obj_subjectClass= new Subject_Class();
    //     $responseSubject= $obj_subjectClass->getSubject($request);

    //     return json_encode($responseSubject);
    // }


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
