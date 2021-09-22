<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DB;
use Auth;
use Hash;
use Session;
use Illuminate\Validation\Rule;
use App\School, App\Admin;
use App\Student, App\Teacher ,App\Teacher_Class_Subject;
use App\Attachments_Table;

class User extends Authenticatable
{
    use Notifiable;
    protected $softDelete = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','email_verified_at','password','remember_token','pass_code','device_token','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function file_url()
    {
        return $this->hasMany('App\Attachments_Table','reference_id','id')->where('table_type',$this->table)->where('status',1);
    }

    public function getTable(){
        return $this->table;
    }

    public static function getId($request){
      return Auth::guard($request->role)->user()->id;
    }

    public static function getPrifix($request){
      $role =$request->route()->getPrefix();
      return explode("/", $role);
    }

    public static function getclass_section($request){
      if($request->teacher_class_subject_id)
      {
        $class_section_details = Teacher_Class_Subject::select('class_section.*')
                                        ->join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
                                        ->join('class_section','class_section.id','=','subject_class.class_section_id')
                                        ->where('teacher_class_subject.id',$request->teacher_class_subject_id)
                                        ->where('subject_class.deleted',0)
                                        ->where('class_section.deleted',0)
                                        ->first();
        return $class_section_details;                                            
      }
    }


    public function getUserObject($userType){
       if($userType=='teacher'){
           $objUser =new Teacher();
        }
        else if($userType=='student'){
           $objUser= new Student();
        }
        else if($userType=='admin'){
           $objUser= new Admin();
        }
        else if($userType=='user'){
           $objUser= new User();
        } 
        else if($userType=='school'){
           $objUser= new School();
        }  
        return $objUser;
    }

    public  function CheckUserAuth($request){

          $objUser =$this->getUserObject($request->role);

          $lastOneHour=date("Y-m-d H:i:s");
          $lastOneHour = date('Y-m-d H:i:s',strtotime('-2 days',strtotime($lastOneHour)));

          $user = $objUser->where(['token'=>$request->header('Authorization')])
              ->where('updated_at' ,'>=' ,$lastOneHour)->first();
      
          if(!empty($user)){
              session(["is_active"=>1]);
              session(["role"=>$request->role]);
              session(["user_school_id"=>'']);
              if($request->role!='admin'){
                session(["user_school_id"=>$user->id]);
                if($request->role!='school')
                  session(["user_school_id"=>$user->school_id]);              
              }
              session(["user_details"=>$user]);
            $request->user_id = $user->id;
            $user->password = $this->string_decrypt($user->pass_code);
            if(Auth::guard($request->role)->attempt(['login_id'=>$user->login_id,'password'=>$user->password]))
              return true;

          }
       return false;
    }

    public function getsignUpRules($request){
        
          $rules=[
            "role"     => "required|in:student,teacher,admin,user,school",
            //'password' => 'required|min:3|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password' => 'required|min:3',
            'login_id' => "required",
            'name'     => "required|string",
          ];

          $userType="";
          
          if(!empty($request->role)){
            $userType = $request->role;
            $rules['email'] = "email|unique:$userType";
           }

          if(!empty($request->id)){
            $rules['email'] =  [ Rule::unique($userType)->ignore($request->id)];
          }

          if($userType=='teacher'){
              $rules['school_id'] ="required|numeric";
              $rules['date_of_joining'] = "required|date" ;
              $rules['employee_id'] = "required";
              $rules['phone_no'] = "required|numeric|min:10";
              $rules['address'] = "required";
             // $rules['dob'] = "required|date";
          }
          else if($userType=='student'){
              $rules['school_id'] ="required|numeric";
              $rules['registration_no']="required";
              $rules['roll_no']="required";
              $rules['class_section_id']="required|numeric";
              $rules['parent_name']="required|string";
              $rules['parent_phono_no1']="required|numeric|min:10";
              $rules['dob'] = "required|date";
              $rules['phone_no'] = "required|numeric|min:10";
              $rules['address'] = "required";
          }
          else if($userType=='school'){
              $rules['parent_id'] = "required|numeric";
              $rules['phone_no'] = "required|numeric|min:10";
              $rules['address'] = "required";
            //   $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:10000';
          } 
          return $rules;
    }

    public function getUserDetails($id, $userType){
        $objUser =$this->getUserObject($userType);
        //$data = $objUser->find($id);
        $data = $objUser->where('deleted',0)->where('id',$id)->first();
        unset($data['pass_code'],$data['password']);
        return $data;
    }

     public function getUserLogin($request, $userType){
        $returnData=[];
        if($request->role == 'teacher' or $request->role == 'student'){
            $condition=['login_id'=>$request->login_id, 'password'=>$request->password, 'deleted'=>0];    
        }else{
            $condition=['login_id'=>$request->login_id, 'password'=>$request->password];
        }
        if(!empty($request->school_id))
           $condition['school_id']=$request->school_id;
        if(Auth::guard($userType)->attempt($condition)){
                $user = Auth::guard($userType)->user();
                $token = md5(uniqid($user->email.$user->updated_at, true));
                $user->token = $token;
                if(!empty($request->device_token)){
                  $user->device_token =$request->device_token;
                }
                $user->save();
                session(["is_active"=>1]);
                session(["role"=>$userType]);
                session(["user_school_id"=>'']);
                if($userType!='admin'){
                  session(["user_school_id"=>$user->id]);
                  if($userType!='school')
                    session(["user_school_id"=>$user->school_id]);              
                }
                session(["user_details"=>$user]);
                $returnData = $this->getUserAllDetails( $user->id, $userType);
          }
          return $returnData;
    }
     public function getUserAllDetails($id, $userType){
          $returnData=[];
           $userOthetDetails=[];
          if($userType=='teacher'){
                 $user = Teacher::with('file_url')->find($id);
                 $teacher_class_subject = Teacher_Class_Subject::select('teacher_class_subject.id','teacher_class_subject.subject_class_id   as subject_class_id','subject_master.id as subject_id',
                    'subject_master.subject_name',
                    'class_section.id as class_section_id',
                    'class_section.class_name',
                    'class_section.section_name')
                  ->leftJoin('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
                  ->leftJoin('subject_master','subject_master.id','=','subject_class.subject_id')
                  ->leftJoin('class_section','class_section.id','=','subject_class.class_section_id')
                  ->where('teacher_class_subject.teacher_id',$user->id)
                  ->where('teacher_class_subject.deleted',0)
                  ->get();
                $userOthetDetails['teacher_class_subject'] = $teacher_class_subject->toArray();
          }
          else if($userType=='student'){
                $user = Student::with('file_url','class_section')->where('deleted',0)->find($id);
                
                $subject_class_data = Subject_Class::select('subject_master.id','subject_master.subject_name')
                      ->leftJoin('subject_master','subject_master.id','=','subject_class.subject_id')
                      ->where('subject_class.class_section_id',$user->class_section_id)
                      ->where('subject_master.deleted',0)
                      ->where('subject_class.deleted',0)
                      ->get();
                $userOthetDetails['subjects'] = $subject_class_data->toArray();
          }
          else if($userType=='user'){
                $user = self::with('file_url')->find($id);
          }else if($userType=='school'){
                 $user = School::with('file_url')->find($id);
          }else if($userType=='admin'){
                 $user = Admin::with('file_url')->find($id); 
          }
          $user=$user->toArray();
          if(!empty($user["pass_code"])){
               $user['password'] = $this->string_decrypt($user["pass_code"]);
          } 
          $returnData = array_merge($user, $userOthetDetails);
          return $returnData;
    }

    public function getAllUsers($request)
    {
        if($request->role == 'teacher')
        {
            $objTeacher = Teacher::select('id','name','login_id','employee_id')->where('deleted',0);
            if(!empty($request->page_order)){
                $objTeacher->orderby($request->page_order[0],$request->page_order[1]); 
            }
            if(!empty($request->page_limit)){
                $data = $objTeacher->paginate($request->page_limit)->toArray();
                foreach ($data['data'] as $key => $value) {
                  $data['data'][$key]['class_section_and_subject_name'] = $this->getclass_section_and_subject_name($value['id']);
                }
            }
            else
            {
                $data = $objTeacher->get()->toArray(); 
                foreach ($data as $key => $value) {
                  $data[$key]['class_section_and_subject_name'] = $this->getclass_section_and_subject_name($value['id']);
                } 
            }
        }
        else if($request->role == 'student')
        {
            $objStudent = Student::select('id','name','login_id','registration_no','roll_no','class_section_name')->where('deleted',0);
            if(!empty($request->page_order)){
                $objStudent->orderby($request->page_order[0],$request->page_order[1]); 
            }
            if(!empty($request->page_limit)){
                $data = $objStudent->paginate($request->page_limit)->toArray();
            }
            else
            {
                $data = $objStudent->get()->toArray(); 
            }

        }
        return $data;
    }

    public function getclass_section_and_subject_name($id)
    {
        if($id){
              $data = Teacher::select(DB::raw("CONCAT(class_section.class_name,'-',class_section.section_name,'-',subject_master.subject_name) as Class_section_And_Subject_name"))
                              ->join('teacher_class_subject','teacher_class_subject.teacher_id','=','teacher.id')
                              ->join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
                              ->join('class_section','class_section.id','=','subject_class.class_section_id')
                              ->join('subject_master','subject_master.id','=','subject_class.subject_id')
                              ->where('teacher.id',$id)
                              ->where('teacher_class_subject.deleted','0')->get();
              return $data;
            }
    }

    public function createUpdateUser($request,$userType){
        
        $objUser =$this->getUserObject($userType);
        if(!empty($request["password"])){
            $request["pass_code"]= $this->string_encrypt($request["password"]);
            $request["password"]= Hash::make($request["password"]);
        }
       

        if(!empty($request->id)){
            $id = $request->id;
            $insertData = $objUser->find($id);
        }else{
          
             $insertData=[];
             if($userType=='teacher' || $userType=='student'){      
                 $user= $objUser->where(['school_id'=>$request["school_id"],'login_id'=>$request["login_id"]])->first();
             }else{
                 $user= $objUser->where(['login_id'=>$request["login_id"]])->first();
             }
             if($user){
                return "user login id already exist";
             }
        }

        foreach ($objUser->fillable as $key => $value) {
            if(!empty($request[$value]))
                $insertData[$value]=$request[$value];
        }
        if($userType=='school'){
            if($request->hasFile('image')){
             $objAttachments_Table= new Attachments_Table();	
             $url = $objAttachments_Table->uploadAttechments($request);
             $url = $url[0];
             $insertData['logo_url']=$url;
            }
        }
        if($userType=='teacher' || $userType=='student'){
            if(!is_array($request) && $request->hasFile('image')){
             $objAttachments_Table= new Attachments_Table();  
             $url = $objAttachments_Table->uploadAttechments($request);
             $url = $url[0];
             $insertData['isProfilePic']='1';
             $insertData['profile_pic_url']=$url;
            }
        }
        if(!empty($insertData)){
            if(!empty($insertData["id"])){
                $objUser->where(['id'=>$insertData["id"]])->update($insertData->toArray());
                if($userType=='school' && $request['parent_id']==0){
                    $update=array('parent_id'=>$insertData["id"]);
                    $objUser->where(['id'=>$insertData["id"]])->update($update);
                }
            }else{
                $id = $objUser->create($insertData )->id;               
                if($userType=='school' && $request['parent_id']==0){
                    $update=array('parent_id'=>$id);
                    $objUser->where(['id'=>$id])->update($update);
                }
            }
            if(!empty($request->teacherClassSection)){
                (new Teacher_Class_Subject())->addUpdateTeacherClassSubject($id, $request->school_id, $request->teacherClassSection); 
            }elseif(!empty($request['subject_class_id']) && is_array($request['subject_class_id'])){
                (new Teacher_Class_Subject())->addUpdateTeacherClassSubject($id, $request['school_id'], $request['subject_class_id']); 
            }
            (new Attachments_Table())->insertAttechment_new($request, $id, $objUser->getTable());
            return compact("id");
        }
    }

    public function string_encrypt($text){
        $output         = false;
        $secret_key     = "ENUKEPASSCODE";
        $encrypt_method = "AES-256-CBC";
        $secret_iv      = 'g@@di';
        // hash
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_encrypt($text, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }

    public function string_decrypt($text){
        $output         = false;
        $secret_key     = "ENUKEPASSCODE";
        $encrypt_method = "AES-256-CBC";
        $secret_iv      = 'g@@di';
        // hash
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_decrypt(base64_decode($text), $encrypt_method, $key, 0, $iv);
        return $output;
    }

    

    public function logoutUser($request,$userType){
        
        $objUser =$this->getUserObject($userType);
        $user_id=User::getId($request);
        $data = $objUser->find($user_id);
        if($data)
        {
            $data->token = null;
            $data->device_token = null;
            $data->save();
        }
        return $data ;
    }

}
