@extends(  Auth::guard('teacher')->check() ?   'BackEnd/teacherLayouts.master' : 'BackEnd/layouts.master')
@section('title')
Session Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> Session Management</h4>
              </div>
              @if(Session::has('success_message'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{Session::get('success_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif

              <form action="{{url(session("role").'/session')}}" method="GET"  enctype="multipart/form-data">
                @csrf  
                <label for="from" style="padding-left:10px;padding-right:10px;">From</label>
                <input type="text" id="datepicker1" name="from" value="{{$request->from}}">
                <label for="to" style="padding-left:20px;padding-right:10px;">To</label>
                <input type="text" id="datepicker2" name="to" value="{{$request->to}}">
                <button type="submit" class="btn btn-primary" value="Filter" style="margin-left:20px;">Filter</button>
              </form> 

              <div class="card-body text-right">
              @if(Auth::guard('teacher')->check())
              <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addSession" rel='/api/v2/session-form'  data="addeditform" >
                Add Session
              </button>
              @endif
              
                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Teacher Name</th>
                                <th>Class Name</th>
                                <th>Subject Name</th>
                                <th>Topic</th>
                                <th>Online Class Url</th>
                                <th>Meeting Id</th>
                                <th>Password</th>
                                <th>Content</th>
                                <th>Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($sessions))
                          @foreach($sessions as $session)
                            <tr>
                                <td><?= $id; ?></td>
                                <td>{{ucfirst($session->teacher_name)}}</td>
                                <td>{{ucfirst($session->class_name.'-'.$session->section_name)}}</td>
                                <td>{{ucfirst($session->subject_name)}}</td>

                                <td>
                                    <table>
                                        <tr>
                                            <td><b>Topic: {{ucfirst($session->topic)}}</b></td>
                                            <?php $date = Carbon\Carbon::parse($session->date); ?>
                                            <?php $date_month = $date->format('m'); ?>
                                            <?php $dateObj   = DateTime::createFromFormat('!m', $date_month); ?>
                                            <?php $date_monthName = $dateObj->format('F') ?>
                                            <?php $today_date_time = date("Y-m-d H:i:s"); ?>
                                            @if($session->start_time >= $today_date_time)
                                              <td style="color:blue">
                                            @else
                                              <td style="color:green">  
                                            @endif
                                              <b>Date: {{$date->format('d').' '.$date_monthName.','.$date->format('Y')}}</b></td>  
                                        </tr>
                                        <?php $start = Carbon\Carbon::parse($session->start_time); ?>
                                        <?php $start_month = $start->format('m'); ?>
                                        <?php $dateObj   = DateTime::createFromFormat('!m', $start_month); ?>
                                        <?php $start_monthName = $dateObj->format('F') ?>
                                        <?php $end = Carbon\Carbon::parse($session->end_time); ?>
                                        <?php $end_month = $end->format('m'); ?>
                                        <?php $dateObj   = DateTime::createFromFormat('!m', $end_month); ?>
                                        <?php $end_monthName = $dateObj->format('F') ?>
                                        <tr>
                                            <td>Start At: {{$start->format('d').' '.$start_monthName.','.$start->format('Y').'       '  .$start->format('h:ia')}}</td> 
                                            <td>End At: {{$end->format('d').' '.$end_monthName.','.$end->format('Y').'       '  .$end->format('h:ia')}}</td>
                                        </tr>    
                                    </table>
                                </td>

                                <td>{{ucfirst($session->online_class_url)}}</td>
                                <td>{{ucfirst($session->meeting_id)}}</td>
                                <td>{{ucfirst($session->password)}}</td>
                                <td>{{ucfirst($session->content)}}</td>

                                
                                <td>
                                @if(Auth::guard('teacher')->check())
                                <button type="button" class="btn btn-primary gettingform" data-toggle="modal" data-target="#addSession" rel='/api/v2/session-form?id=<?=$session->id?>'  data="addeditform" >
                                        Edit Session 
                                </button>
                                @endif

                                <a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$session->id}})" 
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

      <div class="modal" id="addSession">
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
    var url = '{{url(session("role").'/session')}}';
    
    url = url+'/'+id;
    console.log(url);
    $("#deleteForm").attr('action', url);
}

function formSubmit()
{
    $("#deleteForm").submit();
}
$('#datepicker1').datepicker({
                  format: 'yyyy-mm-dd'
                });
$('#datepicker2').datepicker({
                  format: 'yyyy-mm-dd'
                });
</script>
@endsection