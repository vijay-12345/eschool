<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz_Detail extends Model
{
    protected $table = 'quiz_detail';
	
	protected $primaryKey = 'id';
	  
	public $timestamps = false;

	public $fillable = ['id','question_number','question','option_A','option_B','option_C','option_D','correct_answer','explaination','quiz_table_id'];
	
	protected $hidden = [];	

	public function inserUpdateData($request){
            $questionNumber= $this->lastQuestionId($request['quiz_table_id']);
            if(empty($request['id'])){
                $questionNumber= $questionNumber+1;
                $request['question_number']=$questionNumber;
            }
            
        
            if(!empty($request->id)){
                $id= $request->id;
                $insertData = self::find($id);
            }

            foreach ($this->fillable as $key => $value) {
                if(!empty($request[$value]))
                    $insertData[$value]=$request[$value];
            }
            $correctAnswer= $insertData['option_'.$insertData['correct_answer']];
            $insertData['correct_answer']=$correctAnswer;
           if(!empty($insertData)){
                if(!empty($insertData["id"])){
                    self::where(['id'=>$insertData["id"]])->update($insertData->toArray());
                }else{
                    $id = self::create($insertData )->id;

                }
                //(new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
                return compact("id");
            }
    }

    public function getQuiz($request){
    	if(!empty($request->quiz_table_id))
    	{
            $objQuiz_Result = Quiz_Result::where('user_id',$request->user_id)
                                        ->where('quiz_table_id',$request->quiz_table_id)
                                        ->get()->toArray();
            if(empty($objQuiz_Result)){
                $data = self::where('quiz_table_id',$request->quiz_table_id)->get()->toArray();
                return $data;
            }
            else
            {
                $data['message'] = 'User already give test of this Quiz';
                return $data;
            }
    		           
    	}
    }
    
    public function lastQuestionId($quiz_table_id)
    {
        $lastQuestionNumber = self::where('quiz_table_id',$quiz_table_id)
                                        ->select('question_number')
                                        ->orderByDesc('question_number')
                                        ->first();
        if(!empty($lastQuestionNumber))                                
            return $lastQuestionNumber['question_number'];
        else
            return 0;
    }
}
