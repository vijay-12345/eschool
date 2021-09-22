<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz_Result extends Model
{
    protected $table = 'quiz_result';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','user_id','school_id','role','quiz_table_id','result','total_question','attempt_count','non_attempt_count','correct_count','wrong_count','time_elapsed','attempted_date','result_json'];
	
	protected $hidden = [];	

	public function inserUpdateData($request){
        
            if(!empty($request->id)){
                $id= $request->id;
                $insertData = self::find($id);
            }

            foreach ($this->fillable as $key => $value) {
                if(!empty($request[$value]))
                {
                    $insertData[$value]=$request[$value];
                    
                }
            }
            $insertData['result_json'] = json_encode($request->all());
            
           if(!empty($insertData)){
                if(!empty($insertData["id"])){
                    self::where(['id'=>$insertData["id"]])->update($insertData->toArray());
                }else{
                    // $row = self::where($insertData)->get()->toArray();
                    $row = self::where('user_id',$insertData['user_id'])
                                ->where('quiz_table_id',$insertData['quiz_table_id'])
                                ->where('school_id',$insertData['school_id'])
                                ->get()->toArray();
                    if(empty($row))
                        $id = self::create($insertData )->id;
                    else
                    {	
                        //self::where(['id'=>$row[0]['id']])->update(['status'=>1]);
                        $id = $row[0]['id'];
                    }

                }
                //(new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
                return compact("id");
            }
    }

    public function isQuizResult($request,$quiz_table_id){
            $data = self::where('quiz_table_id',$quiz_table_id)
                        ->where('user_id',$request->user_id)
                        ->get()->toArray();         
            if(!empty($data))
            {
                return "true";
            }
            else
                return "false";
    }

    public function getQuizResultForQuiz_Table($request,$quiz_table_id){
        $data = self::where('quiz_table_id',$quiz_table_id)
                    ->where('user_id',$request->user_id)
                    ->first();
        $value =json_decode($data['result_json']);
        return $value;
    }

    public function getQuizResult($request){
        if(!empty($request->quiz_table_id))
        {
            $data = self::where('quiz_table_id',$request->quiz_table_id)
                        ->where('user_id',$request->user_id)
                        ->get()->toArray();
            foreach($data as $key => $value) {
                $data[$key]['result_json'] = json_decode($value['result_json']);        
            }            
            return $data;
        }
    }

}
