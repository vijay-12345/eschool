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
                <h4 class="card-title"> School Details </h4>
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
                <form action="{{url('/api/v2/useredit')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url(session("role").'/school')}}'>
                <?php $field_hidden = 'none'; ?>
                @if(Auth::guard('admin')->check())
                  <?php $field_hidden = ''; ?>
                  @endif
                  <div style="display:<?php echo $field_hidden;  ?>">
                  <label for="class">Parent School<span class="start">*</span></label>
                  <select class="form-control" name='parent_id'>
                      <!--<option value="">Select Parent School</option>-->
                      <option value="">Select Parent School</option>
                      <option value="0">No Parent</option>
                      @foreach($data['schools'] as $school) 
                          <option value="{{$school->id}}" {{ ( $school->id == $data['parent_id']) ? 'selected' : '' }}>{{ucfirst($school->name)}}</option>
                      @endforeach
                  </select>
                </div>                

                <!-- <input type="hidden" name="_method" value="PUT"> -->
                <input type="hidden" name="school_id" value="{{$data['schoolId']}}" />
                <input type="hidden" name="role" value="{{$data['role']}}" />
                <input type="hidden" name="id" value="{{$data['id']}}" />
                {{ csrf_field()}}
                <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Name <span class="start">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter School Name" name="name" value="{{ $data['name'] }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>School Url </label>
                        <input type="text" class="form-control" placeholder="Enter School Url" name="school_url" value="{{ $data['school_url'] }}">
                     
                      </div>
                  </div>
                  
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Phone No <span class="start">*</span></label>
                        <input type="text" class="form-control" name="phone_no" placeholder="Enter School Phone No" value="{{ $data['phone_no'] }}">
                     </div>
                  </div>
                  <div class="col-md-6 ">
                     <div class="form-group">
                        <label>Email <span class="start">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Enter School Email" value="{{ $data['email'] }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Login Id <span class="start">*</span></label>
                        <input type="text" class="form-control" name="login_id" placeholder="Enter School Login id" value="{{ $data['login_id'] }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Password <span class="start">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Set Password"  value="{{ $data['password'] }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Logo </label>
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="image[]" accept=".jpg,.png,.jpeg">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                          
                          </div>
                          <image src="{{ $data['logo_url'] }}" style="width:70px;Height:70px"/>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Address <span class="start">*</span></label>
                        <textarea rows="4" cols="80" class="form-control" name="address" placeholder="Enter School's address" >{{ $data['address'] }}</textarea>
                     </div>
                  </div>
               </div>
                  <div class="row justify-content-center">
                      <div class="col-md-3">
                      <a class="btn btn-primary btn-block" href="{{url(session("role").'/student')}}">Back</a>
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
</script>
@endsection