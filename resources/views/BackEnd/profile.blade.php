@extends('BackEnd/teacherLayouts.master')

@section('title')
Profile Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
             
              <div class="card-header">
                <h3 class="card-title"> Profile Management</h3>
              </div>
              <div class="card-body">
                <div class="row">
                <div class="col-md-4">
                @if($data['profile_pic_url'] !='')
                  <img src="{{$data['profile_pic_url']}}" alt="logo" width="100" height="100" style="border-radius:100%;">                          
                  @else
                     <span class="glyphicon glyphicon-user"></span>
                  @endif
                </div>  
              

              @if(Session::has('success_message'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{Session::get('success_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif
              
                <div class="col-md-4">
                  <h5><span class=" glyphicon glyphicon-book"></span> Class and Subjects:</h5>
                <table id="dataTable"   style="margin-bottom: 3em;">  
                    <tr>
                        <th >Class Name</th>
                        <th>Subject</th>
                    </tr>
                
                    @if(!empty($data))
                    @foreach($data['teacher_class_subject'] as $teacher_class_subject)
                    <tr>
                      <td>
                        {{$teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name']}}
                      </td>
                      <td>
                        {{$teacher_class_subject['subject_name']}}
                      </td>
                    </tr>
                    @endforeach
                    @endif
                      
                </table>
                </div>
              </div>
                <div class="row">
                  <div class="col-md-4"></div>
                <div class="col-md-4">
                <h5><span class="glyphicon glyphicon-user"></span> Details: </h5>
                
                <table id="dataTable2">
                          <tr>
                            <th>Name</th>
                            <td>{{$data['name']}}</td>                            
                          </tr>
                          <tr>
                            <th>School Name</th>
                            @foreach($schools as $school)
                              @if($school->id == $data['school_id'])
                                <td>{{$school->name}}</td>
                              @endif
                            @endforeach                            
                          </tr>
                          <tr>
                            <th>Login Id</th>
                            <td>{{$data['login_id']}}</td>
                          </tr>
                          <tr>
                            <th>Password</th>
                            <td>{{$data['password']}}</td>
                          </tr>
                          <tr>
                            <th>Email</th>
                            <td>{{$data['email']}}</td>
                          </tr>
                          <tr>
                            <th>Phone No</th>
                            <td>{{$data['phone_no']}}</td>
                          </tr>
                          <tr>
                            <th>Employee Id</th>
                            <td>{{$data['employee_id']}}</td>
                          </tr>
                          <tr>
                            <th>Date of Joining</th>
                            <?php $date = Carbon\Carbon::parse($data['date_of_joining']); ?>
                            <?php $date_month = $date->format('m'); ?>
                            <?php $dateObj   = DateTime::createFromFormat('!m', $date_month); ?>
                            <?php $date_monthName = $dateObj->format('F') ?>
                            <td>{{$date->format('d').' '.$date_monthName.', '.$date->format('Y')}}</td>
                          </tr>
                          <tr>
                            <th>Date of Birth</th>
                            <?php $date = Carbon\Carbon::parse($data['dob']); ?>
                            <?php $date_month = $date->format('m'); ?>
                            <?php $dateObj   = DateTime::createFromFormat('!m', $date_month); ?>
                            <?php $date_monthName = $dateObj->format('F') ?>
                            <td>{{$date->format('d').' '.$date_monthName.', '.$date->format('Y')}}</td>
                          </tr>
                          <tr>
                            <th>Address</th>
                            <td>{{$data['address']}}</td>
                          </tr> 
                    </table>  
                 </div>
               </div>
              </div>

            </div>
            <div align="center">
            <a href="{{url(session("role").'/change-password/'.$data['id'])}}" 
              class="btn btn-primary">Change Password</a>
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
    $('#dataTable2').DataTable();

</script>
@endsection