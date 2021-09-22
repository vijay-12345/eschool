@extends('BackEnd/teacherLayouts.master')
@section('title')
Dashboard | School App
@endsection
@section('content')
<?php 

   function viewMoreAndLess($first, $secondArr = [])
   {
       if(!empty($first)){
         echo $first;
       }
       if(!empty($secondArr)){
        $secondArr=array_unique($secondArr);
           ?>
<span class="viewLess"><?=$secondArr[0]?>
<?php if(count($secondArr)>1){?>
<span class="clickviewmore" title="View More">view more..</span>
<?php }?>
</span>
<span class="viewmore">
<?php 
   echo implode(", ", $secondArr);
   ?>
<span class="clickviewless" title="View Less">view less..</span>
</span>
<?php }
   }
   
   ?>
<div class="panel-header panel-header-sm">
</div>
<div class="content">
   <div class="row">
      @if(Auth::guard('school')->check() || Auth::guard('admin')->check())
      <div class="col-md-4">
         <div class="box-dashboard">
            <div class="d-flex align-items-center justify-content-between">
               <div>
                  <h3 class="total-order mt0">Students</h3>
                  <h2 class="color-red total-value mt0 mb0">{{$studentCount}}</h2>
               </div>
               <div>
                  <div class="total-order-ico">
                     <img src="<?php echo url('/adminAssets/img/icon1.png') ?>" alt="" title="">
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-4">
         <div class="box-dashboard">
            <div class="d-flex align-items-center justify-content-between">
               <div>
                  <h3 class="total-order mt0">Teachers</h3>
                  <h2 class="color-yellow total-value mt0 mb0">{{$teacherCount}}</h2>
               </div>
               <div>
                  <div class="total-order-ico">
                     <img src="<?php echo url('/adminAssets/img/icon2.png') ?>" alt="" title="">
                  </div>
               </div>
            </div>
         </div>
      </div>
      @endif
      @if(Auth::guard('teacher')->check())
        <div class="col-md-12">
      @else
        <div class="col-md-4">
      @endif
         <div class="box-dashboard">
            <div class="d-flex align-items-center justify-content-between">
               <div>
                  <h3 class="total-order mt0">Live Upcoming Sessions</h3>
                  <h2 class="color-blue total-value mt0 mb0">{{count($UpcommingSession)}}</h2>
               </div>
               <div>
                  <div class="total-order-ico">
                     <img src="<?php echo url('/adminAssets/img/icon3.png') ?>" alt="" title="">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-6">

         <div class="card card-tasks">
            <div class="announcements">
               <h4 class="card-title mt-2">Notice Board</h4>
               <div id="noticeBoard">
               </div>
               <div class="stats">
                  
                  <a href="{{url(session("role").'/notice')}}"> View more</a>
                </div>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="card card-tasks">
            <div class="session-section">
               <h4 class="card-title mt-1">Live Upcoming Sessions</h4>
               <?php
                 $Today="<h3>Today</h3>";
                 $Tomorrow="<h3>Tomorrow</h3>";
                 $changeDate="";
                 $count=1;
                 foreach($UpcommingSession as $upComming){
                  if($count<=5){
                   $count = $count + 1;
                   $sessionDate=date("Y-m-d" ,strtotime($upComming->date) );
                   if($changeDate != $sessionDate ){
                     if($sessionDate==date("Y-m-d")){
                        echo $Today;     
                     }elseif($sessionDate == date('Y-m-d',strtotime('+1 day',strtotime(date("Y-m-d"))))){
                        echo $Tomorrow;
                      }else{
                        echo "<h3>".date('d M Y', strtotime($upComming->date))."</h3>";
                      } 
                      $changeDate =$sessionDate;
                   }
                    ?>
                      <div class="box">
                          <div class="main-flex">
                            <div class="left">
                                <h2><?= $upComming->subject_name; ?></h2>
                                <p><i class="now-ui-icons ui-2_time-alarm"></i> <?php echo date('h:i a', strtotime($upComming->start_time)); ?></p>
                                <p><i class="now-ui-icons ui-2_time-alarm"></i>  <?php echo date('h:i a', strtotime($upComming->end_time)); ?></p>
                            </div>
                            <div class="right">
                               @if(!empty($upComming->class_name))
                                <div class="row mb-1">
                                  <div class="col-4"><b>Class</b></div>
                                  <div class="col-8"><?= $upComming->class_name.'-'.$upComming->section_name; ?></div>
                                </div>
                                @endif
                                @if(!empty($upComming->teacher_name))
                                <div class="row mb-1">
                                  <div class="col-4"><b>Teacher</b></div>
                                  <div class="col-8"><?= $upComming->teacher_name ?></div>
                                </div>
                                @endif
                                @if(!empty($upComming->online_class_url))
                                <div class="row mb-1">
                                  <div class="col-4"><b>Meeting Url</b></div>
                                  <div class="col-8"> <a href="{{$upComming->online_class_url}}" target="_blank" rel="noopener noreferrer">{{$upComming->online_class_url}}</a></div>
                                </div>
                                @endif
                                @if(!empty($upComming->meeting_id))
                                <div class="row mb-1">
                                  <div class="col-4"><b>Meeting Id</b></div>
                                  <div class="col-8">{{$upComming->meeting_id}}</div>
                                </div>
                                @endif
                                @if(!empty($upComming->password))
                                <div class="row mb-1">
                                  <div class="col-4"><b>Password</b></div>
                                  <div class="col-8"><?= $upComming->password; ?></div>
                                </div>
                                @endif
                            </div>
                          </div>
                      </div>
                   
                   <?php }} ?>
                   <div class="stats">
                   <a href="{{url(session("role").'/session')}}"  style="color: blue">View More</a>
                 </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('scripts')
@endsection