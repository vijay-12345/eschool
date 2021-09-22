<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Quiz_Result;

class Quiz_Table extends Model
{
    protected $table = 'quiz_table';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','name','school_id','class_section_id','subject_id','total_time','start_time','expired_time','publish'];
	
	protected $hidden = [];	

    public function inserUpdateData($request){
        
            if(!empty($request->id)){
                $id= $request->id;
                $insertData = self::find($id);
            }

            foreach ($this->fillable as $key => $value) {
                if(!empty($request[$value]))
                    $insertData[$value]=$request[$value];
            }
            
           if(!empty($insertData)){
                if(!empty($insertData["id"])){
                    self::where(['id'=>$insertData["id"]])->update($insertData->toArray());
                }else{
                    $id = self::create($insertData )->id;
                    // $row = self::where('subject_id',$insertData['subject_id'])
                    //             ->where('class_section_id',$insertData['class_section_id'])
                    //             ->where('school_id',$insertData['school_id'])->get()->toArray();
                    // if(empty($row))
                    //     $id = self::create($insertData )->id;
                    // else
                    // {
                    //     //self::where(['id'=>$row[0]['id']])->update(['status'=>1]);
                    //     $id = $row[0]['id'];
                    // }

                }
                //(new Attachments_Table())->insertAttechment_new($request, $id, $this->getTable());
                return compact("id");
            }
    }

    public function getTableData($request){
        if(!empty($request->class_section_id && $request->subject_id))
        {
            $day_date = Carbon::now(); 
            $objQuiz_Result = new Quiz_Result();
            $data = self::select('quiz_table.*')
                        ->where('class_section_id',$request->class_section_id)
                        ->where('subject_id',$request->subject_id)
                        ->where('expired_time','>',$day_date)
                        ->where('publish','!=','0')
                        ->where('class_section.deleted',0)
                        ->where('subject_master.deleted',0)
                        ->join('class_section','class_section.id','quiz_table.class_section_id')
                        ->join('subject_master','subject_master.id','quiz_table.subject_id')
                        ->get()->toArray();
            foreach ($data as $key => $value) {
                $data[$key]['is_result_submitted'] = $objQuiz_Result->isQuizResult($request,$value['id']);
                $data[$key]['result_json'] = $objQuiz_Result->getQuizResultForQuiz_Table($request,$value['id']);
            }
            return $data;           
        }
    }

}
