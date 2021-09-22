@extends('BackEnd/layouts.master')
@section('title')
Quiz Management | School App
@endsection
@section('content')
<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header d-sm-flex justify-content-between">
                <h4 class="card-title"> Quiz Management</h4>
                <div>
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url(session("role")."/quiz")}}">Quiz</a></li>
                    </ol>
                  </nav>
                </div>
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
            
              <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addQuiz" rel='/api/v2/quiz-form'  data="addeditform" >
                Add Quiz
              </button>
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <!--<th>Status</th>--->
                                <th>Title</th>
                                <!--<th>School</th>-->
                                <th>Class-Section</th>
                                <th>Subject</th>
                                <th>Question Count</th>
                                <th>Students Response</th>
                                <th>Start-Expire</th>
                                <th>Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($quiz_tables))
                          @foreach($quiz_tables as $quiz_table)
                            <tr>
                                <td><?= $id; ?></td>
                    
                                <td>{{ucfirst($quiz_table->name)}}</td>
                               
                                  <td>{{ $quiz_table->class_name }}-{{ $quiz_table->section_name }}</td>
                                  
                                  <td>{{ $quiz_table->subject_name }}</td>
                                 
                                <td>{{ $quiz_table->total_question }}</td>
                               
                                <td>{{ $quiz_table->total_attempt }}/{{ $quiz_table->total_students }}</td>
                                <?php $start_time = Carbon\Carbon::parse($quiz_table->start_time); ?>
                                <?php $start_time_month = $start_time->format('m'); ?>
                                <?php $start_timeObj   = DateTime::createFromFormat('!m', $start_time_month); ?>
                                <?php $start_time_monthName = $start_timeObj->format('F') ?>

                                <?php $expired_time = Carbon\Carbon::parse($quiz_table->expired_time); ?>
                                <?php $expired_time_month = $expired_time->format('m'); ?>
                                <?php $expired_timeObj   = DateTime::createFromFormat('!m', $expired_time_month); ?>
                                <?php $expired_time_monthName = $expired_timeObj->format('F') ?>

                                <td><table>
                                        <tr>
                                                @if($quiz_table->publish == '0')
                                                <td>-------- </td> &nbsp; &nbsp;<td> -------</td>
                                                @else
                                                <td>
                                                {{$start_time->format('d').' '.$start_time_monthName.', '.$start_time->format('Y').'       '  .$start_time->format('h:ia')}}</td><td>{{$expired_time->format('d').' '.$expired_time_monthName.', '.$expired_time->format('Y').'       '  .$expired_time->format('h:ia')}}
                                                </td>
                                                @endif
                                            </tr>
                                    </table>
                                </td>

                                <td>
                                    @if($quiz_table->publish == '0')
                                    <button type="button" class="btn btn-primary editQuize gettingform" data-toggle="modal" data-target="#addQuiz" rel='/api/v2/quiz-form?id=<?=$quiz_table->id?>' id="<?=$quiz_table->id?>"  data="addeditform" >
                                            Edit Quiz
                                    </button> 
                                    <a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$quiz_table->id}})" 
                                    data-target="#DeleteModal" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                    <a href="{{url(session("role")."/quiz-detail/addQuestion/".$quiz_table->id)}}" class="btn btn-primary a-btn-slide-text" class="btn btn-xs btn-danger"> Questions</a>
                                    @endif
                                    @if($quiz_table->publish == '0')
                                    <button type="button" class="btn btn-success gettingform" data-toggle="modal" data-target="#addPublish" rel='/api/v2/quiz-publish-form/{{ $quiz_table->id }}'  data="addPublishForm" >
                                     Publish
                                    </button>
                                    @elseif($quiz_table->total_attempt < 1)
                                    <a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="publishData({{$quiz_table->id}})" 
                                    data-target="#UnPublishModal" class="btn btn-xs btn-danger">UnPublish</a>
                                    @endif
                                    
                                    <a href="{{url(session("role")."/quiz-results/".$quiz_table->id)}}" class="btn btn-primary a-btn-slide-text" class="btn btn-xs btn-danger">Quiz Result</a>
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

      <div class="modal" id="addQuiz">
        <div class="modal-dialog">
          <div class="modal-content" id = "addeditform">
          </div>
        </div>
      </div>

      <div class="modal" id="addPublish">
        <div class="modal-dialog">
          <div class="modal-content" id = "addPublishForm">
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
        <!------PUBLISH MODEL ---->
        <div id="PublishModal" class="modal fade text-danger" role="dialog">
        <div class="modal-dialog ">
          <!-- Modal content-->
          <form action="" id="publishForm" method="get">
              <div class="modal-content">
                  <div class="modal-header bg-danger">
                      <!-- <button type="button" class="close float-right" data-dismiss="modal">&times;</button> -->
                      <h4 class="modal-title text-center">PUBLISH CONFIRMATION</h4>
                  </div>
                  <div class="modal-body">
                      {{ csrf_field() }}

                      <p class="text-center">Are You Sure Want To Publish ?</p>
                  </div>
                  <div class="modal-footer">
                      <center>
                          <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                          <button type="submit" name="" class="btn btn-danger" data-dismiss="modal" onclick="publishformSubmit()">Yes, Publish</button>
                      </center>
                  </div>
              </div>
          </form>
          
        </div>
        </div>
        <!------UNPUBLISH MODEL ---->
        <div id="UnPublishModal" class="modal fade text-danger" role="dialog">
        <div class="modal-dialog ">
          <!-- Modal content-->
          <form action="" id="publishForm" method="get">
              <div class="modal-content">
                  <div class="modal-header bg-danger">
                      <!-- <button type="button" class="close float-right" data-dismiss="modal">&times;</button> -->
                      <h4 class="modal-title text-center">UNPUBLISH CONFIRMATION</h4>
                  </div>
                  <div class="modal-body">
                      {{ csrf_field() }}
                      
                      <p class="text-center">Are You Sure Want To UnPublish ?</p>
                  </div>
                  <div class="modal-footer">
                      <center>
                          <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                          <button type="submit" name="" class="btn btn-danger" data-dismiss="modal" onclick="publishformSubmit()">Yes, UnPublish</button>
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
    var url = '{{url(session("role").'/quiz')}}';
    
    url = url+'/'+id;
    console.log(url);
    $("#deleteForm").attr('action', url);
}

function formSubmit()
{
    $("#deleteForm").submit();
}
function publishData(id)
{
    var id = id;
    var url = '{{url(session("role").'/quiz/publish')}}';
    
    url = url+'/'+id;
    console.log(url);
    $("#publishForm").attr('action', url);
}

function publishformSubmit()
{
    $("#publishForm").submit();
}

function getALLSubjectsByClass(classSubId,getSubjectId=''){ 
        var url = '{{url(session("role").'/quiz-result/getsubject')}}';
        $.ajax({
            type: "POST",
            url: url,
            data: {
                    "_token": "{{ csrf_token() }}",
                    "class_section_id":classSubId
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
    
    $(document).ready(function(){
       $('.editQuize').click(function(){
           var id = $(this).attr('id');
           var url = '{{url(session("role").'/quiz-result/getquizeDetail')}}';
           
            $.ajax({
                type: "POST",
                url: url,
                data: {
                        "_token": "{{ csrf_token() }}",
                        "quizeId":id
                        },
                success: function(response){
                    var resp = JSON.parse(response);
                    getALLSubjectsByClass(resp.class_section_id,resp.subject_id);
              } 
              });
           //var classSectionId= $('#class_section_id').val();
           //alert(classSectionId);

       }); 
    });
    
   
</script>
@endsection