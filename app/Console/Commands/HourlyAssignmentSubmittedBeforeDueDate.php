<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Session;
use App\Student, App\Assignment, App\Notifaction;

class HourlyAssignmentSubmittedBeforeDueDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hour:assignmentsubmittedbeforduedate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send notification before hour of due date of assignment submission';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $today = date('Y-m-d H:i:s');
        $today_After1 = date('Y-m-d H:i:s',strtotime('+1 hour'));
        
        $datas = Assignment::whereBetween('due_date', array($today,$today_After1))->get();
        if($datas)
            $datas = $datas->toArray();

        foreach ($datas as $data) {
            $array = [];
            $array['school_id'] = $data['school_id'];
            $array['role'] = 'student';
            $request = (object) $array;
            (new Notifaction())->setNewNotificatin($request,'assignment',$data['id'],'beforHour');
        }
           $this->info('Befor Hourly Assignment Submission notification has been send successfully');
        //
    }
}
