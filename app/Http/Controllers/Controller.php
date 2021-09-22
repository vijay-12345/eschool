<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function apiResponse($data=[], $error = false)
	{
		$defaultResponseArr = [];
		
		switch($error)
		{
			case true: 
			case '1':
			case 'true':
				$defaultResponseArr['success'] = false;				
				break;
			default:
				$defaultResponseArr['success'] = true;
		}

		return \Response::json( array_merge($defaultResponseArr, $data) );
	}

	public function errorToMeassage($error){
		$message=[];
		if(!empty($error)){
			$error= $error->toArray();
			foreach($error as $key => $value) {
				foreach ($value as  $val) {
					$message[]=$val;
				}
			}
		 }
		 return implode(",",$message);
	}

}
