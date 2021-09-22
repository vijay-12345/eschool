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
use App\Class_Section;
use \Validator;
use App\Teacher;
use Redirect;
use App\School;

use App\Teacher_Class_Subject;

class teacherManagement extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        view()->share('page_title', 'TEACHER MANAGEMENT');
    }
    public function index()
    {
        //$school_id=Auth::guard(session("role"))->id();
        $school_id = [];
        if(session('user_school_id')!='')
            array_push($school_id,session('user_school_id'));
        else
        {
            $school_ids = School::select('id')->get();
            foreach ($school_ids as $schoolid) {
                array_push($school_id,$schoolid->id);
            }
        }
        $teachers = Teacher::with('file_url')->whereIN('school_id',$school_id)->where('deleted',0)->get();
        foreach($teachers as $key=>$value){
            $teacher_class_subject = Teacher_Class_Subject::select(DB::raw('GROUP_CONCAT(CONCAT_WS("-",class_name,section_name,subject_name)) as class_section_subject'))
            ->leftJoin('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
            ->leftJoin('subject_master','subject_master.id','=','subject_class.subject_id')
            ->leftJoin('class_section','class_section.id','=','subject_class.class_section_id')
            ->where('teacher_class_subject.teacher_id',$value->id)
            ->where('teacher_class_subject.deleted',0)
                    ->where('subject_class.deleted',0)
                    ->where('class_section.deleted',0)
            ->groupby('teacher_class_subject.teacher_id')
            ->first();
            if(empty($teacher_class_subject)){
                $teachers[$key]->teacher_class_subject ='';
                continue;
            }
              $teacher_class_subject=$teacher_class_subject->toArray();
          
            $teachers[$key]->teacher_class_subject = $teacher_class_subject['class_section_subject'];
        }
        return view('BackEnd/teacherManagement.teacher',compact("teachers"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $schoolList=School::select('id',"name")->get();
        $schoolId = session('user_school_id');
        $role = 'teacher';
        $classSections=(new Class_Section())->getclasssectionNameWithsubjectNotAssignToTeacher();
        return view('BackEnd/teacherManagement.addTeacher',compact("classSections","schoolId","role",'schoolList'));
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
            'email' => 'required|email|max:255',
            'login_id' => 'required',
            'password' => 'required',
            'employee_id'=>'required',
            'phone_no' => 'numeric',
            'address' => 'required',
            'teacherClassSection' => 'required',
        ],[
            'name.required'=>'Teacher name is required.',
            'login_id.required' => 'Teacher login id is required.',
            'email.required' => 'Teacher email is required',
            'password.required' => 'Teacher Password is required',
            'employee_id.required'=>'Teacher employee id is required',
            'phone_no.required'=>'Teacher mobile no is required',
            'address.required'=>'Teacher address is required',
            'teacherClassSection.required'=>'Teacher class-section name  is required',
        ]);
        $objUser= new  User();
        $data = $objUser->createUpdateUser($request, $request->role);
        if(!empty($data['id'])){
            Session::flash('success_message','Teacher added successfully');
        }else{        
            Session::flash('error_message',$data);
        }
        return redirect(session("role").'/teacher');
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
        $data=$user->getUserAllDetails($id,'teacher');
        $data['schoolList']=School::select('id',"name")->get();;
        $data['schoolId']=$data['school_id'];
        $data['role']="teacher";
        $data['slectedClassSubjects']=DB::table('teacher_class_subject')->where('teacher_id','=',$id)->where('deleted',0)->get('subject_class_id')->pluck('subject_class_id');

        $array2_slectedClassSubjects = $data['slectedClassSubjects'];
        $selected_data= Class_Section::select(DB::raw("subject_class.*,class_name, section_name, subject_name"))
        ->join('subject_class', function($join) use($array2_slectedClassSubjects){ 
            $join->on("subject_class.class_section_id","=","class_section.id");
            $join->whereIn('subject_class.id', $array2_slectedClassSubjects);
        })
        ->join('teacher_class_subject', function($join) { 
            $join->on("teacher_class_subject.subject_class_id","=","subject_class.id");
            $join->where('teacher_class_subject.deleted',0);
        })
        ->join('subject_master','subject_master.id','=','subject_class.subject_id');
        $selected_data = $selected_data->groupby('id');
        $selected_data=$selected_data->get();
        

        $data['classSections']=(new Class_Section())->getclasssectionNameWithsubjectNotAssignToTeacher();
        // print_r(json_encode($data['classSections']));
        // echo "good";
        foreach ($selected_data as $key => $value) {
            $data['classSections'][] = $value;
        }
        // print(json_encode($data['classSections']));
        // die;
        return view('BackEnd/teacherManagement.editTeacher',$data); 
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
            'email' => 'required|email|max:255',
            'login_id' => 'required',
            'password' => 'required',
            'employee_id'=>'required',
            'phone_no' => 'numeric',
            'address' => 'required',
            'teacherClassSection' => 'required',
        ],[
            'name.required'=>'Teacher name is required.',
            'login_id.required' => 'Teacher login id is required.',
            'email.required' => 'Teacher email is required',
            'password.required' => 'Teacher Password is required',
            'employee_id.required'=>'Teacher employee id is required',
            'phone_no.required'=>'Teacher mobile no is required',
            'address.required'=>'Teacher address is required',
            'teacherClassSection.required'=>'Teacher class-section name  is required',
        ]);
        $objUser= new  User();
        $data = $objUser->createUpdateUser($request, $request->role);
        if(!empty($data['id'])){
            Session::flash('success_message','Teacher updated successfully');
        }else{        
            Session::flash('error_message',$data);
        }
        return redirect(session("role").'/teacher');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $teacher = Teacher::where('id', $id)->update(array('deleted' => 1));
        //DB::table('teacher')->where('id','=',$id)->delete();
        DB::table('teacher_class_subject')->where('teacher_id','=',$id)->update(array('deleted' => 1));
        Session::flash('success_message','Teacher deleted successfully');
        return redirect(session("role").'/teacher');
    }

    // public function getExcelData($format='.csv'){
    //     $pathInfo = pathinfo($FILE['file']['name']);
    //     if($pathInfo['extension'] != $format){
    //     $this->set('status', 0);
    //     $this->set('message', 'Invalid File. please check.');
    //     return false;
    //     }
    //     $file = fopen($FILE['file']['tmp_name'],"r");
    //     $fileData=[];
    //     $headerArray= fgetcsv($file);

    //     while($row = fgetcsv($file)){
    //     $fileData[]=array_combine($headerArray, $row);
    //     }
    //     fclose($file);
    // }
    
    public function excelUpload(Request $request)
    {
        $rules =[
            'file'=>'required|max:5000|mimes:xlsx',
        ];
        $validatedData = Validator::make( $request->all(),$rules);
        $errors=array();
        if ($validatedData->fails()){
            $errors= $validatedData->errors();
            return Redirect::back()->withErrors($errors);
        }
        $path=$request->file('file')->getRealPath();
        $excel = Importer::make('Excel');
        $excel->load($path);
        $data=$excel->getCollection();
        $objUser=new User();
        $insertData[]=array();
        $header=$data[0];
        $header = array_map('strtolower', $header);
        $header = array_map('trim', $header);
        $rulesTeacher=[
            'name' => 'required',
            'email' => 'email|max:255',
            'login_id' => 'required',
            'password' => 'required',
            'employee_id'=>'required',
            'phone_no' => 'numeric',
            'address' => 'required',
        ];
        $rulesStudent=[
            'name' => 'required',
            'registration_no' => 'required',
            'email' => 'email|max:255',
            'login_id' => 'required',
            'phone_no'=>'numeric',
            'password' => 'required',
            'roll_no'=>'required',
            'parent_phono_no1' => 'required||numeric',
            'address' => 'required',
            //'parent_name' => 'required',
            'dob' =>'required'
            
        ];
           
        for($row=1;$row<sizeof($data);$row++){
            $excelRow=array_combine($header,$data[$row]);
            if($excelRow['role']=='teacher'){
                $excelRow['date_of_joining']=$excelRow['date_of_joining']->format('Y-m-d');
                $excelRow['subject_class_id']=empty(trim($excelRow['subject_class_id']))?[]:explode(',',$excelRow['subject_class_id']);
                
            }
            $excelRow['school_id']=Auth::guard(session("role"))->id();
            if(!empty($excelRow['dob']))
                $excelRow['dob']=$excelRow['dob']->format('Y-m-d');
            if($excelRow['role']=='teacher'){
                $validatedData = Validator::make( $excelRow,$rulesTeacher);
            }elseif($excelRow['role']=='student'){
                $validatedData = Validator::make($excelRow,$rulesStudent);
            }else{
                Session::flash('error_message',"Role not defined on line $row not define");
                return redirect(session("role").'/teacher');
            }

            if ($validatedData->fails()){
                $errors= $validatedData->errors();
                return view('BackEnd/teacherManagement.teacher')->withErrors($errors);
            }
            if(empty($erros)){
                $createdUser=$objUser->createUpdateUser($excelRow,$excelRow['role']);
                if(empty($createdUser['id'])){
                    Session::flash('error_message',"On row no $row $createdUser");
                    if($excelRow['role']=='teacher'){
                        return redirect(session("role").'/teacher');
                    }elseif($excelRow['role']=='student'){
                        return redirect(session("role").'/student');
                    }
                }
            }else{
                Session::flash('error_message',"on line no $key");
            }
        }
        Session::flash('success_message',"File imported successfully");
        if($excelRow['role']=='teacher'){
            return redirect(session("role").'/teacher');
        }elseif($excelRow['role']=='student'){
            return redirect(session("role").'/student');
        }
    }
    
}
