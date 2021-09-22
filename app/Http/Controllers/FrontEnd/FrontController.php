<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Session;

class FrontController extends Controller
{
    public function index(Request $request){
        if($_SERVER['HTTP_HOST']=='demo.vclassroom.in'){
        $role= USER::getPrifix($request);
        $school_name=last($role);
        $role=$role[0];
        if(session("is_active")==1){
            return redirect($role.'/dashboard');
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
            if($data['id'] ==1){
            if($data)
            {   
                return redirect($request->role.'/dashboard');
            }
            else
            {
                Session::flash('error_message','Invalid Email or Password');
                return redirect()->back();
            }
            }else{
                 Session::flush();
                Session::flash('error_message','Please Enter Demo School Details Only');
                return redirect()->back();
            }
        }
        return view('FrontEnd.login',compact('role','school_name'));
        }else{
            return "welcome to landing page";
        }
    }

    public function fronttest(){
        return view('FrontEnd.test');
    }
}
