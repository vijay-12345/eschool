@extends('BackEnd/layouts.master')
@section('title')
Session Attendance Management | School App
@endsection
@section('content')
<div class="dialog-background" id="loader" style="display:none;">
    <div class="dialog-loading-wrapper">
        <span class="dialog-loading-icon">Loading....</span>
    </div>
</div> 
<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"></h4>
              </div>
              @if(Session::has('success_message'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{Session::get('success_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif


              <form action="{{url(session("role").'/session-attendance')}}" method="POST" class="eschoolForm"  enctype="multipart/form-data"  rel='{{url(session("role").'/session-attendance/view')}}'>
                @csrf
              
                @if(Auth::guard('admin')->check())

                    <select class="form-control" style="margin-left:10px" name='school_id' id='school_id' onchange="getALLClassSection(this.value)">
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


               
                  <select class="form-control" style="margin-left:10px" name='class_section_id' id='class_section_id' onchange="getALLSubjectsByClass(this.value)">
                 
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

                <select class="form-control" style="margin-left:10px" name='subject_id' id='subject_id'>
                      <option value="">Select Subject</option>
                      @if(array_key_exists("subject",$dropdown_selected))
                        @foreach($subjects as $subject)
                          @if($dropdown_selected['subject'] == $subject->id)
                              <option value="{{$subject->id}}" selected>{{ucfirst($subject->subject_name)}}</option>
                          @endif
                        @endforeach
                      @endif
                </select>
                &nbsp; &nbsp;
                <input class="form-top" type="text" placeholder="From Date" id="date_from" name="date_from" value="{{(array_key_exists("date_from",$dropdown_selected)?$dropdown_selected['date_from']: $request->date_from) }}" style="height: 30px; width:90px;">
                &nbsp; &nbsp;
                <input class="form-top" type="text" autocomplete="off" placeholder="To Date" id="date_to" name="date_to" value="{{(array_key_exists("date_to",$dropdown_selected)?$dropdown_selected['date_to']: $request->date_to) }}" style="height: 30px; width:90px;">
                &nbsp; &nbsp;
                <button type="submit" class="btn btn-primary form-top" style="">Submit</button>
                <a href="{{url(session("role").'/session-attendance')}}" class="btn btn-primary form-top">Reset</a>
              </form>  

              <div class="card-body text-right">
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Session</th>
                                <th>No Of Attendees</th>
                                <th>Action<th>
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($sessions))
                          @foreach($sessions as $session)
                            <tr>
                                <td><?= $id; ?></td>

                                    <td>
                                        <table>
                                            <tr>
                                                <td><b>Topic: {{ucfirst($session->topic)}}</b></td>
                                                <?php $today_date = date("Y-m-d H:i:s"); ?>

                                                @if($session->end_time >= $today_date) 
                                                  <td style="color:dodgerblue">Status: {{ucfirst('Upcoming')}}</td>
                                                @else
                                                  <td style="color:green">Status: {{ucfirst('Completed')}}</td> 
                                                @endif   
                                            </tr>
                                            <?php $start = Carbon\Carbon::parse($session->start_time); ?>
                                            <?php $start_month = $start->format('m'); ?>
                                            <?php $dateObj   = DateTime::createFromFormat('!m', $start_month); ?>
                                            <?php $start_monthName = $dateObj->format('F') ?>
                                            <?php $end = Carbon\Carbon::parse($session->end_time); ?>
                                            <?php $end_month = $end->format('m'); ?>
                                            <?php $dateObj   = DateTime::createFromFormat('!m', $end_month); ?>
                                            <?php $end_monthName = $dateObj->format('F') ?>
                                            <tr>
                                                <td>Start At: {{$start->format('d').' '.$start_monthName.', '.$start->format('Y').'       '  .$start->format('h:ia')}}</td> 
                                                <td>End At: {{$end->format('d').' '.$end_monthName.', '.$end->format('Y').'       '  .$end->format('h:ia')}}</td>
                                            </tr>    
                                        </table>
                                    </td>

  
                                    <td>{{$session->participate_student_count.'/'.$session->total_student_count}}</td>
                                
                                <td>
                                    <a href="{{url(session("role").'/session-attendance/view/'.$session->id)}}" id="{{$session->id}}" class="btn btn-primary view">View</a>
                                </td>  
                            </tr>
                            <?php $id++; ?>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

@endsection
@section('scripts')
<script>
    $(document).ready( function () {
    var classSectionId= $('#class_section_id').val();
    var subjectId= $('#subject_id').val();
    if(classSectionId){
        getALLSubjectsByClass(classSectionId,subjectId);
    }
    
    $('#dataTable').DataTable();
    
} );
    function getALLSubjectsByClass(classSubId,getSubjectId=''){ 
        var url = '{{url(session("role").'/quiz-result/getsubject')}}';
        $.ajax({
            type: "POST",
            url: url,
            data: {
                    "_token": "{{ csrf_token() }}",
                    "class_section_id":classSubId
                    },
            beforeSend: function(){
                $('#loader').show();
            },
            complete: function(){
                $('#loader').hide();
            },
            success: function(response){
                var jsonData = eval('(' + response + ')'); 
                  //localStorage.removeItem("model_checked");
                  $('#subject_id').empty();
                  var modelContainerStr = '';
                  modelContainerStr = modelContainerStr + '<option value=""  >Select Subject</option>';        
                  for(i=0; i < jsonData.length; i++){
                      var selected='';
                            if(jsonData[i].id == getSubjectId){
                                selected = 'selected';
                            }
                            modelContainerStr = modelContainerStr + '<option value="'+jsonData[i].id+'" '+selected+' >'+jsonData[i].subject_name+'</option>';        
                  } 
                  $('#subject_id').append(modelContainerStr);
                  $('select#subject_id')[0].sumo.reload();

          } 
          });
    }

    function getALLClassSection(classSubId,getSubjectId=''){ 
        var url = '{{url(session("role").'/class_section-using-school')}}';
        $.ajax({
            type: "POST",
            url: url,
            data: {
                    "_token": "{{ csrf_token() }}",
                    "school_id":classSubId
                    },
            beforeSend: function(){
                $('#loader').show();
            },
            complete: function(){
                $('#loader').hide();
            },
            success: function(response){
                var jsonData = eval('(' + response + ')'); 
                  //localStorage.removeItem("model_checked");
                  $('#class_section_id').empty();
                  var modelContainerStr = '';
                  for(i=0; i < jsonData.length; i++){
                      var selected='';
                            if(jsonData[i].id == getSubjectId){
                                selected = 'selected';
                            }
                            modelContainerStr = modelContainerStr + '<option value="'+jsonData[i].id+'" '+selected+' >'+jsonData[i].class_name+'-'+jsonData[i].section_name+'</option>';        
                  } 
                  $('#class_section_id').append(modelContainerStr);
                  $('select#class_section_id')[0].sumo.reload();

          } 
          });
    }
        $(document).ready(function(){
            $('body').on('click', '.view', function() {
            event.preventDefault();
            var date_from= '';
            var date_to= '';
            // Get form
            var session_table_id =$(this).attr('id');
            date_from= $('#date_from').val();
            date_to= $('#date_to').val();
            window.location = $('.eschoolForm').attr('rel')+'/'+session_table_id+'/'+date_from+'/'+date_to;
          }); 
        });

        jQuery(function(){
         jQuery('#date_from').datetimepicker({
          format:'Y-m-d',
          onShow:function( ct ){
           this.setOptions({
            maxDate:jQuery('#date_to').val()?jQuery('#date_to').val():false
           })
          },
          timepicker:false
         });
         jQuery('#date_to').datetimepicker({
          format:'Y-m-d',
          onShow:function( ct ){
           this.setOptions({
            minDate:jQuery('#date_from').val()?jQuery('#date_from').val():false
           })
          },
          timepicker:false
         });
        });

</script>
@endsection