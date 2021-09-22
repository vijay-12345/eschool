@extends('BackEnd/teacherLayouts.master')
@section('title')
Assignment Management | School App
@endsection
@section('content')


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

              <form action="{{url(session("role").'/assignment')}}" method="POST"  enctype="multipart/form-data">
                @csrf 
                <select class="form-control" name="teacher_class_subject_id" id="teacher_class_subject_id" style="margin-left:20px;">
                    <option value="">Select Class-Section and Subject</option>
                    @foreach($teacher_class_subjects as $teacher_class_subject)
                      @if(array_key_exists("teacher_class_subject",$dropdown_selected))
                      <option {{($dropdown_selected['teacher_class_subject'] == $teacher_class_subject['id'] ? 'selected':'' ) }} value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                      @else
                      <option value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                      @endif
                    @endforeach
                </select>
                
                <button type="submit" class="btn btn-primary form-top" style="margin-left:20px" value="">Filter</button>
                
              </form>

              <div class="card-body text-right">
              <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addAssignment" rel='/api/v2/assignment-form'  data="addeditform" >
                Add Assignment
              </button>
              
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Title & Description</th>
                                <th>Date</th>
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
                                    <td><b>Title:</b>{{ucfirst($assignment['title'])}}</td> 
                                  </tr>
                                  <tr>
                                    <td><b>Description:</b>{{ucfirst($assignment['assignment_description'])}}</td>
                                  </tr>
                                  
                                </table>
                              </td>
                              <td>
                                <table><tr>
                                    <?php $due_date = Carbon\Carbon::parse($assignment['due_date']); ?>
                                    <?php $due_date_month = $due_date->format('m'); ?>
                                    <?php $due_dateObj   = DateTime::createFromFormat('!m', $due_date_month); ?>
                                    <?php $due_date_monthName = $due_dateObj->format('F') ?>
                                    <td><b>Due Date:</b>{{$due_date->format('d').' '.$due_date_monthName.', '.$due_date->format('Y').'       '  .$due_date->format('h:ia')}}
                                    </td>
                                  </tr>
                                  <tr>
                                    <?php $created_date = Carbon\Carbon::parse($assignment['created_date']); ?>
                                    <?php $created_date_month = $created_date->format('m'); ?>
                                    <?php $created_dateObj   = DateTime::createFromFormat('!m', $created_date_month); ?>
                                    <?php $created_date_monthName = $created_dateObj->format('F') ?>
                                    <td><b>Created Date:</b>{{$created_date->format('d').' '.$created_date_monthName.', '.$created_date->format('Y').'       '  .$created_date->format('h:ia')}}</td>
                                  </tr></table>
                              </td>

                              <td>
                                <table>
                                  <tr>
                                <td><?php if($assignment['total_attachments'] >1){
                                        $word='s';
                                    }else{
                                        $word='';
                                    }
                                    ?>
                                    <a>
                                    <!--<span class="glyphicon glyphicon-download" style="cursor:pointer;" ></span>--><strong>{{ucfirst($assignment['total_attachments'])}}</strong> Attachment{{$word}}</a>
                                </td>
                                  </tr>
                                  <tr>
                                <td>
                                    <a>{{ucfirst($assignment['submittedbystudent'].'/'.$assignment['totalstudent'])}} Attempt</a>
                                </td>
                              </tr>
                                </table>
                              </td>  
                                    
                                <td>
                                  @if($assignment['total_attachments'] >0)
                                    <button type="button"  class="btn btn-primary gettingform" data-toggle="modal" data-target="#downloadAttachment" rel='/api/v2/assignment-download-form?id=<?=$assignment['id']?>'  data="attachmentForm" >
                                      View Attachment
                                    </button>
                                  @endif

                                    <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addAssignment" rel='/api/v2/assignment-form?id=<?=$assignment['id']?>'  data="addeditform" >
                                            Edit
                                    </button>
                                    <a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$assignment['id']}})" 
                                    data-target="#DeleteModal" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                
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

      <div class="modal" id="addAssignment">
        <div class="modal-dialog">
          <div class="modal-content" id = "addeditform">
          </div>
        </div>
      </div>

      <div class="modal" id="downloadAttachment">
        <div class="modal-dialog">
          <div class="modal-content" id = "attachmentForm">
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
    var url = '{{url(session("role").'/assignment')}}';
    
    url = url+'/'+id;
    console.log(url);
    $("#deleteForm").attr('action', url);
}

function formSubmit()
{
    $("#deleteForm").submit();
}
</script>
@endsection