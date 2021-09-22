@extends(  Auth::guard('teacher')->check() ?   'BackEnd/teacherLayouts.master' : 'BackEnd/layouts.master')
@section('title')
Session Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> Session Management</h4>
              </div>
              @if(Session::has('success_message'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{Session::get('success_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif

                <form action="{{url(session("role").'/session')}}" method="POST"  enctype="multipart/form-data">
                @csrf  
                

              <div class="row-md-1" cellspacing="0" width="100%">
                @if(!Auth::guard('teacher')->check())  
                  @if(Auth::guard('admin')->check())
                    <select class="form-control" name='school_id' id='school_id' onclick="getALLClassSection(this.value)" style="margin-left:20px;width:180px;">
                      <option value="">Select School</option>
                      @foreach($schools as $school)
                        @if(array_key_exists("school_id",$dropdown_selected))
                          @if($dropdown_selected['school_id'] == $school->id)
                            <option value="{{$school->id}}" selected>{{ucfirst($school->name)}}</option>
                          @else
                            <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                          @endif
                        @else
                           <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                        @endif  
                      @endforeach
                    </select>  
                
                  @else
                  <input type='hidden' name='school_id' value="<?= session('user_school_id'); ?>">
                  @endif


               
                  <select class="form-control"  name='class_section_id' id='class_section_id' onchange="getALLSubjectsByClass(this.value)" style="margin-left:10px;width:180px;"> 
                 
                      <option value="">Select Class_Section</option>
                      @if(!(Auth::guard('admin')->check()))
                        @foreach($class_sections as $class_section)
                            @if(array_key_exists("class_section",$dropdown_selected))
                              @if($dropdown_selected['class_section'] == $class_section->id)
                                <option value="{{$class_section->id}}" selected>{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                              @else
                                <option value="{{$class_section->id}}">{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                              @endif
                            @else
                              <option value="{{$class_section->id}}">{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                            @endif
                        @endforeach  
                      @else
                        @if(array_key_exists("class_section",$dropdown_selected))
                          @foreach($class_sections as $class_section)
                            @if($dropdown_selected['class_section'] == $class_section->id)
                                <option value="{{$class_section->id}}" selected>{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                            @endif
                          @endforeach
                        @endif
                      @endif                
                      
                  </select>

                <select class="form-control" name='subject_id' id='subject_id' style="margin-left:10px">
                      <option value="">Select Subject</option>
                      
                </select>
              

                <!-- <select class="form-control" name="teacher_class_subject_id" id="teacher_class_subject_id" style="margin-left:20px">
                    <option value="">Select Class-Section and Subject</option>
                    @foreach($teacher_class_subjects as $teacher_class_subject)
                      @if(array_key_exists("teacher_class_subject",$dropdown_selected))
                      <option {{($dropdown_selected['teacher_class_subject'] == $teacher_class_subject['id'] ? 'selected':'' ) }} value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                      @else
                      <option value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                      @endif
                    @endforeach
                </select> -->
              @endif 
              &nbsp; &nbsp;
              <input class="form-top" type="text" placeholder="From Date" id="date_from" name="from" value="{{$request->from}}" style="height: 30px; width:90px;">
              &nbsp; &nbsp;
              <input class="form-top" type="text" placeholder="To Date" id="date_to" name="to" value="{{$request->to}}" style="height: 30px; width:90px;">
              &nbsp; &nbsp;
                
              <button type="submit" class="btn btn-primary form-top" value="">Filter</button>
              <a href="{{url(session("role").'/session')}}" class="btn btn-primary form-top">Reset</a>
                 
                </div>


              </form>
              
              <div class="card-body">
              
              <div class="col-md-12">
                  <div style="text-align:right;">
                    @if(Auth::guard('teacher')->check())
                    <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addSession" rel='/api/v2/session-form'  data="addeditform" >
                      Add Session
                    </button>
                    @endif
                </div>
         <div class="card card-tasks">
            <div class="session-section">
              @if(!empty($sessions))
               <h4 class="card-title mt-1">Live Upcoming Sessions</h4>
              @else
               <tr><td colspan="10" class="text-center">Record not found</td></tr>
              @endif 
               <?php
                 $Today="<h3>Today</h3>";
                 $Tomorrow="<h3>Tomorrow</h3>";
                 $changeDate="";
                if(!empty($sessions))
                 foreach($sessions as $session){
                   $sessionDate=date("Y-m-d" ,strtotime($session->date) );
                   if($changeDate != $sessionDate ){
                     if($sessionDate==date("Y-m-d")){
                        echo $Today;     
                     }elseif($sessionDate == date('Y-m-d',strtotime('+1 day',strtotime(date("Y-m-d"))))){
                        echo $Tomorrow;
                      }else{
                        echo "<h3>".date('d M Y', strtotime($session->date))."</h3>";
                      } 
                      $changeDate =$sessionDate;
                   }
                    ?>
                      <div class="box">
                          <div class="main-flex">
                            <div class="left">
                                <h2><?= $session->subject_name; ?></h2>
                                <p><i class="now-ui-icons ui-2_time-alarm"></i> <?php echo date('h:i a', strtotime($session->start_time)); ?></p>
                                <p><i class="now-ui-icons ui-2_time-alarm"></i>  <?php echo date('h:i a', strtotime($session->end_time)); ?></p>
                            </div>
                            <div class="col-md-4">
                               @if(!empty($session->class_name))
                                <div class="row mb-3">
                                  <div class="col-4"><b>Class</b></div>
                                  <div class="col-8"><?= $session->class_name.'-'.$session->section_name; ?></div>
                                </div>
                                @endif
                                @if(!empty($session->teacher_name))
                                <div class="row mb-3">
                                  <div class="col-4"><b>Teacher</b></div>
                                  <div class="col-8"><?= $session->teacher_name ?></div>
                                </div>
                                @endif
                                @if(!empty($session->online_class_url))
                                <div class="row mb-3">
                                  <div class="col-4"><b>Meeting Url</b></div>
                                  <div class="col-8"> <a href="{{$session->online_class_url}}" target="_blank" rel="noopener noreferrer">{{$session->online_class_url}}</a></div>
                                </div>
                                @endif
                                @if(!empty($session->meeting_id))
                                <div class="row mb-3">
                                  <div class="col-4"><b>Meeting Id</b></div>
                                  <div class="col-8">{{$session->meeting_id}}</div>
                                </div>
                                @endif
                                @if(!empty($session->password))
                                <div class="row mb-3">
                                  <div class="col-4"><b>Password</b></div>
                                  <div class="col-8"><?= $session->password; ?></div>
                                </div>
                                @endif
                            </div>
                            <div>
                              
                                <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addSession" rel='/api/v2/session-form?id=<?=$session->id?>'  data="addeditform" >
                                        Edit
                                </button>
                              

                                <a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$session->id}})" 
                                data-target="#DeleteModal" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                  
                            </div>
                          </div>

                      </div>
                      
                   <?php } ?>
                   
            </div>
         </div>
      </div>



                
               </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal" id="addSession">
        <div class="modal-dialog">
          <div class="modal-content" id = "addeditform">
          </div>
        </div>
      </div>

      <div id="DeleteModal" class="modal fade text-danger" role="dialog">
        <div class="modal-dialog ">
          <!-- Modal content-->
          <form action="" id="deleteForm" method="post">
              <div class="modal-content">
                  <div class="modal-header bg-danger">
                      <!-- <button type="button" class="close float-right" data-dismiss="modal">&times;</button> -->
                      <h4 class="modal-title text-center">DELETE CONFIRMATION</h4>
                  </div>
                  <div class="modal-body">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <p class="text-center">Are You Sure Want To Delete ?</p>
                  </div>
                  <div class="modal-footer">
                      <center>
                          <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                          <button type="submit" name="" class="btn btn-danger" data-dismiss="modal" onclick="formSubmit()">Yes, Delete</button>
                      </center>
                  </div>
              </div>
          </form>
          
        </div>
        </div>
@endsection
@section('scripts')
<script>
    $(document).ready( function () {
    $('#dataTable').DataTable();
} );

function deleteData(id)
{
    var id = id;
    var url = '{{url(session("role").'/session')}}';
    
    url = url+'/'+id;
    console.log(url);
    $("#deleteForm").attr('action', url);
}

function formSubmit()
{
    $("#deleteForm").submit();
}
$('#datepicker1').datepicker({
                  format: 'yyyy-mm-dd'
                });
$('#datepicker2').datepicker({
                  format: 'yyyy-mm-dd'
                });
</script>
@endsection