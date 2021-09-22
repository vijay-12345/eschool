<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\School, App\Admin,App\Subject_Master;
use App\Student, App\Teacher ,App\Teacher_Class_Subject;
use App\Forum, App\RemainNotifactions, App\Assignment_Submittted;
use DB;
class Notifaction extends Model
{
	use Notifiable;
  protected $table = 'notifactions';
	protected $primaryKey = 'id';
	public $timestamps = false;
	public $fillable = ['id','school_id','role','table_type','reference_id','action','status'];
	protected $hidden = [];
    
    public function getTable(){
    	return $this->table;
    }


  function setNewNotificatin($request,$table_type,$reference_id,$action){
	 $insertDate=[
	 	'school_id'=>$request->school_id,
	 	'role'=>$request->role,
	 	'table_type'=>$table_type,
	 	'reference_id'=>$reference_id,
	 	'action'=>$action
	 	];
  	 $id = self::create($insertDate);
  }  

  
 /* SEND NOTIFICATION  */ 
/*    public function callCurlToSendNotification()
    {    
        $url = url('/');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url."/api/v2/process-notification",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }  
*/
 function checkNotification(){
    
    $date= date('Y-m-d H:i:s',strtotime('-5 days',strtotime(date("Y-m-d H:i:s"))));
    $remaining= RemainNotifactions::where('created_at',">",$date)->get();
    if(!empty($remaining)){
      foreach ($remaining as $value) {
          $notify = json_decode($value->notification_details,true);
          $returnData =   $this->sendNotification($notify[0], $notify[1],$notify[2],$notify[3],$notify[4],$notify[5]);
          $returnData = json_decode($returnData);
          if(!$returnData->failure){
                $value->delete();
          }
      }
    }

  	$notifications = self::where('status','0')->get();
  	foreach($notifications as $notification){

   		if($this->makeNotification($notification));
  		{
  			$notification->status='1';
  			$notification->save();
  		}
  	}	
  	return 'All Notification Send';
  }


  function getStudentList($data){

  		$obj = Student::where('device_token', "<>" ,null);

  		if(!empty($data->school_id)){
	  		$obj->where('student.school_id',$data->school_id);
  		}
      if(!empty($data->class_section_id)){
        $obj->where('student.class_section_id',$data->class_section_id);
      }
  		if(!empty($data->teacher_class_subject_id)){
  			$obj->Join('class_section','class_section.id','=','student.class_section_id')
                ->Join('subject_class','subject_class.class_section_id','=','class_section.id')
           		->Join('teacher_class_subject','teacher_class_subject.subject_class_id','=','subject_class.id')
           		->where('teacher_class_subject.id',$data->teacher_class_subject_id);
      }  		
      return $obj->get();

  }



  function getTeacherList($data){
  

  		$obj=Teacher::where('device_token', "<>" ,null);
		if(!empty($data->school_id)){
			$obj->where('teacher.school_id',$data->school_id);
		}
		 if(!empty($data->teacher_class_subject_id)){
			$obj->Join('teacher_class_subject','teacher_class_subject.teacher_id','=','teacher.id')
			->where('teacher_class_subject.id',$data->teacher_class_subject_id);
		}
		return $obj->get();
  }



  function makeNotification($notification){
  	
  	$Authorizationkey="AAAAYKRlTJA:APA91bGAddU0C6qYaM2FQT_UBwe43AstINrpw0U-kFQSLlbH0KlIuZaWSu2gzeGJifAP3fP9MlXH8M2JHP1C-78pRF7Cz3gte-JSgADT6vbV2Gyr4VvTElZtfRT60fHTKqTwfPF3QCBU";
  	$data=[];
    $AllTotification=[];

  	if($notification->table_type=='assignment' && in_array($notification->action, ['add','update','beforHour'])){
    		$type='assignment';
    		if($notification->action=='add'){
    			$title = "New assignment";
  			$message="Hi <name of student> you have new <subject> assignment due date of this assignment submition is <due date time>";
    		}
    		if($notification->action=='update'){
    			$title = "Assignment Changes";
  			$message="Hi <name of student> assignment of <subject> is updated with due date of this assignment submition is <due date time>";
    		}
        if($notification->action=='beforHour'){
          $title = "Assignment Submission Date Expired in less than 1 Hour";
        $message="Hi <name of student> assignment of <subject> is  due date Expired in less than 1 Hour of this assignment submition is <due date time>";
        }

    		$tabledata = DB::table($notification->table_type)
  	   		->Join('teacher_class_subject','teacher_class_subject.id','=','assignment.teacher_class_subject_id')
  	        ->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
  			->Join('subject_master','subject_master.id','=','subject_class.subject_id')
    			->where('assignment.id',$notification->reference_id)->first();
    		if(!empty($tabledata)){
  	  		$studentsList= $this->getStudentList($tabledata);

        if($notification->action=='beforHour')
        {
            $assignment_id = $notification->reference_id;
            $user_submitted = Assignment_Submittted::select('student_id')->where('assignment_id',$assignment_id)->pluck('student_id')->toArray();
            $obj = Student::where('device_token', "<>" ,null);
            if(!empty($tabledata->school_id)){
              $obj->where('student.school_id',$tabledata->school_id);
            }
            if(!empty($tabledata->class_section_id)){
              $obj->where('student.class_section_id',$tabledata->class_section_id);
            }

            $obj = $obj->whereNotIn('id',$user_submitted);     
            $studentsList = $obj->get();
            
        }  

  			foreach ($studentsList as $key => $student) {
  				  $bodymess=str_replace("<name of student>",$student->name ,$message);
  	  			  $bodymess=str_replace("<subject>",$tabledata->subject_name , $bodymess);
  				  $bodymess=str_replace("<due date time>", date("d M Y H:i:s", strtotime($tabledata->due_date)) , $bodymess);
            $AllTotification[]=[$Authorizationkey, $student->device_token,$title,$bodymess,$tabledata,$type];
  			   }
    		}
  	}
    elseif($notification->table_type=='study_material' && in_array($notification->action, ['add','update'])){
        $type='study_material';
        if($notification->action=='add'){
          $title = "New Study_Material";
        $message="Hi <name of student> you have new study_material of <subject> with url link is <content>";
        }
        if($notification->action=='update'){
          $title = "Study_Material Changes";
        $message="Hi <name of student> study material of <subject> is updated with url link is <content>";
        }
        

        $tabledata = DB::table($notification->table_type)
          ->Join('teacher_class_subject','teacher_class_subject.id','=','study_material.teacher_class_subject_id')
            ->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
        ->Join('subject_master','subject_master.id','=','subject_class.subject_id')
          ->where('study_material.id',$notification->reference_id)->first();
        if(!empty($tabledata)){
          $studentsList= $this->getStudentList($tabledata);


        foreach ($studentsList as $key => $student) {
            $bodymess=str_replace("<name of student>",$student->name ,$message);
              $bodymess=str_replace("<subject>",$tabledata->subject_name , $bodymess);
              $bodymess=str_replace("<content>",$tabledata->content , $bodymess);
            $AllTotification[]=[$Authorizationkey, $student->device_token,$title,$bodymess,$tabledata,$type];
           }
        }
    }
    elseif($notification->table_type=='session_table' && in_array($notification->action, ['add','update'])){
    		$type='session';
      	if($notification->action=='add'){
    			$title = "New Session";
  			$message="Hi <name of student> you have new Session of <subject> on <date> at <time>";
    		}
    		if($notification->action=='update'){
    			$title = "Session Changes";
  			$message="Hi <name of student> There is an update in your session of <subject> now it will be on <date> at <time>";
    		}

    		$tabledata = DB::table($notification->table_type)
  	   		->Join('teacher_class_subject','teacher_class_subject.id','=','session_table.teacher_class_subject_id')
  	        ->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
  			->Join('subject_master','subject_master.id','=','subject_class.subject_id')
    			->where('session_table.id',$notification->reference_id)->first();

    		if(!empty($tabledata)){

  	  		$studentsList= $this->getStudentList($tabledata);
  	  		foreach ($studentsList as $key => $student) {
  				  $bodymess=str_replace("<name of student>",$student->name ,$message);
  	  			  $bodymess=str_replace("<subject>",$tabledata->subject_name , $bodymess);
  				  $bodymess=str_replace("<date>", date("d M Y", strtotime($tabledata->date)) , $bodymess);
  				  $bodymess=str_replace("<time>", date("H:i:s", strtotime($tabledata->start_time)) , $bodymess);
  				  $AllTotification[]=[$Authorizationkey, $student->device_token,$title,$bodymess,$tabledata,$type];
  			   }
    		}	
  	}elseif($notification->table_type=='forum_table' && in_array($notification->action, ['reply','update'])){
  		
      $type='forum';
  		$tabledata = DB::table($notification->table_type)
   		->Join('teacher_class_subject','teacher_class_subject.id','=','forum_table.teacher_class_subject_id')
      ->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
  		->Join('subject_master','subject_master.id','=','subject_class.subject_id')
  		->Join('class_section','class_section.id','=','subject_class.class_section_id')
  		->where('forum_table.id',$notification->reference_id)->first();

  		if($notification->action=='reply'){

  			$title = "Forum Reply";
			   $message="Hi <name of receiver> there is a new forum post from <name of creater> on <subject> for <class-session-subject>"; 
  		}
      	// 	if($notification->action=='update'){
      	// 		$title = "Forum Reply Changes";
    			// $message="Hi <name of student/ teacher name > there is a new forum post from <name of student/ teacher name > on <subject for student/class-session-subject for teacher>"; 
      	// 	}

  		if(!empty($tabledata)){
  			$replyBy=[];
  			if($tabledata->who_replyed=='teacher'){
  				$replyBy = Teacher::find($tabledata->reply_from);
	  			$userList = $this->getStudentList($tabledata);
  			}elseif($tabledata->who_replyed=='student'){
				$replyBy = Student::find($tabledata->reply_from);
  				$userList = $this->getTeacherList($tabledata);
  			}

  			if(!empty($userList)){
		  		foreach ($userList as $key => $user) {
					  $bodymess=str_replace("<name of receiver>",$user->name ,$message);
		  			  $bodymess=str_replace("<name of creater>", $replyBy->name , $bodymess);
					  $bodymess=str_replace("<subject>",$tabledata->subject_name , $bodymess);
					  $bodymess=str_replace("<class-session-subject>", $tabledata->class_name." ".$tabledata->section_name , $bodymess);
					 $AllTotification[]=[$Authorizationkey, $user->device_token,$title,$bodymess,$tabledata,$type];
				  }
  			}
  		}	
  	}
  	elseif($notification->table_type=='assignment_submittted' && in_array($notification->action, ['add','update'])){
  		
  		$type='assignment_submittted';
    	if($notification->action=='add'){
  			$title = "Assignment Submit";
			  $message="Hi <teacher name> , <student name> submit his assignment of <subject> for <class-session> please check";
  		}
  		if($notification->action=='update'){
  			$title = "Assignment Submit Changes";
			  $message="Hi <teacher name> , <student name> has update his Submitted assignment ofof <subject> for <class-session>  please check";
  		}

  		$tabledata = DB::table($notification->table_type)
			->Join('assignment','assignment.id','=','assignment_submittted.assignment_id')
      ->Join('teacher_class_subject','teacher_class_subject.id','=','assignment.teacher_class_subject_id')
      ->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
      ->Join('class_section','class_section.id','=','subject_class.class_section_id')
      ->Join('subject_master','subject_master.id','=','subject_class.subject_id')
      ->where('assignment_submittted.id',$notification->reference_id)->first();
  		
  		if(!empty($tabledata)){
	  		$replyBy = Student::find($tabledata->student_id);
  			$userList = $this->getTeacherList($tabledata);

	  		foreach ($userList as $key => $user) {
				    $bodymess=str_replace("<teacher name>",$user->name ,$message);
	  			  $bodymess=str_replace("<student name>",$replyBy->name , $bodymess);
	  			  $bodymess=str_replace("<subject>",$tabledata->subject_name , $bodymess);
	  			  $bodymess=str_replace("<class-session>",$tabledata->class_name." ".$tabledata->section_name , $bodymess);
				    $AllTotification[]=[$Authorizationkey, $user->device_token,$title,$bodymess,$tabledata,$type];
			   }
  		}	
  	}
  	elseif($notification->table_type=='assignment_submittted' && in_array($notification->action, ['add','update'])){
  		
  		$type='assignment_submittted';
    	if($notification->action=='add'){
  			$title = "Assignment Submit";
			$message="Hi <teacher name> , <student name> submit his assignment of <subject> for <class-session> please check";
  		}
  		if($notification->action=='update'){
  			$title = "Assignment Submit Changes";
			$message="Hi <teacher name> , <student name> has update his Submitted assignment ofof <subject> for <class-session>  please check";
  		}
  		
  		$tabledata = DB::table($notification->table_type)
      ->Join('assignment','assignment.id','=','assignment_submittted.assignment_id')
      ->Join('teacher_class_subject','teacher_class_subject.id','=','assignment.teacher_class_subject_id')
      ->Join('subject_class','subject_class.id','=','teacher_class_subject.subject_class_id')
      ->Join('class_section','class_section.id','=','subject_class.class_section_id')
      ->Join('subject_master','subject_master.id','=','subject_class.subject_id')
      ->where('assignment_submittted.id',$notification->reference_id)->first();
  		
  		if(!empty($tabledata)){
	  		$replyBy = Student::find($tabledata->student_id);
  			$userList = $this->getTeacherList($tabledata);
	  		foreach ($userList as $key => $user) {
  				  $bodymess=str_replace("<teacher name>",$user->name ,$message);
    			  $bodymess=str_replace("<student name>",$replyBy->name , $bodymess);
    			  $bodymess=str_replace("<subject>",$tabledata->subject_name , $bodymess);
    			  $bodymess=str_replace("<class-session>",$tabledata->class_name." ".$tabledata->section_name , $bodymess);
  				  $AllTotification[]=[$Authorizationkey, $user->device_token,$title,$bodymess,$tabledata,$type];
			   }
  		}	
  	}elseif($notification->table_type=='time_table' && in_array($notification->action, ['add','update'])){
      $type='time_table';
      if($notification->action=='add'){
        $title = "Add New Time Table";
        $message="Hi <name of student> new time table added please have a look on time table section"; 
      }
      if($notification->action=='update'){
        $title = "Time Table Update";
        $message="Hi <name of student> new time table added please have a look on time table section";
      }
      $tabledata = DB::table($notification->table_type)
        ->Join('class_section','class_section.id','=','time_table.class_section_id')
        ->where('time_table.id',$notification->reference_id)->first();
      if(!empty($tabledata)){
        $userList = $this->getStudentList($tabledata);
        foreach ($userList as $key => $user) {
            $bodymess=str_replace("<name of student>",$user->name ,$message);
            $AllTotification[]=[$Authorizationkey, $user->device_token,$title,$bodymess,$tabledata,$type];
         }
      } 
    }
    elseif($notification->table_type=='notice_board' && in_array($notification->action, ['add','update'])){
      $type='notice_board';
      if($notification->action=='add'){
        $title = "Add New Notice";
        $message="Hi <name of user> Please check the latest Notice <Notice time> for details check Notice section in app"; 
      }
      if($notification->action=='update'){
        $title = "Notice Update";
        $message="Hi <name of user> Please check the latest Notice <Notice time> for details check Notice section in app";
      }
      $tabledata = DB::table($notification->table_type)
        ->where('notice_board.id',$notification->reference_id)->first();
     
      if(!empty($tabledata)){
        if(in_array($tabledata->type, ['all','student'])){
          $userList = $this->getStudentList($tabledata);
          foreach ($userList as $key => $user) {
              $bodymess=str_replace("<name of user>",$user->name ,$message);
              $bodymess=str_replace("<Notice time>",date("d M Y H:i", strtotime($tabledata->date)) ,$bodymess);
              $AllTotification[]=[$Authorizationkey, $user->device_token,$title,$bodymess,$tabledata,$type];
           }
        }
        if(in_array($tabledata->type, ['all','teacher'])){
          $userList = $this->getTeacherList($tabledata);
          foreach ($userList as $key => $user) {
              $bodymess=str_replace("<name of user>",$user->name ,$message);
              $bodymess=str_replace("<Notice time>",date("d M Y H:i", strtotime($tabledata->date)) ,$bodymess);
              $AllTotification[]=[$Authorizationkey, $user->device_token,$title,$bodymess,$tabledata,$type];
           }
        }
      } 
    }

    foreach ($AllTotification as  $notify) {
       $returnData =   $this->sendNotification($notify[0], $notify[1],$notify[2],$notify[3],$notify[4],$notify[5]);
       $returnData = json_decode($returnData);
       if($returnData->failure){
          $notification_details=json_encode($notify);
          RemainNotifactions::create(compact('notification_details'));
       } 
    }
  } 


  function sendNotification($Authorizationkey, $device_token,$title,$bodymess,$data,$type){

    // $device_token="f2MSoVF9QEqzm2pn4JGGXv:APA91bFC_hppzrq9MRGkVU3RbNF6FVXUrrGNDmrhhPJt5P__l8Hxk8NtcwtSZTLfyLXW6lQcSB6Mg3CuyAvWJ13bdykCrdcEChTz-mTFGNodNVquqOmCmT9FEjFQv7eHaJcd2iueheYs";

    // $Authorizationkey='AAAA4w755t8:APA91bFdeaN3poBnxdnk0FjAr3tk07C0f5e9yf9Y6uudj1mMqEYZCdV9Yf67YdHvkJpHXmY6XmvcFpMxra4N2gb1a4ogZ7twt16cl9LNT1CklCA0XJp82epAAf2GabRMv3bhtPw3q0RY';


    // ////ENUKE
    // $Authorizationkey="AAAAYKRlTJA:APA91bGAddU0C6qYaM2FQT_UBwe43AstINrpw0U-kFQSLlbH0KlIuZaWSu2gzeGJifAP3fP9MlXH8M2JHP1C-78pRF7Cz3gte-JSgADT6vbV2Gyr4VvTElZtfRT60fHTKqTwfPF3QCBU";

    // $title="New Order";
    // $bodymess="You have received a new order num:4567";
    // $data= ['order_id'=>"4567",'quntity'=>"6",'discription'=>"disign product"];

    
      $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
      $fields = array(
          'to' => $device_token,
          'data' => array('title'=>$title ,'body' => $bodymess,'tag' => $data,
                          "icon"=>"",
                          "badge"=>"1",
                          "sound"=>"default",
                          "type"=>$type
                      ),
         'notification' => array('title'=>$title ,'body' => $bodymess,'tag' => $data),
      );
      $headers = array(
          'Authorization:key=' . $Authorizationkey,
          'Content-Type:application/json'
      );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm); 
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); 
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
  }



function PushNotification($storeid,$data)
{
     if($storeid == '') 
        return true;      
    //for curl excute
    $User=User::where(['role_id'=>3,'store_id'=>$storeid])->get();

    $notificationsurl=url('sendnotifications').'?data='.json_encode($data)."&store=".$storeid;     
    //pr($notificationsurl);
    //Notifications::set_for_notification($notificationsurl);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $notificationsurl); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS,2000);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); 
    $result = curl_exec($ch);
    curl_close($ch);  
      //////fordirect
      /*    $User= User::where(['role_id'=>3,'store_id'=>$storeid])->get();
          foreach($User as $user){
            $msg='Please open your application to get update';
            androidPushNotificationtouseronly(json_encode($data), $user, $msg);
          }
           */
  }


  function iphonePushNotification($data, $user, $msg) 
  { 
  //prd($user);
          $deviceToken  = $user->device_token;
          $ctx    = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'local_cert', "pem/Certificates".$user->store_id.".pem");
    //$passphrase   = '123@456';
    //stream_context_set_option($ctx, 'ssl', 'passphrase',  $passphrase);
    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
    if (!$fp) {
     return false;
    }
    $body['aps'] = array('alert' => $msg,'sound' => 'default');
    $body['Message']=$data;
    $payload = json_encode($body,1);
    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
    $result = fwrite($fp, $msg, strlen($msg));
    fclose($fp);
    return true;
  }


}