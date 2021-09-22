@extends(  Auth::guard('teacher')->check() ?   'BackEnd/teacherLayouts.master' : 'BackEnd/layouts.master')
@section('title')
Notice Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> Notification Management</h4>
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
              @if(!Auth::guard('teacher')->check())
              <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addNotification" rel='/api/v2/notice-form'  data="addeditform" >
                Add Notice
              </button>
              @endif
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Message</th>
                                @if(!Auth::guard('teacher')->check())
                                <th>Actions</th>
                                @endif
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($notices))
                          @foreach($notices as $notice)
                            <tr>
                                <td><?= $id; ?></td>
                                <td>{{ucfirst($notice->type)}}</td>
                                <td>{{$notice->title}}</td>
                                <td>{{$notice->message}}</td>

                                <td>
                                @if(!Auth::guard('teacher')->check())
                                <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addNotification" rel='/api/v2/notice-form?id=<?=$notice->id?>'  data="addeditform" >
                                        Edit Notice
                                </button>
                                <a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$notice->id}})" 
                                data-target="#DeleteModal" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>
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

      <div class="modal" id="addNotification">
        <div class="modal-dialog">
          <div class="modal-content" id = "addeditform">
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
    var url = '{{url(session("role").'/notice')}}';
    
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