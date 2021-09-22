@extends('BackEnd/layouts.master')
@section('title')
Quiz Detail Management | School App
@endsection
@section('content')
<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
                <div class="card-header d-sm-flex justify-content-between">
                <h4 class="card-title">{{ $quiz_section_subject->class_section_name }} || {{ $quiz_section_subject->subject_name }} || {{ $quiz_section_subject->name }}</h4>
                <div>
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url(session("role")."/quiz")}}">Quiz</a></li>
                        <li class="breadcrumb-item"><a href="{{url(session("role")."/quiz-detail/addQuestion/".$quize_id)}}">Quiz Question</a></li>
                    </ol>
                  </nav>
                </div>
              </div>
                
                
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
              @if(Session::has('error_message'))
                     <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{Session::get('error_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif
              <div class="card-body text-right">
              @foreach($quiz_tables as $quiz_table)
              @if($quiz_table->id == $quize_id && $quiz_table->publish == '0')

              <button type="button" class="btn btn-success gettingform" data-toggle="modal" data-target="#addPublish" rel='/api/v2/quiz-publish-form/{{ $quiz_table->id }}'  data="addPublishForm" >
                Publish
              </button>
              <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addQuestion" rel='/api/v2/quiz-question-form/{{ $quize_id}}'  data="addeditform" >
                Add Question
              </button>
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importQuizDetail">
                Import Questions
              </button>
            
              @break
              @endif
              @endforeach


                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>Question No.</th>
                                <th>Question</th>
                                <th>Correct Answer</th>
                                @foreach($quiz_details as $quiz_detail)
                                @foreach($quiz_tables as $quiz_table)
                                  @if($quiz_table->id == $quiz_detail->quiz_table_id && $quiz_table->publish == '0')
                                     <th>Action</th> 
                                  @break    
                                  @endif
                                @endforeach
                                @break
                                @endforeach  
                                
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($quiz_details))
                          @foreach($quiz_details as $quiz_detail)
                            <tr>
                                <td>{{ucfirst($quiz_detail->question_number)}}</td>
                                <td><strong>{{ucfirst($quiz_detail->question)}}</strong>
                                    <table>
                                        <tr>
                                            <td>A: {{ucfirst($quiz_detail->option_A)}}</td> 
                                            <td>B: {{ucfirst($quiz_detail->option_B)}}</td>
                                        </tr>
                                        <tr>
                                            <td>C: {{ucfirst($quiz_detail->option_C)}}</td> 
                                            <td>D: {{ucfirst($quiz_detail->option_D)}}</td>
                                        </tr>    
                                    </table>

                                </td>
                        
                                @if($quiz_detail->correct_answer == $quiz_detail->option_A)
                                  <td><?php echo 'A'; ?></td> 
                                @elseif($quiz_detail->correct_answer == $quiz_detail->option_B)
                                  <td><?php echo 'B'; ?></td>
                                @elseif($quiz_detail->correct_answer == $quiz_detail->option_C)
                                  <td><?php echo 'C'; ?></td>
                                @elseif($quiz_detail->correct_answer == $quiz_detail->option_D)
                                  <td><?php echo 'D'; ?></td>
                                @else  
                                <td><?php echo ''; ?></td>
                                @endif
                              
                                <td>
                                  @foreach($quiz_tables as $quiz_table)
                                  @if($quiz_table->id == $quiz_detail->quiz_table_id && $quiz_table->publish == '0')
                                  <button type="button" class="btn btn-primary editQuize gettingform" data-toggle="modal" data-target="#editQuestion" rel='/api/v2/quiz-question-editform/<?=$quiz_detail->id?>' id="<?=$quiz_detail->id?>"  data="editform" >
                                            Edit
                                  </button>
                                   
                                      <a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$quiz_detail->id}})" 
                                      data-target="#DeleteModal" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                  @break    
                                  @endif
                                  @endforeach    
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

      <div class="modal" id="importQuizDetail">
        <div class="modal-dialog">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Import Questions</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
          
            @if(!empty($request['quiz_table_id']))
            <form action="{{url(session("role").'/import-quiz-detail/'.$quize_id)}}" method="POST"  enctype="multipart/form-data">
            @else
            <form action="{{url(session("role").'/import-quiz-detail/'.$quize_id)}}" method="POST"  enctype="multipart/form-data">
            @endif  
            {{ csrf_field()}}
              <div class="form-group">
                  <label>Select excel file to upload <span class="start">*</span></label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" name="file" id="inputGroupFile02">
                    <label class="custom-file-label" for="customFile">Choose file</label>
                  </div>
              </div>
              <button type="submit" class="btn btn-primary float-left">Upload</button>
              <br>
              <a href="{{url('/sample/Quiz_with_Question.xlsx')}}" class="float-right">Download Sample</a>
            </form>


            </div>

            <!-- Modal footer -->
           

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

       <div class="modal" id="addQuestion">
        <div class="modal-dialog">
          <div class="modal-content" id = "addeditform">
          </div>
        </div>
      </div>
      <div class="modal" id="editQuestion">
        <div class="modal-dialog">
          <div class="modal-content" id = "editform">
          </div>
        </div>
      </div>
      <div class="modal" id="addPublish">
        <div class="modal-dialog">
          <div class="modal-content" id = "addPublishForm">
          </div>
        </div>
      </div>
@endsection
@section('scripts')
<script> 
$(document).ready( function () {
    $('#dataTable').DataTable();
} );
@if (count($errors) > 0)
    $('#addClass').modal('show');
    $('#importQuizDetail').modal('show');
@endif
function deleteData(id)
{
    var id = id;
    var url = '{{url(session("role").'/quiz-detail')}}';
    url = url+'/'+id;
    $("#deleteForm").attr('action', url);
}

function formSubmit()
{
    $("#deleteForm").submit();
}
$(document).ready(function() {
    $('#importQuizDetail').on('hidden.bs.modal', function(){
      $('.custom-file-label').text('');
     });
     $('#inputGroupFile02').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
    
    $('#quiz_table_id').change(function(){
        var quizeId= $(this).val();
        var url = '{{url(session("role")."/quiz-detail/addQuestion")}}';
        url = url+'/'+quizeId;
        //alert(quizeId);
        $('#getQuiz').attr('href', url);
        //Quiz_form
    })
});

$('body').on('click', '.questionSubmitMoreButton', function() {
  event.preventDefault();
  // Get form
  var more='More';
  var id =$('#editId').val();
  var form = $('.eschoolForm')[0];
  // Create an FormData object
  var data = new FormData(form);
  url = $(form).attr('action');
  callSubmit(id,data,url,more);
  //response = ajaxCallMultipartForm(url, data);
});

$('body').on('click', '.questionSubmitButton', function() {
  event.preventDefault();
  // Get form
  var more='';
  var id =$('#editId').val();
  var form = $('.eschoolForm')[0];
  // Create an FormData object
  var data = new FormData(form);
  url = $(form).attr('action');
  callSubmit(id,data,url,more);
  //response = ajaxCallMultipartForm(url, data);
});

function callSubmit(id,data,url,more){
    
      $.ajax({
      type: "POST",
      enctype: 'multipart/form-data',
      url: url,
      data: data,
      processData: false,
      contentType: false,
      async: true,
      headers: { 'Authorization':$('#authToken').val()},
      cache: false,
      timeout: 600000, 
            success: function(data) {
                if(data.success)
                {

                  toastr.success(data.message);
                  window.setTimeout(function() {
                      if(more){
                          $('#addother').show();
                          $("#addQuestionres").find('input:text, select, textarea').val('');
                          $('form.quiz_table_id_reset select').trigger("change"); //Line2
                      }else{
                         window.location = $('.eschoolForm').attr('rel');
                      }
                }, 3000);

                }else{
                  hendleError(data);
                }
            },
            error: function(e) {
                console.log("ERROR : ", e);
                hendleError(e.responseJSON);
            }
          });
          return response;
}

</script>
@endsection