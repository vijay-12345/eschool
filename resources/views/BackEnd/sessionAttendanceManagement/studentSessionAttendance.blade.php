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
                <h4 class="card-title">Attendance Report By Student</h4>
              </div>
              @if(Session::has('success_message'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{Session::get('success_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif


              <form action="{{url(session("role").'/session-attendance-by-student')}}" method="POST"  enctype="multipart/form-data">
                @csrf
              
                @if(Auth::guard('admin')->check())

                    <select class="form-control" style="margin-left:10px" name='school_id' id='school_id' onchange="getALLClassSection(this.value)">
                      <option value="">Select School</option>
                      @foreach($schools as $school)
                        @if(array_key_exists("school_id",$dropdown_selected))
                          @if($dropdown_selected['school_id'] == $school->id)
                            <option value="{{$school->id}}" selected>{{ucfirst($school->name)}}</option>
                          @else
                            <option value="{{$school->id}}" {{($request->school_id && $request->school_id == $school->id) ? 'selected' : ''}}>{{ucfirst($school->name)}}</option>
                          @endif
                        @else
                           <option value="{{$school->id}}" {{($request->school_id && $request->school_id == $school->id) ? 'selected' : ''}}>{{ucfirst($school->name)}}</option>
                        @endif  
                      @endforeach
                    </select>
                <input type='hidden' name='schoolId' id="schoolId" value=" {{$request->school_id}}">
                
                @else
                <input type='hidden' name='school_id' value="<?= session('user_school_id'); ?>">
                @endif


               
                  <select class="form-control" style="margin-left:10px" name='class_section_id' id='class_section_id' onchange="getALLSubjectsByClass(this.value)">
                 
                      <option value="">Select Class_Section</option>
                      @if(!(Auth::guard('admin')->check()))
                        @foreach($class_sections as $class_section)
                                <option value="{{$class_section->id}}"  {{($request->class_section_id && $request->class_section_id == $class_section->id) ? 'selected' : ''}} >{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                        @endforeach  
                      @else
                         
                      @endif                
                      
                  </select>
                <input type='hidden' name='classSectionId' id="classSectionId" value=" {{$request->class_section_id}}">
                <input type="hidden" name="subjectId" id="subjectId" value="{{($request->subject_id) ? $request->subject_id : ''}}">
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
                <input class="form-top" type="text" placeholder="From Date" id="date_from" name="from" value="{{($request->from)}}" style="height: 30px; width:90px;">
                &nbsp; &nbsp;
                <input class="form-top" type="text" placeholder="To Date" id="date_to" name="to" value="{{($request->to)}}" style="height: 30px; width:90px; ">
                &nbsp; &nbsp;
                <!--<label for="class" style="padding-left:50px"></label>-->
                <button type="submit" class="btn btn-primary form-top" style="">Submit</button>
                &nbsp; &nbsp;
                <a href="" class="btn btn-primary form-top" style="">Reset</a>
                &nbsp; &nbsp;
                <button type="submit" name="export" value="export_record" class="btn btn-primary form-top" style="float: right">export</button>
               
              </form>  

              <div class="card-body text-right">
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Attempt<th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($students))
                          @foreach($students as $student)
                            <tr>
                                <td><?= $id; ?></td>
                                <td>
                                @if($student->isProfilePic == '1')
                                    <img src="{{$student->profile_pic_url}}" alt="logo" width="50" style="border-radius: 100%;">                          
                                @elseif($student->isProfilePic == '0')
                                     <span class="glyphicon glyphicon-user"></span>
                                @endif
                                </td>
                                <td>{{ucfirst($student->name)}}</td>
                                <td>{{($student->total_attend)}}/{{($totalsession)}}</td> 
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
        var schoolId=$('#schoolId').val();
        var classSectionId= $('#classSectionId').val();
        var subjectId= $('#subjectId').val();
        if(schoolId && schoolId>0){
            getALLClassSection(schoolId,classSectionId);
        }
        if(classSectionId && classSectionId>0){
            getALLSubjectsByClass(classSectionId,subjectId);
        }
    });

    $(document).ready( function () {
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
                  modelContainerStr = modelContainerStr + '<option value="0"  >Select Subject</option>';        
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

    function getALLClassSection(schoolId,classSectionId=''){ 
       // alert(schoolId);
        var url = '{{url(session("role").'/class_section-using-school')}}';
        $.ajax({
            type: "POST",
            url: url,
            data: {
                    "_token": "{{ csrf_token() }}",
                    "school_id":schoolId
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
                            if(jsonData[i].id == classSectionId){
                                selected = 'selected';
                            }
                            modelContainerStr = modelContainerStr + '<option value="'+jsonData[i].id+'" '+selected+' >'+jsonData[i].class_name+'-'+jsonData[i].section_name+'</option>';        
                  } 
                  $('#class_section_id').append(modelContainerStr);
                  $('select#class_section_id')[0].sumo.reload();

          } 
          });
    }
    


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