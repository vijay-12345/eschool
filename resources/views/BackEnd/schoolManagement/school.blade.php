@extends('BackEnd/layouts.master')
@section('title')
School Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> School Management</h4>
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
  <a href="{{url(session("role").'/school/create')}}" class="btn btn-primary">Add School</a>
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>School Name</th>
                                <th>School Url</th>
                                <th>Login Id</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($schools))
                          @foreach($schools as $school)
                            <tr>
                                <td><?= $id; ?></td>
                                <td>{{$school->name}}</td>
                                <td>{{$school->school_url}}</td>
                                <td>{{$school->login_id}}</td>
                                <td>{{$school->email}}</td>
                                <td>{{$school->phone_no}}</td>
                                <td>
                                  <a href="{{url(session("role").'/school',[$school->id,'edit'])}}" 
                                            class="btn btn-primary a-btn-slide-text">
                                      <i class="fa fa-edit"></i> Edit</span>            
                                  </a>
                                  
                                  <a href="javascript:;" class="btn btn-primary a-btn-slide-text" 
                                     data-toggle="modal" onclick="deleteData({{$school->id}})" 
                                     data-target="#DeleteModal" class="btn btn-xs btn-danger">
                                     <i class="fa fa-trash"></i> Delete
                                  </a>
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
              <h4 class="modal-title">Import Schools</h4>
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

            <form action="{{url(session("role").'/importSchools')}}" method="POST"  enctype="multipart/form-data">
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
              <a href="{{url('/sample/studentReg.xlsx')}}" class="float-right">Download Sample</a>
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
    $('#importSchool').modal('show');
@endif
function deleteData(id)
{
    var id = id;
    var url = '{{url(session("role").'/school')}}';
    url = url+'/'+id;
    $("#deleteForm").attr('action', url);
}

function formSubmit()
{
    $("#deleteForm").submit();
}
$(document).ready(function() {
    $('#importSchool').on('hidden.bs.modal', function(){
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