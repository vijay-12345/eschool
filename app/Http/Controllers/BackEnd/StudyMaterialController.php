<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use App\Session_Table, App\Teacher_Class_Subject, App\Subject_Class, App\Study_Material, App\Attachments_Table;
class StudyMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'Study Material MANAGEMENT');
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
        else{
            $array['teacher_class_subject_id'] = $request->teacher_class_subject_id;
            $dropdown_selected['teacher_class_subject'] = $request->teacher_class_subject_id;
        }
        $request = (object) $array;
        $objStudy_Material = new Study_Material();
        if(Auth::guard('teacher')->check() && empty($request->teacher_class_subject_id))
            $studymaterials = null;
        else
            $studymaterials = $objStudy_Material->getStudyMaterial($request);

        if(!empty($studymaterials))    
            foreach ($studymaterials as $key => $value) {
               $studymaterials[$key]['total_attachments'] = $objStudy_Material->getTotalAttachments($value['id']);
            }
        $attachments = Attachments_Table::where('table_type','study_material')->get();

        return view('BackEnd/studymaterialManagement.studymaterial',compact("studymaterials","dropdown_selected","teacher_class_subjects","attachments"));

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('study_material')->where('id','=',$id)->delete();
        Session::flash('success_message','Study Material deleted successfully');
        return redirect(session("role").'/study-material');
    }
}
