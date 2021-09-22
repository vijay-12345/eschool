@extends('BackEnd/layouts.master')
@section('title')
Session Attendance Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Session Attendees View</h4>
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

                <a href="{{ URL::previous() }}" class="btn btn-warning"> <i class="fas fa-arrow-left"></i> Go Back</a>
                <div class="table-responsive">
                
                <table id="dataTable" class="display">
                  @csrf
                        <thead>
                            <tr>
                                <th style="padding-right:0.001px">S.No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Roll No</th>
                                
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($session_attendances))
                          @foreach($session_attendances as $session_attendance)
                            <tr>
                                <td><?= $id; ?></td>
                                @foreach($students as $student)
                                  @if($student->id == $session_attendance->user_id)
                                    <td>
                                      @if($student->isProfilePic == '1')
                                        <img src="{{$student->profile_pic_url}}" alt="logo" width="50" style="border-radius: 100%;">                          
                                      @elseif($student->isProfilePic == '0')
                                         <span class="glyphicon glyphicon-user"></span>
                                      @endif
                                    </td>  
                                    <td>{{ucfirst($student->name)}}</td>
                                    <td>{{ucfirst($student->roll_no)}}</td>
                                  @endif
                                @endforeach
                                       
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

@endsection
@section('scripts')
<script>
    $(document).ready( function () {
    $('#dataTable').DataTable();
} );

</script>
@endsection