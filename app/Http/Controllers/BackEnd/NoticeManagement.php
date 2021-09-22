<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
class NoticeManagement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'NOTICE MANAGEMENT');
    }
    public function index()  
    {
        $notices=DB::table('notice_board');
        if(session('user_school_id')!='')
            $notices->where('notice_board.school_id',session('user_school_id'));
        if(Auth::guard('teacher')->check())
        {
            $notices->where('type','!=','student');
        }
        $notices = $notices->get();     
        return view('BackEnd/noticeManagement.notice',compact("notices"));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('notice_board')->where('id','=',$id)->delete();
        Session::flash('success_message','Notice deleted successfully');
        return redirect(session("role").'/notice');
    }
}
