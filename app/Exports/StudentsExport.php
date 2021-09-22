<?php

namespace App\Exports;

use App\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Session_Table, App\Session_Attendance;
use DB;

class StudentsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($request)
    {
        $this->class_section_id=$request['class_section_id'];
        $this->subject_id=$request['subject_id'];
        $this->from=$request['from'];
        $this->to=$request['to'];
    }
    
    public function collection()
    {
        $request['class_section_id']=$this->class_section_id;
        $request['subject_id']=$this->subject_id;
        $request['from']=$this->from;
        $request['to']=$this->to;
            $totalsession = Session_Table::select(DB::raw("COUNT(session_table.id) as total_session"));
                $totalsession->join('teacher_class_subject', 'teacher_class_subject.id','session_table.teacher_class_subject_id');
                $totalsession->join('subject_class', 'subject_class.id','teacher_class_subject.subject_class_id');
                $totalsession->where('subject_class.class_section_id',$request['class_section_id']);
                $totalsession->where('teacher_class_subject.deleted',0);
                $totalsession->where('subject_class.deleted',0);
                if($request['subject_id']){
                $totalsession->where('subject_class.subject_id',$request['subject_id']);
                }
                $start_time=' 00:00:00';
                $end_time=' 23:59:59';
                if($request['from'] && $request['to']){
                    $totalsession->where('session_table.start_time','>=',$request['from'].$start_time);
                    $totalsession->where('session_table.end_time','<=',$request['to'].$end_time);
                }    
              $totalsession= $totalsession->first();
            $totalsession=$totalsession['total_session'];

            $all_students = Student::select('id','roll_no','name')->where('class_section_id',$request['class_section_id'])->where('deleted',0)->get();
            $i=1;
            $student_details=array();
            foreach($all_students as $student){
                $session_attend = Session_Attendance::select(DB::raw("COUNT(session_attendance.id) as total_attend"));
                    $session_attend->where('session_attendance.user_id',$student->id);
                    $session_attend->where('subject_class.class_section_id',$request['class_section_id']);
                    $session_attend->where('subject_class.deleted',0);
                    $session_attend->where('teacher_class_subject.deleted',0);
                    $session_attend->join('session_table','session_table.id','session_attendance.session_table_id');
                    $session_attend->join('teacher_class_subject','teacher_class_subject.id','session_table.teacher_class_subject_id');
                    $session_attend->join('subject_class','subject_class.id','teacher_class_subject.subject_class_id');
                    if($request['subject_id']){
                    $session_attend->where('subject_class.subject_id',$request['subject_id']);
                    }
                        if($request['from'] && $request['to']){
                            
                            $session_attend->where('session_table.start_time','>=',$request['from'].$start_time);
                            $session_attend->where('session_table.end_time','<=',$request['to'].$end_time);
                        } 
                    $session_attend=$session_attend->first();
                $student_detail['s_no']=$i;
                $student_detail['roll_no']=$student->roll_no;
                $student_detail['name']=$student->name;
                $student_detail['total_attend']= ($session_attend->total_attend >0)? $session_attend->total_attend : 0;
                $student_detail['total_session']= $totalsession;
                $student_details[]=$student_detail;
                $i++;
            }
            return collect($student_details);
    }
    
    public function headings(): array
    {
        return [
            'S. No.',
            'Roll No.',
            'Name',
            'Total Session',
            'Total Attend',
        ];
    }
}
