@extends('BackEnd/layouts.master')
@section('title')
 Teacher Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header"> 
                <h5 class="title">Add Teacher Form</h5>
              </div>
              <div class="card-body">
              @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                @endif
                <form action="{{url('/api/v2/signup')}}" method="POST" class="eschoolForm" rel="{{url(session("role").'/teacher')}}" id="addTeacher">
                {{ csrf_field()}}

                  <?php $field_hidden = 'none'; ?>
                  @if(Auth::guard('admin')->check())
                  <?php $field_hidden = ''; ?>
                  @endif
                  <div style="display:<?php echo $field_hidden;  ?>">
                  <label for="class">School<span class="start">*</span></label>
                  <select class="form-control" name='school_id'>
                      <option value="">Select type</option>
                      @foreach($schoolList as $school)
                        @if($school->id == $schoolId)
                          <option value="{{$school->id}}" selected>{{ucfirst($school->name)}}</option>
                        @else
                          <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                        @endif  
                      @endforeach
                  </select>
                  </div>
                  <input type="hidden" name="role" value="{{$role}}" />
                  <div class="row">
                  
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Name <span class="start">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter teacher Name" name="name" value="{{ old('teacherName') }}">
                      </div>
                    </div>
                    <div class="col-md-6 ">
                      <div class="form-group">
                      <label>Email <span class="start">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Enter teacher Email" value="{{ old('email') }}">
                      
                        </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Login Id <span class="start">*</span></label>
                        <input type="text" class="form-control" name="login_id" placeholder="Enter teacher Login id" value="{{ old('login_id') }}">
                      
                       </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Password <span class="start">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Set Password"  value="{{ old('password') }}" id="password">
                        <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password" id="show__hide_password"></span>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Employee Id <span class="start">*</span></label>
                        <input type="text" class="form-control" name="employee_id" placeholder="Enter employee id" value="{{ old('employee_id') }}">
                       </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Mobile No <span class="start">*</span></label>
                        <input type="text" class="form-control" name="phone_no" placeholder="Enter mobile No" value="{{ old('phone_no') }}">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                   <div class="col-md-6">
                    <div class="form-group">
                            <label>Joining Date <span class="start">*</span></label>
                            <div class='input-group date' id="datetimepicker1">
                                <input type='text' class="form-control" name="date_of_joining" id="date_of_joining" value="{{ old('date_of_joining') }}"/>
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
                                <input type='text' class="form-control" name="dob" value="{{ old('dob') }}" id="dob"/>
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
                              <input type="file" class="custom-file-input" name="image[]" id="profilepic">
                              <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                  </div>

                  <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                          <label>Address <span class="start">*</span></label>
                          <textarea rows="4" cols="80" class="form-control" name="address" placeholder="Enter teacher's address" >{{ old('address') }}</textarea>
                      </div>
                    </div>

                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Class-Section Name </label>
                        <select class="js-example-basic-multiple form-control" name="teacherClassSection[]" multiple="multiple">
                           @if(!empty($classSections))
                           @foreach($classSections as $classSection)
                           <option value="<?= $classSection->id; ?>"><?= $classSection->class_name.'-'.$classSection->section_name.'-'.$classSection->subject_name; ?></option>
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
                      <button class="btn btn-primary btn-block submitButton">Submit</button>
                     </div>
                  </div>
                </form>
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
$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
$('#profilepic').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
})
</script>
@endsection