@extends('BackEnd/layouts.master')
@section('title')
Assignment Management | School App
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
                <h4 class="card-title"> Assignment Management</h4>
              </div>
              @if(Session::has('success_message'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{Session::get('success_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif
              <div class="card-body text-right">
                  <div class="card-body text-left">
                  <tr>
                <form action="{{url($request['role'].'/library-assignment')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/library-study-material')}}'>
                    {{ csrf_field() }}

                     <label for="class"><!----Class-Section<span >*</span>--></label>
                      <select class="form-control" name="class_section_id" id="class_section_id" onchange="getALLSubjectsByClass(this.value)">
                        <option value="">Select Class-Section</option>
                        @foreach($class_sections as $class_section)
                        <option value="{{$class_section->id}}" {{($request->class_section_id && $request->class_section_id == $class_section->id) ? 'selected' : ''}}>{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                        @endforeach
                      </select>
                    <input type="hidden" name="subjectId" id="subjectId" value="{{($request->subject_id) ? $request->subject_id : ''}}">
                     <label for="class"><!-----Subjects<span >*</span>---></label>
                      <select class="form-control" name="subject_id" id="subject_id">
                        <option value="">Select Subjects</option>    
                      </select>
                     &nbsp; &nbsp;
                    <button type="submit" class="btn btn-primary form-top">Submit</button>
                  </form>
                </tr>

                </div>
            
              <!--<button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addAssignment" rel='/api/v2/assignment-form'  data="addeditform" >
                Add Assignment
              </button>-->
              
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Description</th>
                                <th>Dates</th>
                                <th>Details</th>
                                <th>Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($assignments))
                          @foreach($assignments as $assignment)
                            <tr>
                                <td><?= $id; ?></td>
                                <td>
                                    <table>
                                        <tr>
                                            <td><strong>Title:</strong> {{ucfirst($assignment['title'])}}</td> 
                                        
                                        </tr>
                                        <tr>
                                            <td><strong>Description:</strong> {{ucfirst($assignment['assignment_description'])}}</td> 
                                            
                                        </tr>
                                          
                                    </table>  
                                </td>
                                <td style="width:170px;">
                                    <table>
                                        <tr>
                                            <?php $due_date = Carbon\Carbon::parse($assignment['due_date']); ?>
                                            <?php $due_date_month = $due_date->format('m'); ?>
                                            <?php $due_dateObj   = DateTime::createFromFormat('!m', $due_date_month); ?>
                                            <?php $due_date_monthName = $due_dateObj->format('F') ?>

                                    
                                            <td><strong>Due Date:</strong> {{$due_date->format('d').' '.$due_date_monthName.', '.$due_date->format('Y').'       '  .$due_date->format('h:ia')}}</td> 
                                        </tr>
                                        <tr>
                                            <?php $created_date = Carbon\Carbon::parse($assignment['created_date']); ?>
                                            <?php $created_date_month = $created_date->format('m'); ?>
                                            <?php $created_dateObj   = DateTime::createFromFormat('!m', $created_date_month); ?>
                                            <?php $created_date_monthName = $created_dateObj->format('F') ?>

                                            <td><strong>Created Date:</strong> {{$created_date->format('d').' '.$created_date_monthName.', '.$created_date->format('Y').'       '  .$created_date->format('h:ia')}}</td>
                                        </tr>    
                                    </table>
                                </td>
                                <td style="width:110px;">
                                    <table>
                                        <tr><td>
                                            <?php if($assignment['total_attachments'] >1){
                                            $word='s';
                                            }else{
                                                $word='';
                                            }
                                            ?>
                                            <a><strong>{{$assignment['total_attachments']}}</strong> Attachment{{$word}}</a>
                                            </td>
                                        </tr>
                                        <tr><td>{{ucfirst($assignment['total_student_submitted'])}}/{{ucfirst($assignment['total_students'])}} Attempts</td></tr>
                                        
                                    </table>
                                   
                                </td>    
                                <td>
                                @if($assignment['total_attachments'] >0)
                                  <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#downloadAttachment" rel='/api/v2/library-assignment-form?id=<?=$assignment['id']?>'  data="attachmentForm" >
                                    View Attachment
                                  </button>
                                @endif
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
      <div class="modal" id="downloadAttachment">
        <div class="modal-dialog">
          <div class="modal-content" id = "attachmentForm">
          </div>
        </div>
      </div>
@endsection
@section('scripts')
<script>
    $(document).ready( function () {
    $('#dataTable').DataTable();
    
    var classSectionId= $('#class_section_id').val();
    var subjectId= $('#subjectId').val();
    if(classSectionId){
        getALLSubjectsByClass(classSectionId,subjectId);
    }
    
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
</script>
@endsection