@extends('BackEnd/layouts.master')
@section('title')
 Student Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5 class="title">Add Student Form</h5>
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
                <form action="{{url('/api/v2/signup')}}" method="POST" class="eschoolForm" rel='{{url(session("role").'/student')}}'>
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
                        <input type="text" class="form-control" placeholder="Enter Student Name" name="name" value="{{ old('name') }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Registration No <span class="start">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Student Registration Number" name="registration_no" value="{{ old('registration_no') }}">
                      </div>
                    </div>
                 
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Class Name <span class="start">*</span></label>
                        <select class="form-control" name="class_section_id">
                           <option value="">select class and section</option>
                           @if(!empty($classSections))
                           @foreach($classSections as $classSection)
                           <option value="<?= $classSection->id; ?>"><?= $classSection->	class_name.'-'.$classSection->section_name; ?></option>
                           @endforeach
                           @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 ">
                      <div class="form-group">
                      <label>Email </label>
                        <input type="email" class="form-control" name="email" placeholder="Enter Student Email" value="{{ old('email') }}">
                      
                        </div>
                    </div>
                 
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Phone No</label>
                        <input type="text" class="form-control" name="phone_no" placeholder="Enter Student Phone No" value="{{ old('phone_no') }}">
                       </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Roll No <span class="start">*</span></label>
                        <input type="text" class="form-control" name="roll_no" placeholder="Enter Student Roll No" value="{{ old('roll_no') }}">
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Login Id <span class="start">*</span></label>
                        <input type="text" class="form-control" name="login_id" placeholder="Enter Student Login id" value="{{ old('login_id') }}">
                      
                       </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Password <span class="start">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Set Password"  value="{{ old('password') }}">
                      
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                          <label>Date of Birth <span class="start">*</span></label>
                          <div class='input-group date' id="datetimepicker1">
                              <input type='text' class="form-control" name="dob" value="{{ old('dob') }}" id="dob" min="1900-01-01" max=""/>
                              <!-- <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span> -->
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

                      <div class="col-md-6">
                        <div class="form-group">
                            <label>Address <span class="start">*</span></label>
                            <textarea rows="4" cols="80" class="form-control" name="address" placeholder="Enter student's address" >{{ old('address') }}</textarea>
                        </div>
                    </div>
                  </div>Parent's Details
                <hr>
                   <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Parent's Name</label>
                        <input type="text" class="form-control" name="parent_name" placeholder="Enter Student parent Name" value="{{ old('parent_name') }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Parent's Mob No 1 <span class="start">*</span></label>
                        <input type="number" class="form-control" name="parent_phono_no1" placeholder="Enter Student Parents Mob no 1." value="{{ old('parent_phono_no1') }}">
                      
                      
                      </div>
                    </div>
                  
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Parent's Mob No 2 </label>
                            <input type="number" class="form-control" name="parent_phono_no2" placeholder="Enter Student Parents Mob no 2." value="{{ old('parent_phono_no2') }}">
                        </div>
                    </div>
                  </div>
                   
                    
                 
                  <div class="row justify-content-center">
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
    });

    $('#profilepic').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })

// $(function() {
//   $('#datetimepicker1').datetimepicker({
//     allowInputToggle: true
//   });
  
// });
</script>
@endsection