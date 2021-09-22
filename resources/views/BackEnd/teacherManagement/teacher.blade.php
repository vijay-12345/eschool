@extends('BackEnd/layouts.master')
@section('title')
 Teacher Management | School App
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
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> Teacher Management</h4>
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
  <a href="{{url(session("role").'/teacher/create')}}" class="btn btn-primary">Add Teacher</a>
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importStudent">
  Import Teachers
</button>
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Teacher Name</th>
                                <th>Login Id</th>
                                <th>Employee Id</th>
                                <th>Class-Section And Subject Name</th>
                                <th>Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($teachers))
                          @foreach($teachers as $teacher)
                            <tr>
                                <td><?= $id; ?></td>
                                <td>{{$teacher->name}}</td>
                                <td>{{$teacher->login_id}}</td>
                                <td>{{$teacher->employee_id}}</td>
                                <td>{{viewMoreAndLess('',explode(',',$teacher->teacher_class_subject))}}</td>
                                <td>
        <a href="{{url(session("role").'/teacher',[$teacher->id,'edit'])}}" class="btn btn-primary a-btn-slide-text">
        <i class="fa fa-edit"></i> Edit</span>            
        </a>
<a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$teacher->id}})" 
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
      <div class="modal" id="importStudent">
        <div class="modal-dialog">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Import Teachers</h4>
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

            <form action="{{url(session("role").'/importTeachers')}}" method="POST"  enctype="multipart/form-data">
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
              <a href="{{url('/sample/teacherReg.xlsx')}}" class="float-right">Download Sample</a>
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
@if (count($errors) > 0)
    $('#addClass').modal('show');
    $('#importStudent').modal('show');
@endif

function deleteData(id)
{
    var id = id;
    var url = '{{url(session("role").'/teacher')}}';
    url = url+'/'+id;
    $("#deleteForm").attr('action', url);
}

function formSubmit()
{
    $("#deleteForm").submit();
}
$(document).ready(function() {
    $('#importStudent').on('hidden.bs.modal', function(){
      $('.custom-file-label').text('');
     });
     $('#inputGroupFile02').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
});
</script>
@endsection