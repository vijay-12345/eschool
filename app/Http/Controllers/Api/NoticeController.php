<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Notice_Board;
use App\School;


class NoticeController extends \App\Http\Controllers\Controller
{
    public function deleteNotice(Request $request)
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
            
            $objNotice_Board = new Notice_Board();
            // $id= $objNotice_Board->;

            if($id)
                return $this->apiResponse(['message'=>'Notice deleted','id'=>$id]);
            else
                return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function updateNotice(Request $request)
    {
        try {
            $rules = [
                'id'=>'required|numeric',
                'school_id' => 'required|numeric',
                'role' => 'required|in:student,teacher,user,admin,school',
                'type'=>'required|in:teacher,student,all'
            ];
            
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                     return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }      
            }
            $objNotice_Board = new Notice_Board();
            $id= $objNotice_Board->addUpdateNotice($request);
            return $this->apiResponse(['message'=>'Notice Updated','id'=>$id]);
            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

    public function addNotice(Request $request)
    {
        try {
            $rules = [
                'school_id' => 'required|numeric',
                'role' => 'required|in:student,teacher,user,school,admin',
                'title'=> 'required|max:255',
                'message'=>'required',
                'type'=>'required|in:teacher,student,all'
            ];
            
            if($request->role != 'super_admin')
            {
                $validatedData = Validator::make( $request->all(),$rules);
                if ($validatedData->fails()){          
                     return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
                }      
            }
            $objNotice_Board = new Notice_Board();
            $id= $objNotice_Board->addUpdateNotice($request);

            return $this->apiResponse(['message'=>'Notice added','id'=>$id]);

            
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

	public function notice(Request $request)
	{
		try {
		    $rules = [
                'school_id' => 'required|numeric',
                'role' => 'required|in:student,teacher,user,admin,school'
            ];
            $validatedData = Validator::make( $request->all(),$rules);
            if($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }

            $objNotice_Board = new Notice_Board();
            $data= $objNotice_Board->getNotice($request);

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

    public function noticeForm(Request $request){
                                  
        $type =['all','teacher','student'];
        $notice=[];
        $schools = [];
        if(Auth::guard('admin')->check())
        {
            $objSchool = new School();
            $schools = $objSchool->getAlldata();            
        }
        else if(Auth::guard('school')->check())
        {
            $schools = School::where('id',$request->user_id)->get();
        }

        if(!empty($request->id)){
          
           $notice = (new Notice_Board())->with('file_url')->find($request->id)->toArray();
        }
        $request=$request->all();
       
        return view('BackEnd/noticeManagement.addeditnotice',compact("schools","type","notice","request"));  
    }
	

}