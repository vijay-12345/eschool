<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Class_Section, App\Subject_Class, App\Teacher_Class_Subject;
use Auth;
use DB;
use Session;

class subjectMaping extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'SUBJECT MANAGEMENT');
    }
    public function index(Request $request)
    {
        $classSections=(new Class_Section())->getClass_Section($request);
        $subjects=DB::table('subject_master')->get();

        $allClassSections=DB::table('subject_class')->where('subject_class.deleted',0)->where('class_section.deleted',0)
        ->Join('subject_master', 'subject_master.id', '=', 'subject_class.subject_id')
        ->join('class_section', 'class_section.id', '=', 'subject_class.class_section_id')
        ->select('subject_class.id','class_section.class_name','class_section.section_name','subject_master.subject_name');
         if(session('user_school_id')!='')
            $allClassSections->where('subject_class.school_id',session('user_school_id') );
       $allClassSections= $allClassSections->get();
        return view('BackEnd/subjectManagement.subjectMaping',compact("classSections","subjects","allClassSections"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'studnetClassSection' => 'required',
            'subject' => 'required',
        ]
        ,[
            'studnetClassSection.required'=>'Class-Section name is required',
            'subject.required'=>'Subject is required',
        ]);
        $data=[
            'school_id'=>session('user_school_id'),
            'class_section_id'=>$request->studnetClassSection,
            'subject_id'=>$request->subject,
         ];
         $check = DB::table('subject_class')
         ->where('class_section_id', '=', $request->studnetClassSection)
         ->where('subject_id', '=', $request->subject)
         ->get();
        if (empty($check[0]->id)) {
            DB::table('subject_class')->insert($data);
        } else {
        DB::table('subject_class')->where('id','=',$check[0]->id)->update($data);
        }
        Session::flash('success_message','Mapping added successfully');
        return redirect(session("role").'/mapping');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $classSections=DB::table('class_section')->get();
        $classSectionSelected=DB::table('subject_class')->where('id','=',$id)->first();
        $subjects=DB::table('subject_master')->get();
        $allClassSections=DB::table('subject_class')
        ->Join('subject_master', 'subject_master.id', '=', 'subject_class.subject_id')
        ->join('class_section', 'class_section.id', '=', 'subject_class.class_section_id')
        ->select('subject_class.id','class_section.class_name','class_section.section_name','subject_master.subject_name')
        ->get();
        return view('BackEnd/subjectManagement.editSubjectMaping',compact("classSectionSelected","subjects","allClassSections","classSections"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'studnetClassSection' => 'required',
            'subject' => 'required',
        ]
        ,[
            'studnetClassSection.required'=>'Class-Section name is required',
            'subject.required'=>'Subject is required',
        ]);
        $data=[
            'school_id'=>Auth::guard('school')->id(),
            'class_section_id'=>$request->studnetClassSection,
            'subject_id'=>$request->subject,
         ];
         $check = DB::table('subject_class')
         ->where('class_section_id', '=', $request->studnetClassSection)
         ->where('subject_id', '=', $request->subject)
         ->get();
        if (empty($check[0]->id)) {
            DB::table('subject_class')->where('id','=',$id)->update($data);
        } else {
        DB::table('subject_class')->where('id','=',$check[0]->id)->update($data);
        }
        
         Session::flash('success_message','Mapping updated successfully');
         return redirect(session("role").'/mapping');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $teacher_class_subject = Teacher_Class_Subject::where('subject_class_id', $id)->update(array('deleted' => 1));
        $subject_class = Subject_Class::where('id', $id)->update(array('deleted' => 1));
        Session::flash('success_message','Mapping deleted successfully');
        return redirect(session("role").'/mapping');
    }
}
