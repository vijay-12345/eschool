<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use DB;
use Session;
use App\Assignment_Test;
use App\Session_Table, App\Teacher_Class_Subject, App\Subject_Class, App\Study_Material, App\Assignment, App\Attachments_Table;
class LibraryAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'Assignment');
    }
    public function index(Request $request)  
    {  

        if(session('user_school_id')!='')
            $request['role'] ='school';    
        else
            $request['role'] ='admin';
        
        $class_sections = DB::table('class_section')->where('deleted',0);
        if(session('user_school_id')!='')
            $class_sections = $class_sections->where('school_id',session('user_school_id'));
        $class_sections = $class_sections->get();

        $assignments=array();

        return view('BackEnd/libraryAssignmentManagement.assignment',compact("assignments","class_sections","request"));
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'class_section_id' => 'required',
                'subject_id'=>'required'
            ];
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            if(session('user_school_id')!='')
            $request['role'] ='school';    
            else
                $request['role'] ='admin';

            $class_sections = DB::table('class_section');
            if(session('user_school_id')!='')
                $class_sections = $class_sections->where('school_id',session('user_school_id'));
            $class_sections = $class_sections->get();

            $objAssignment = new Assignment_Test();
            $assignments = $objAssignment->getLibraryAssignmentByFilter($request);
            return view('BackEnd/libraryAssignmentManagement.assignment',compact("assignments","class_sections","request"));
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function download(Request $request){
        $file= DB::table('attachments_table')
                ->where('id',$request['id'])
                ->first();
        $file_url = $file->file_url;
        $fileArray=explode('/',$file_url);
        $file_name=$fileArray[3];
        
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$file_name."\""); 
        readfile($file_url);
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
