<?php
namespace App\Http\Controllers\BackEnd;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use Hash;
use Importer;
use App\User;
use \Validator;
use Redirect;

class SchoolManagement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'School MANAGEMENT');
    }
    public function index()  
    {
        $schools=DB::table('school')->get(); 
        return view('BackEnd/schoolManagement.school',compact("schools"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $schoolId= session('user_school_id');
        $role="school";
        $parent_schools=DB::table('school')->select('parent_id')->groupBy('parent_id')->get()->toArray();
        foreach($parent_schools as $parent_school){
            $parent_schools=DB::table('school')->select('id','name')->where('id',$parent_school->parent_id)->first();
            $schools[]=$parent_schools;
        }
        return view('BackEnd/schoolManagement.addSchool',compact("schools","schoolId","role"));
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
            'name' => 'required',
            'phone_no' => 'required',
            'email' => 'email|max:255',
            'login_id' => 'required',
            'password' => 'required'
        ],[
            'name.required'=>'School name is required.',
            'login_id.required' => 'School login id is required',
            'password.required' => 'School Password is required',
            'phono_no.required' => 'School Mobile No is required',
            'email.required' => 'School email is required',
        ]);
        $objSchool= new School();
        $data = $objSchool->createUpdateSchool($request, $request->role);
        if(!empty($data['id'])){
            Session::flash('success_message','School added successfully');
        }else{        
            Session::flash('error_message',$data);
        }
        return redirect(session("role").'/school');
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
        $user=new User();
        $data=$user->getUserAllDetails($id,'school');
        $data['schoolId']=session('user_school_id');
        $data['role']="school";     
        $parent_schools=DB::table('school')->select('parent_id')->groupBy('parent_id')->get()->toArray();
        foreach($parent_schools as $parent_school){
            $parent_schools=DB::table('school')->select('id','name')->where('id',$parent_school->parent_id)->first();
            $data['schools'][]=$parent_schools;
        }
        return view('BackEnd/schoolManagement.editSchool',compact('data'));
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
            'name' => 'required',
            'phone_no' => 'required',
            'email' => 'email|max:255',
            'login_id' => 'required',
            'password' => 'required'
        ],[
            'name.required'=>'School name is required.',
            'login_id.required' => 'School login id is required',
            'password.required' => 'School Password is required',
            'phono_no.required' => 'School Mobile No is required',
            'email.required' => 'School email is required',
        ]);
        $objSchool= new  School();
        $data = $objSchool->createUpdateSchool($request, $request->role);
        if(!empty($data['id'])){
            Session::flash('success_message','School updated successfully');
        }else{        
            Session::flash('error_message',$data);
        }
        return redirect(session("role").'/school');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('school')->where('id','=',$id)->delete();
        Session::flash('success_message','School deleted successfully');
        return redirect(session("role").'/school');
    }

    public function importExcel(Request $request){
        $rules =[
            'file'=>'required|max:5000|mimes:xlsx',
        ];
        $validatedData = Validator::make( $request->all(),$rules);
        $errors=array();
        if ($validatedData->fails()){
            $errors= $validatedData->errors();
            
            //return Redirect::back()->withErrors($errors);
        }
       
        $path=$request->file('file')->getRealPath();
        $excel = Importer::make('Excel');
        $excel->load($path);
        $data=$excel->getCollection();
        if(sizeof($data['1'])==13){
            $insertRow=0;
            $insert_data[]='';
            for($row=1;$row<sizeof($data);$row++){
            $dateTime = new $data[$row][11]; 
            $date=$dateTime->format('y-m-d');
            $rules=[
                $data[$row][0] => 'required',
                $data[$row][1] => 'required',
                $data[$row][2] => 'required',
                $data[$row][3] => 'email|max:255',
                $data[$row][5] => 'required',
                $data[$row][4] => 'numeric',
                $data[$row][7] => 'required',
                $data[$row][5]  => 'required',
                $data[$row][9]  => 'required||numeric',
                $data[$row][12] => 'required',
                $data[$row][8]  =>  'required',
                $date => 'required',
            ];  
            if(empty($erros)){
                $insert_data=array(
                    'school_id'=>Auth::guard(session("role"))->id(),
                    'name'=>$data[$row][0],
                    'registration_no'=>$data[$row][1],
                    'roll_no'=>$data[$row][5],
                    'class_section_id'=> $data[$row][2],   
                    'email'=>$data[$row][3], 
                    'phone_no'=>$data[$row][4],
                    'parent_name'=>$data[$row][8],
                    'login_id'=>$data[$row][6], 
                    'password'=>$data[$row][7], 
                    'parent_phono_no2'=>$data[$row][10],
                    'parent_phono_no1'=>$data[$row][9],
                    'address'=>$data[$row][12],
                    'dob'=>$date,
                );
                    $objUser= new  User();
                    $createdUser=$objUser->createUpdateUser($insert_data,'student');
                    $insertRow ++;
                    if(empty($createdUser['id'])){
                    Session::flash('error_message',"$createdUser for user row $row total user created $insertRow ");
                    return redirect(session("role").'/student');
                    }
                }
            }
        }else{
            Session::flash('error_message','Please provide data in file according to sample.');
            return redirect(session("role").'/student');
        }
        Session::flash('success_message','File uploaded successfully');
        return redirect(session("role").'/student');
    }
}
