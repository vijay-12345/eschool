<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use DB;
use Session;
use App\Class_Section;
use App\Time_Table;

class timetableManagement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'TIME TABLE MANAGEMENT');
    }
    public function index(Request $request)
    {
        
        $classSections = (new Class_Section())->getClass_Section($request);
        $allData = (new Time_Table())->getTimeTable($request);
        return view('BackEnd/timetableManagement.timetable',compact("classSections","allData"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('time_table')->where('id','=',$id)->delete();
        DB::table('attachments_table')->where('reference_id','=',DB::getPdo()->lastInsertId())->delete();
        Session::flash('success_message','Time table deleted successfully');
        return redirect(session("role").'/timetable');
    }
}
