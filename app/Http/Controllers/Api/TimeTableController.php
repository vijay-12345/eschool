<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use DB;
use App\Time_Table, App\School,App\Class_Section;

class TimeTableController extends \App\Http\Controllers\Controller
{
    public function deleteTimeTable(Request $request)
    {
        try {
            $rules = [
                    'id'=>'required|numeric',
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school'
                ];
              
             if($request->role != 'super_admin')
             {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                    return $this->apiResponse(['error' => $validatedData->errors() ], true);
                }
             } 
            
            $objTime_Table= new  Time_Table();
            // $id= $objTime_Table->;

            if($id)
                return $this->apiResponse(['message'=>'Time Table deleted','id'=>$id]);
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addTimeTable(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'type'=>'required|in:teacher,student,class-section',
                    'class_section_id'=>"required",
                    "content"=>"required"
                ];
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                    return $this->apiResponse(['error' => $validatedData->errors() ], true);
                }
            }  
            
            $objTime_Table= new  Time_Table();
            $data= $objTime_Table->addUpdateTimeTable($request);
            return $this->apiResponse(['message'=>'Time_Table added','data'=>$data]);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function timeTable(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school'
                ];
              if(!empty($request->role) && $request->role == 'teacher'){
            //     $rules['teacher_class_subject_id']='required';
             }else{
                 $rules['class_section_id']='required';
            //     $rules['subject_ids']='required';
             }
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ], true);
            }
            $objTime_Table= new  Time_Table();
            $data= $objTime_Table->getTimeTable($request);
           
            if($data){
                if(!empty($request->page_limit)){
                    $data['message']='Response Successful';
                    return $this->apiResponse($data);
                }
                return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
            }
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }
    

    public function timeTableForm(Request $request){
     
        $class_section =(new Class_Section())->getClass_Section($request); 
        //$class_section= DB::table('class_section')->get();

        $time_table=[];
        $schools = [];
        if(Auth::guard('admin')->check())
        {
            $objSchool = new School();
            $schools = $objSchool->getAlldata();            
        }
        if(!empty($request->id)){
          
           $time_table = (new Time_Table())->with('file_url')->find($request->id)->toArray();
        }
      
        $request=$request->all();
        return view('BackEnd/timetableManagement.addeditform',compact("schools","class_section",'time_table','request'));  
    }

}
