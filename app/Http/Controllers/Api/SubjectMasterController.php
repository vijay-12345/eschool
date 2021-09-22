<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Subject_Class, App\Subject_Master;
use DB;


class SubjectMasterController extends \App\Http\Controllers\Controller
{
    public function deleteSubject(Request $request)
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
                     return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            
            $objSubject_Master = new Subject_Master();
            // $id = $objSubject_Master->;
            
            if($id)
                return $this->apiResponse(['message'=>'Subject deleted','id'=>$id]);
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);

            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function updateSubject(Request $request)
    {
        try {
            $rules = [
                    'id' =>'required|numeric',
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'subject_name' => 'required',

            ];
            if($request->role !='super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                     return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objSubject_Master = new Subject_Master();
            $id = $objSubject_Master->inserUpdateData($request);

            return $this->apiResponse(['message'=>'Subject updated','id'=>$id]);

            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addSubject(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'subject_name' => 'required',

            ];
            if($request->role !='super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                     return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }
            }
            $objSubject_Master = new Subject_Master();
            $id = $objSubject_Master->inserUpdateData($request);

            return $this->apiResponse(['message'=>'Subject added','id'=>$id]);

            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function getSubjectList(Request $request)
    {
        try {
            $rules = [
                    'school_id' => 'required|numeric',
                    'role'=>'required|in:student,teacher,admin,user,school',
                    'class_section_id' => 'required'
            ];
            
            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            $objSubject_Class = new Subject_Class();
            $data = $objSubject_Class->getSubject($request);
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
    public function subjectMasterForm(Request $request){
        $subject_master=[];
      
    
        if(!empty($request->id)){
          
           $subject_master =DB::table('subject_master')->where('id','=',$request->id)->first();
            // ->Join('attachments_table', 'attachments_table.reference_id', '=', 'time_table.id')
            // ->select('time_table.id','time_table.type','attachments_table.file_label','attachments_table.file_url','time_table.class_section_id')
            // ->where('time_table.id','=',$request->id)
            // ->get();
        }
      
        $request=$request->all();
        return view('BackEnd/subjectManagement.addeditmasterform',compact('subject_master','request'));  
    }

}