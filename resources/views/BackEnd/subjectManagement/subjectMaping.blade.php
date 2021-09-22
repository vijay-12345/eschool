@extends('BackEnd/layouts.master')
@section('title')
 Subject Mapping | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> Subject Management</h4>
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
              
              <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addSubject" rel='/api/v2/subject-class-form'  data="addeditform" >
                Add Subject
              </button>
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Class-Section</th>
                                <th>Subject</th>
                                <th>Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($allClassSections))
                          @foreach($allClassSections as $allClassSection)
                            <tr>
                                <td><?= $id; ?></td>
                                <td>{{$allClassSection->class_name}}-{{$allClassSection->section_name}}</td>
                                <td>{{$allClassSection->subject_name}}</td>
                                <td>

                                    <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addSubject" rel='/api/v2/subject-class-form?id=<?=$allClassSection->id?>'  data="addeditform" > Edit </button>
                                    
                                    <a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$allClassSection->id}})" 
                                    data-target="#DeleteModal" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                  </td>
    
                            </tr>
                            <?php $id++; ?>
                            @endforeach
                            @endIf
                        </tbody>
                    </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal" id="addSubject">
        <div class="modal-dialog">
          <div class="modal-content" id = "addeditform">
            

            <!-- Modal footer -->
            <!-- <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div> -->

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
    var url = '{{url(session("role").'/mapping')}}';
    url = url+'/'+id;
    $("#deleteForm").attr('action', url);
}

function formSubmit()
{
    $("#deleteForm").submit();
}
</script>
@endsection