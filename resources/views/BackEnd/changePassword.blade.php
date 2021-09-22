@extends('BackEnd/teacherLayouts.master')
@section('title')
Change Password Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
</div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5 class="title">Change Password</h5>
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
                <form action="{{url('/api/v2/change_password')}}" method="POST" class="eschoolForm" rel='{{url(session("role").'/profile')}}'>
                {{ csrf_field()}}  
                <input type="hidden" name="role" value="{{session("role")}}" />
                <input type="hidden" name="user_id" value="{{$user_detail['id']}}" />
                <div align="center">               
                  <div class="form-group">
                    <div class="col-md-4 text-center">
                    <label>Old Password</label><input type="password" class="form-control" name="old_password" placeholder="" >
                    </div>
                  </div>
                    
                 <div class="form-group">
                    <div class="col-md-4 text-center">
                        <label>New Password</label><input type="password" class="form-control"  name="new_password" placeholder="">
                    </div>
                 </div>  
                   
                 <div class="form-group">
                    <div class="col-md-4 text-center">
                        <label>Confirm Password</label><input type="password" class="form-control" name="confirm_password" placeholder="">
                    </div>
                 </div>
                 <div>
                       <div class="col-md-1">
                        <button class="btn btn-primary btn-block submitButton">Submit</button>
                       </div>
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

</script>
@endsection