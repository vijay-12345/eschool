@extends('BackEnd/layouts.master')
@section('title')
 Time Table Management | School App
@endsection
@section('content')


<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> Time Table Management</h4>
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
              <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addClass" rel='/api/v2/time-table-form'  data="timetableform" >
  Add Time Table
</button>
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <!-- <th>Label Name</th> -->
                                <th>Type</th>
                                <th>Class</th>
                                <th>Title</th>
                                <th>Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($allData))
                          @foreach($allData as $attachment)
                            <tr>
                                <td><?= $id; ?></td>
                                <td>{{ucfirst($attachment['type'])}}</td>
                                <td>{{$attachment['class_section']['class_name']}}-{{$attachment['class_section']['section_name']}}</td>
                                <td>{{$attachment['content']}}</td>
                                <td>
                                @if(!empty($attachment['file_url']['file_url']))
                                <a href="{{$attachment['file_url']['file_url']}}" download="{{$attachment['file_url']['file_url']}}" class="btn btn-primary a-btn-slide-text">
                                <i class="fa fa-download"></i> Download</span>            
                                </a>
                                @endif
                                <a> 
                                <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addClass" rel='/api/v2/time-table-form?id={{$attachment['id']}}'  data="timetableform" ><i class="fa fa-edit"></i>Edit</button></a>                      

        <a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$attachment['id']}})" 
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

      <div class="modal" id="addClass">
        <div class="modal-dialog">
          <div class="modal-content" id='timetableform'>
           
          </div>
        </div>
      </div>
      <div id="DeleteModal" class="modal fade text-danger" role="dialog">
        <div class="modal-dialog ">
          <!-- Modal content-->
          <form action="" id="deleteForm" method="post">
              <div class="modal-content">
                  <div class="modal-header bg-danger">
                      <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
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
    var url = '{{url(session("role").'/timetable')}}';
    url = url+'/'+id;
    $("#deleteForm").attr('action', url);
}
function formSubmit()
{
    $("#deleteForm").submit();
}
$(document).ready(function() {
    $('#addClass').on('hidden.bs.modal', function(){
        $(this).find('form')[0].reset();
     });
});
</script>
@endsection