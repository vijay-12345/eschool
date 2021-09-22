<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo url('/') ?>/adminAssets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo url('/') ?>/adminAssets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="<?php echo url('/') ?>/adminAssets/css/style.css">

    <title>ESchool</title>
</head>
<body>
    <div id="home" class="container-fluid">
         <div class="container">
             <div class="row">
                 <div class="col-sm-4 login-box">
                     <div class="title-box">
                         <h2>{{ ucfirst($school_name) }} Login</h2>
                         <p>Please enter  your Login details !</p>
                     </div>
                     @if(Session::has('error_message'))
                     <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{Session::get('error_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                     <form method="POST" action="{{ url('/admin') }}">  @csrf
                        @if($role == 'teacher' || $role =='student')
                        <div class="row">
                                <select class="form-control" name="school_id">
                                  <option value="">Select School </option>
                                  @foreach($schools as $school)
                                  <option value="{{$school->id}}">{{$school->name}}</option>
                                  @endforeach
                              </select>
                        </div>
                        @endif
                        <div class="row">
                            <input name="role"  type="hidden" value='{{$role}}'>
                            <input name="login_id" id="login_id" type="text" placeholder="Enter Email" class="form-control inpt-sm">
                        </div>
                        <div class="row">
                            <input name="password" id="password" type="password" placeholder="Enter Password" class="form-control inpt-sm">
                        </div>
                        <div class="row chk-lab">
                            <div class="col-sm-6">
                                <input type="checkbox"> <label>Remember me</label>
                            </div>
                            <div class="col-sm-6 colkd">
                                <a>Forget Password ?</a>
                            </div>
                        </div>
                        <div class=" submot-row">
                           <button type="submit" class="btn btn-sm btn-success">Sign In</button>
                        </div>
                    </form>
                 </div>
             </div>
         </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
           <div class="coy-info">
               <p>Designed by <a href="https://www.smarteyeapps.com">Smarteyeapps.com</a></p>
               
           </div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
        
        </div>
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="<?php echo url('/') ?>/adminAssets/js/core/jquery.min.js"></script>
        <script src="<?php echo url('/') ?>/adminAssets/js/core/popper.min.js"></script>
        <script src="<?php echo url('/') ?>/adminAssets/js/core/bootstrap.min.js"></script>
        <script src="<?php echo url('/') ?>/adminAssets/js/script.js"></script>
</body>

</html>