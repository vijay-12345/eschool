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
               <h5 class="title">Add School Form</h5>
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
               <form action="{{url('/api/v2/signup')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url(session("role").'/school')}}'>
               {{ csrf_field()}}
               <input type="hidden" name="school_id" value="{{$schoolId}}" />
               <input type="hidden" name="role" value="{{$role}}" />
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Name <span class="start">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter School Name" name="name" value="{{ old('name') }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>School Url </label>
                        <input type="text" class="form-control" placeholder="Enter School Url" name="school_url" value="{{ old('school_url') }}">
                     </div>
                  </div>
                  
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Phone No <span class="start">*</span></label>
                        <input type="text" class="form-control" name="phone_no" placeholder="Enter School Phone No" value="{{ old('phone_no') }}">
                     </div>
                  </div>
                  <div class="col-md-6 ">
                     <div class="form-group">
                        <label>Email <span class="start">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Enter School Email" value="{{ old('email') }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Login Id <span class="start">*</span></label>
                        <input type="text" class="form-control" name="login_id" placeholder="Enter School Login id" value="{{ old('login_id') }}">
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
                        <label>Logo </label>
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="image[]" accept=".jpg,.png,.jpeg">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                          </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Address <span class="start">*</span></label>
                        <textarea rows="4" cols="80" class="form-control" name="address" placeholder="Enter School's address" >{{ old('address') }}</textarea>
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
   
   } );
   // $(function() {
   //   $('#datetimepicker1').datetimepicker({
   //     allowInputToggle: true
   //   });
   
   // });
</script>
@endsection