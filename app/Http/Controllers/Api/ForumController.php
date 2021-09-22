<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Forum, App\Teacher_Class_Subject;


class ForumController extends \App\Http\Controllers\Controller
{
	public function deleteForumPost(Request $request)
	{
		try {
		    $rules=[
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
		    
		    $objForum=new Forum();	
			// $id= $objForum->;		    

		    if($id)
		    	return $this->apiResponse(['message'=>'Forum deleted','id'=>$id]);
		    else
		    	return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
		    
		} catch(\Exception $e) {
			return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
		}
	}

	public function addForumPost(Request $request)
	{
		try {
		    $rules=[
		            'school_id' => 'required|numeric',
		            'role' => 'required|in:student,teacher,user',
		            "message_content"=>'required'
		        ];
	        if(!empty($request->role) && $request->role == 'teacher'){
				$rules['teacher_class_subject_id']='required';
	        }else{
	        	$rules['class_section_id']='required';
	        	$rules['subject_ids']='required';
	        }
		    $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
       
		    $objForum=new Forum();	
			$data= $objForum->addForum($request);		    

		    if($data)
		    	return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
		    else
		    	return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
		    
		} catch(\Exception $e) {
			return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
		}
	}

	public function addForumPostWebPanel(Request $request)
	{
		try {
		    $rules=[
		            'school_id' => 'required|numeric',
		            'role' => 'required|in:student,teacher,user',
		            "message_content"=>'required'
		        ];
	        if(!empty($request->role) && $request->role == 'teacher'){
				$rules['teacher_class_subject_id']='required';
	        }else{
	        	$rules['class_section_id']='required';
	        	$rules['subject_ids']='required';
	        }
		    $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
       
		    $objForum=new Forum();	
			$data= $objForum->addForumWebPanel($request);		    

		    if($data)
		    	return $this->apiResponse(['message'=>'Response Successful','data'=>$data]);
		    else
		    	return $this->apiResponse(['error'=>'Not Found data with given condition'],true);
		    
		} catch(\Exception $e) {
			return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
		}
	}



	public function getForumPost(Request $request)
	{
		try {
			$rules=[
		            'school_id' => 'required|numeric',
		            'role' => 'required|in:student,teacher,user'
		        ];
	        if(!empty($request->role) && $request->role=='teacher'){
				$rules['teacher_class_subject_id']='required';
	        }else{
	        	$rules['class_section_id']='required';
	        	$rules['subject_ids']='required';
	        }
		    $validatedData = Validator::make( $request->all(),$rules);

            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
       
		    $objForum=new Forum();	
			$data= $objForum->getForumList($request);		    

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


	public function getForumLast(Request $request)
	{
		try {
			$rules=[
		            'school_id' => 'required|numeric',
		            'role' => 'required|in:student,teacher,user'
		        ];
	        if(!empty($request->role) && $request->role=='teacher'){
				$rules['teacher_class_subject_id']='required';
	        }else{
	        	$rules['class_section_id']='required';
	        	$rules['subject_ids']='required';
	        }
		    $validatedData = Validator::make( $request->all(),$rules);

            if ($validatedData->fails()){          
                 return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
       
		    $objForum=new Forum();	
			$data= $objForum->getForumList($request,1);		    

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

	public function forumForm(Request $request,$teacher_class_subject_id){
        $schools = [];
        $teacher_class_subject['id'] = $teacher_class_subject_id;
        


        $user_detail = session('user_details');

        $request['teacher_id'] = $user_detail['id'];
        $request['school_id'] = $user_detail['school_id'];
        // $request=$request->all();
        
        $objTeacher_Class_Subject = new Teacher_Class_Subject();
        $teacher_class_subjects = $objTeacher_Class_Subject->getClassSectionSubject($request['teacher_id'],$request['school_id']);
       
        return view('BackEnd/forumManagement.addinforum',
                    compact("teacher_class_subject","request"));
    }

}