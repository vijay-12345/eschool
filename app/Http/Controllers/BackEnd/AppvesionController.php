<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;

class AppvesionController extends Controller
{
    public function __construct()
    {
        view()->share('page_title', 'APP VERSION');
    }
    public function index()
    {
        $allApps=DB::table('app_version')
        ->select('app_version.*','school.name as schoolName')
        ->leftjoin('school','school.id','=','app_version.school_id')
        ->get();
        return view('BackEnd/appVersionManagement.appVersion',compact("allApps"));
    }
    public function delete($id)
    {
        DB::table('app_version')->where('id','=',$id)->delete();
        Session::flash('success_message','App version deleted successfully');
        return redirect(session("role").'/app-version');
      
    }
  
}
