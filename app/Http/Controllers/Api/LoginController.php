<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use Hash;
use App\Student, App\Teacher, App\User, App\Subject_Class, App\Class_Section, App\Teacher_Class_Subject;


class LoginController extends \App\Http\Controllers\Controller
{
	public function changePassword(Request $request) 
    {
        try
        {
            $rules = [
                'user_id' => 'required',
                'role' => 'required|in:student,teacher,user',
                'old_password' => 'required|min:3',
                'new_password' => 'required|min:3'
                ]; 
            $validatedData = Validator::make( $request->all(),$rules);
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            $objUser = new User();
            $CheckUserAuth = $objUser->CheckUserAuth($request);
            if($CheckUserAuth == 'true')
            {
            	$obj = $objUser->getUserObject($request->role);
            	$data = $obj->find($request->user_id);

            	$hashedPassword = $data->password;
            	if ( Hash::check($request->old_password, $hashedPassword) ){
            		$data->password = Hash::make($request->new_password);
	            	$data->pass_code = $objUser->string_encrypt($request->new_password);
	            	$data->save();
            		return $this->apiResponse(['message'=>'Successful Password is Changed']);
            	}
            	else
            		return $this->apiResponse(['message'=>'Password Not Match with Old Password'],true);
            }
            else
            {
            	return $this->apiResponse(['message'=>'Authentication is not successful'],true);
            }
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }

	public function logout(Request $request){
		try{
			
		    $rules = [
	    		'user_id' => 'required|numeric',
	            'role' => 'required|in:student,teacher,admin,user,school',
	            'school_id' => 'required|numeric'
            ];
            $validatedData = Validator::make( $request->all(),$rules);
            
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
            
            $objUser= new  User();
            $data = $objUser->logoutUser($request, $request->role);
        	if($data)
        	{   
		 		return $this->apiResponse(['message' => 'successfull logout']);
        	}
        	else
        	{
        		return $this->apiResponse(['error' => 'Logout not successful'],true);	
        	}
            
		}
		catch(\Exception $e) {
			return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
		}
	}



	public function login(Request $request)
	{
		try {
		    
		    $rules = [
	            'login_id' => 'required',
	            'password' => 'required|min:3',
	            'role' => 'required|in:student,teacher,admin,user,school',
            ];
            if(!empty($request->role) && !in_array($request->role, ['admin','school'])){
				$rules['school_id'] ='required|numeric';
            }

            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }

            $objUser= new  User();
            $data = $objUser->getUserLogin($request, $request->role);

            if($data){
            	return $this->apiResponse(['message' => 'Successfully login','data'=>$data]);
            }
            else
                return $this->apiResponse(['error'=>'Invalid login credentials'],true);
		
		} catch(\Exception $e) {
			return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
		}
	}


	public function signup(Request $request)
	{
		try {

			$objUser= new  User();
			$rules= $objUser->getsignUpRules($request);
		    $customMessages = [
		        'regex' => 'Password atleast have one digit ,one capital one small character.'
		    ];
		     $validatedData = Validator::make( $request->all(),$rules,$customMessages);
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
          
            $objUser= new  User();
            $data = $objUser->createUpdateUser($request, $request->role);
            if(!empty($data['id'])){
            	$data['message']='Successfully Created';
            	return $this->apiResponse($data);
            }
            else
                return $this->apiResponse(['error'=>$data, 'message'=>$data],true);

		} catch(\Exception $e) {
			return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
		}
	}

	public function useredit(Request $request)
	{
		try {
			$objUser= new  User();
			$rules= $objUser->getsignUpRules($request);
			$rules['id']="required|numeric";

		    $customMessages = [
		        'regex' => 'Password atleast have one digit ,one capital one small character.'
		    ];
		    $validatedData = Validator::make( $request->all(),$rules);
    
    	
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }
 
            $objUser= new  User();
            $data = $objUser->createUpdateUser($request, $request->role);
            if(!empty($data['id'])){
            	$data['message']='Successfully Updated';
            	return $this->apiResponse($data); 	
            }
            else
                return $this->apiResponse(['error'=>$data],true);

		} catch(\Exception $e) {
			return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
		}
    }

    public function loginDetails(Request $request)
    {
        try {
            
            $rules = [
                'role' => 'required|in:student,teacher,user',
            ];

            $validatedData = Validator::make( $request->all(),$rules);
           
            if ($validatedData->fails()){          
                return $this->apiResponse(['error' => $validatedData->errors() ,'message'=> $this->errorToMeassage($validatedData->errors()) ], true);
            }

            $objUser= new  User();
            $data = $objUser->getUserAllDetails($request->user_id, $request->role);

            if($data){
                return $this->apiResponse(['message' => 'Successful','data'=>$data]);
            }
            else
                return $this->apiResponse(['error'=>$data],true);
        
        } catch(\Exception $e) {
            return $this->apiResponse(['message'=>'Request not successful','error'=>$e->getMessage()],true);
        }
    }


}
