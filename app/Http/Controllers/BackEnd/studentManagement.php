<?php
namespace App\Http\Controllers\BackEnd;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use Hash;
use Importer;
use App\User,App\Class_Section,App\Student;
use \Validator;
use Redirect;
use App\School;

class studentManagement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'STUDENT MANAGEMENT');
    }
    public function index(Request $request)
    {
        $dropdown_selected = [];
        $schools = [];
        if(Auth::guard('admin')->check())
            $schools = DB::table('school')->get();
        if(!empty($request->school_id))
            $dropdown_selected['school_id'] = $request->school_id;
        if(!empty($request->class_section_id))
            $dropdown_selected['class_section'] = $request->class_section_id;
        $class_sections = DB::table('class_section')->where('deleted',0);
        if(session('user_school_id')!='')
            $class_sections->where('school_id',session('user_school_id'));
        $class_sections = $class_sections->get();
        $students=DB::table('student')->where('student.deleted',0)
        ->leftJoin('class_section', 'class_section.id', '=', 'student.class_section_id')
        ->select('student.*','class_section.class_name','class_section.section_name');
        if(session('user_school_id')!='')
            $students->where('student.school_id',session('user_school_id'));
        if($request->isMethod('post'))
        {
            if(!empty($request->class_section_id))
                $students->where('student.class_section_id',$request->class_section_id);
            else{
                Session::flash('success_message','Student class_section Must Required');
                return redirect(session("role").'/student');
            }
        
        }
        
        $students = $students->get();
        return view('BackEnd/studentManagement.student',compact("students","class_sections","dropdown_selected","schools"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $schoolList=School::select('id',"name")->get();
        $schoolId = session('user_school_id');
        
        $role="student";
	$classSections=(new Class_Section())->getClass_Section($request);
        return view('BackEnd/studentManagement.addStudent',compact("classSections","schoolId","role","schoolList"));
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
            'registration_no' => 'required',
            'email' => 'email|max:255',
            'login_id' => 'required',
            'phone_no'=>'numeric',
            'password' => 'required',
            'roll_no'=>'required',
            'parent_phono_no1' => 'required||numeric',
            'address' => 'required',
            'parent_name' => 'required',
            'dob' =>'required',
            'class_section_id'=>'required'
        ],[
            'name.required'=>'Student name is required.',
            'registration_no.required' => 'Student registration no is required.',
            'class_section_id.required' => 'Student class-section name is required.',
            'login_id.required' => 'Student login id is required',
            'password.required' => 'Student Password is required',
            'roll_no.required'=>'Student roll no is required',
            'parent_phono_no1.required' => 'Student parents Mobile No 1 is required',
            'address.required' => 'Student address is required',
            'studentphoneNo.numeric'=>'The student phone no must be a number',
            'parent_name.required'=>'Student parents name is required',
            'dob.required'=>'Date of birth is required',
        ]);
        $objUser= new User();
        $data = $objUser->createUpdateUser($request, $request->role);
        if(!empty($data['id'])){
            Session::flash('success_message','Student added successfully');
        }else{        
            Session::flash('error_message',$data);
        }
        return redirect(session("role").'/student');
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
        $data=$user->getUserAllDetails($id,'student');
        $data['schoolList']=School::where('id',$data['school_id'])->select('id',"name")->get();
        $data['classSections']=DB::table('class_section')->where('id',$data['class_section_id'])->get();
        $data['schoolId']=$data['school_id'];
        $data['role']="student";
        return view('BackEnd/studentManagement.editStudent',$data);
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
            'registration_no' => 'required',
            'email' => 'email|max:255',
            'login_id' => 'required',
            'phone_no'=>'numeric',
            'password' => 'required',
            'roll_no'=>'required',
            'parent_phono_no1' => 'required||numeric',
            'address' => 'required',
            'parent_name' => 'required',
            'dob' =>'required',
            'class_section_id'=>'required'
        ],[
            'name.required'=>'Student name is required.',
            'registration_no.required' => 'Student registration no is required.',
            'class_section_id.required' => 'Student class-section name is required.',
            'login_id.required' => 'Student login id is required',
            'password.required' => 'Student Password is required',
            'roll_no.required'=>'Student roll no is required',
            'parent_phono_no1.required' => 'Student parents Mobile No 1 is required',
            'address.required' => 'Student address is required',
            'studentphoneNo.numeric'=>'The student phone no must be a number',
            'parent_name.required'=>'Student parents name is required',
            'dob.required'=>'Date of birth is required',
        ]);
        $objUser= new  User();
        $data = $objUser->createUpdateUser($request, $request->role);
        if(!empty($data['id'])){
            Session::flash('success_message','Student updated successfully');
        }else{        
            Session::flash('error_message',$data);
        }
        return redirect(session("role").'/student');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Student::where('id', $id)->update(array('deleted' => 1));
        Session::flash('success_message','Student deleted successfully');
        return redirect(session("role").'/student');
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
        
        if(sizeof($data['1'])==15){
            $insertRow=0;
            $insert_data[]='';
            for($row=1;$row<sizeof($data);$row++){
            $dateTime = new $data[$row][12]; 
            $date=$dateTime->format('y-m-d');
            $rules=[
                $data[$row][1] => 'required',
                $data[$row][2] => 'required',
                $data[$row][3] => 'required',
                $data[$row][4] => 'email|max:255',
                $data[$row][6] => 'required',
                $data[$row][5] => 'numeric',
                $data[$row][8] => 'required',
                $data[$row][6]  => 'required',
                $data[$row][10]  => 'required||numeric',
                $data[$row][13] => 'required',
                $data[$row][9]  =>  'required',
                $date => 'required',
            ];  
            if(empty($erros)){
                $insert_data=array(
                    'school_id'=>Auth::guard(session("role"))->id(),
                    'name'=>$data[$row][2],
                    'registration_no'=>$data[$row][3],
                    'roll_no'=>$data[$row][6],
                    'class_section_id'=> $data[$row][14],   
                    'email'=>$data[$row][4], 
                    'phone_no'=>$data[$row][5],
                    'parent_name'=>$data[$row][9],
                    'login_id'=>$data[$row][7], 
                    'password'=>$data[$row][8], 
                    'parent_phono_no2'=>$data[$row][11],
                    'parent_phono_no1'=>$data[$row][10],
                    'address'=>$data[$row][13],
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
