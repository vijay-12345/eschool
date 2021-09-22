@extends('BackEnd/layouts.master')
@section('title')
 Teacher Management | School App
@endsection
@section('content')

<?php 
foreach($teacher_class_subject as $slectedClassSubject){
  $selectedClass[]=$slectedClassSubject['subject_class_id']; 
}
?>
<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> Teacher Details</h4>
              </div>
              <div class="card-body>
                <div class="table-responsive">
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
                <form action="{{url('/api/v2/useredit')}}" method="POST" class="eschoolForm" rel='{{url(session("role").'/teacher')}}'>
                
                <!-- <input type="hidden" name="_method" value="PUT"> -->
                <?php $field_hidden = 'none'; ?>
                @if(Auth::guard('admin')->check())
                <?php $field_hidden = ''; ?>
                @endif
                <div style="display:<?php echo $field_hidden;  ?>">
                <label for="class">School<span class="start">*</span></label>
                <select class="form-control" name='school_id'>
                    <option value="">Select type</option>
                    @foreach($schoolList as $school)
                      @if($school->id == $school_id)
                        <option value="{{$school->id}}" selected>{{ucfirst($school->name)}}</option>
                      @else
                        <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                      @endif  
                    @endforeach
                </select>
                </div>

                <input type="hidden" name="role" value="{{$role}}" />
                <input type="hidden" name="id" value="{{$id}}" />
                {{ csrf_field()}}
                
                <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Name <span class="start">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter teacher Name" name="name" value="{{ $name }}">
                      </div>
                    </div>
                    <div class="col-md-6 ">
                      <div class="form-group">
                      <label>Email <span class="start">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Enter teacher Email" value="{{  $email  }}">
  
                        </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Login Id <span class="start">*</span></label>
                        <input type="text" class="form-control" name="login_id" placeholder="Enter teacher Login id" value="{{  $login_id }}">
                      
                       </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Password <span class="start">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Set Password"  value="{{  $password }}" id="password">
                        <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password" id="show__hide_password"></span>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Employee Id <span class="start">*</span></label>
                        <input type="text" class="form-control" name="employee_id" placeholder="Enter employee id" value="{{$employee_id }}">
                       </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Mobile No <span class="start">*</span></label>
                        <input type="text" class="form-control" name="phone_no" placeholder="Enter mobile No" value="{{ $phone_no}}">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                   <div class="col-md-6">
                    <div class="form-group">
                            <label>Joining Date </label>
                            <div class='input-group date' id="datetimepicker1">
                                <input type='text' class="form-control" name="date_of_joining" value="{{  $date_of_joining  }}" id="date_of_joining"/>
                                <!-- <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date of Birth </label>
                            <div class='input-group date' id="datetimepicker2">
                                <input type='text' class="form-control" name="dob" value="{{ $dob }}" id="dob" min="1900-01-01" max=""/>
                                <!-- <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span> -->
                            </div>
                        </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Profile Pic <span class="start"></span></label>
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" name="image[]"  id="profilepic">
                              <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                            <?php   if($profile_pic_url != null){ ?>
                                  <a href="<?= $profile_pic_url; ?>" download="<?= $profile_pic_url; ?>">
                                Download</a>
                             <?php } ?>
                        </div>
                    </div>

                  <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                          <label>Address <span class="start">*</span></label>
                          <textarea rows="4" cols="80" class="form-control" name="address" placeholder="Enter teacher's address" >{{  $address  }}</textarea>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">

                        <label>Class-Section Name <span class="start">*</span></label>
                        <select class="form-control" name="teacherClassSection[]" multiple="multiple" id="slectedClasses">
                         
                          @if(!empty($classSections))
                            <?php $classSections = $classSections->toArray(); ?> 
                           @foreach($classSections as $classSection)
                            <?php $bool =1; ?>
                              @foreach($slectedClassSubjects as $selectedClass_id)
                              @if($classSection['id'] == $selectedClass_id)
                              <?php $bool =0; ?>
                              <option value="<?= $classSection['id']; ?>" selected><?= $classSection['class_name'].'-'.$classSection['section_name'].'-'.$classSection['subject_name']; ?></option>
                          
                               
                               @endif
                               @endforeach
                            @if($bool == 1)
                                <option value="<?= $classSection['id']; ?>"><?= $classSection['class_name'].'-'.$classSection['section_name'].'-'.$classSection['subject_name']; ?></option>
                            @endif    
                           @endforeach
                           @endif
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row justify-content-center">
                    <div class="col-md-3">
                      <a class="btn btn-primary btn-block" href="{{url(session("role").'/teacher')}}">Back</a>
                     </div>
                     <div class="col-md-3">
                      <button class="btn btn-primary btn-block submitButton">Update</button>
                     </div>
                  </div>
                </form>
                  </div>
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
$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});

</script>
@endsection