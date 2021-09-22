
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="<?php echo url('/') ?>/adminAssets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="<?php echo url('/') ?>/adminAssets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>
  @yield('title')
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <!-- CSS Files -->
  <link href="<?php echo url('/') ?>/adminAssets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?php echo url('/') ?>/adminAssets/css/now-ui-dashboard.css?v=1.5.0" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="<?php echo url('/') ?>/adminAssets/css/dataTable.min.css" rel="stylesheet" />
  <link href="<?php echo url('/') ?>/adminAssets/demo/demo.css" rel="stylesheet" />
  <link href="<?php echo url('/') ?>/adminAssets/css/select2.css" rel="stylesheet" />
  <link href="<?php echo url('/') ?>/adminAssets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
  <link href="<?php echo url('/') ?>/adminAssets/css/toastr.css" rel="stylesheet" />
  <link href="<?php echo url('/') ?>/adminAssets/css/sumoselect.min.css" rel="stylesheet" />
  <link href="<?php echo url('/') ?>/adminAssets/css/datepicker.css" rel="stylesheet" />
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
  <link href="<?php echo url('/') ?>/adminAssets/css/jquery.datetimepicker.css" rel="stylesheet" />



  
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="orange"><!-- Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"-->
      <div class="logo">
        <a href="#" class="simple-text logo-mini">
          CT
        </a>
        <a href="#" class="simple-text logo-normal">
          School App
        </a>
      </div>
    <?php
      $directoryURI = $_SERVER['REQUEST_URI'];
      $path = parse_url($directoryURI, PHP_URL_PATH);
      $components = explode('/', $path);
      $first_part = $components[2];
      $role= session("role");
      ?>
      <div class="sidebar-wrapper" id="sidebar-wrapper">
        <ul class="nav">
          <li class="{{ $first_part== 'dashboard' ? 'active' : ''}} ">
            <a href='{{url("/$role/dashboard")}}'>
              <i class="now-ui-icons design_app"></i>
              <p>Dashboard</p>
            </a>
          </li>
          
          <li class="{{ $first_part== 'session' ? 'active' : ''}} ">
            <a href='{{url("/$role/session")}}'>
              <i class="now-ui-icons ui-1_bell-53"></i>
              <p>Session</p>
            </a>
          </li>

          <li class="{{ $first_part== 'study-material' ? 'active' : ''}} ">
            <a href='{{url("/$role/study-material")}}'>
              <i class="now-ui-icons ui-1_bell-53"></i>
              <p>Study Material</p>
            </a>
          </li>
          <li class="{{ $first_part== 'assignment' ? 'active' : ''}} ">
            <a href='{{url("/$role/assignment")}}'>
              <i class="now-ui-icons ui-1_bell-53"></i>
              <p>Assignment</p>
            </a>
          </li>
          <li  class="{{ $first_part== 'notice' ? 'active' : ''}}">
            <a href='{{url("/$role/notice")}}'>
              <i class="now-ui-icons ui-1_bell-53"></i>
              <p>Notice Board</p>
            </a>
          </li>
          <li  class="{{ $first_part== 'forum' ? 'active' : ''}}">
            <a href='{{url("/$role/forum")}}'>
              <i class="now-ui-icons ui-1_bell-53"></i>
              <p>Forum</p>
            </a>
          </li>

        </ul>
      </div>
    </div>
    <div class="main-panel" id="main-panel">
      <!-- Navbar -->

        @if($role!='admin')
          <div class='row'>
           <div class="col-md-1"></div>
           <div class="col-md-1">
              @if(Auth::guard('school')->check())
               @if(Auth::guard('school')->user()->logo_url)
                    <img src="<?=Auth::guard('school')->user()->logo_url?>" style="height:70px;width: 70px;"/>
               @endif
              @endif 
           </div>
           <div class="col-md-6">
                  <h3><u><i>{{Auth::guard($role)->user()->name}}</i></u></h3>      
            </div>
           <div class="col-md-4">
             Phone: <?=Auth::guard($role)->user()->phone_no?><br>
             @if(Auth::guard('school')->check())
              Site: <a href="<?=Auth::guard('school')->user()->school_url?>"><?=Auth::guard('school')->user()->school_url?></a>
            @endif
          </div>
        </div>
        @endif
      <nav class="navbar navbar-expand-lg navbar-transparent  bg-primary  navbar-absolute">

        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand" href="#">{{$page_title }}</a>

        </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <!-- <form>
              <div class="input-group no-border">
                <input type="text" value="" class="form-control" placeholder="Search...">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <i class="now-ui-icons ui-1_zoom-bold"></i>
                  </div>
                </div>
              </div>
            </form> -->
            <ul class="navbar-nav">
              <!-- <li class="nav-item">
                <a class="nav-link" href="#pablo">
                  <i class="now-ui-icons media-2_sound-wave"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Stats</span>
                  </p>
                </a>
              </li> -->
              <!-- <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="now-ui-icons location_world"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Some Actions</span>
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="#">Action</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <a class="dropdown-item" href="#">Something else here</a>
                </div>
              </li> -->
              <li class="nav-item">
                <a class="nav-link" href='{{url("/$role/profile")}}'>
                  Profile
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href='{{url("/$role/logout")}}'>
                  Logout
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      @yield('content')
      <footer class="footer">
        <div class=" container-fluid ">
          <nav>
            <ul>
              <li>
                <a href="https://www.creative-tim.com">
                  Creative Tim
                </a>
              </li>
              <li>
                <a href="http://presentation.creative-tim.com">
                  About Us
                </a>
              </li>
              <li>
                <a href="http://blog.creative-tim.com">
                  Blog
                </a>
              </li>
            </ul>
          </nav>
          <div class="copyright" id="copyright">
            &copy; <script>
              document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))
            </script>, Designed by <a href="https://www.invisionapp.com" target="_blank">Invision</a>. Coded by <a href="https://www.creative-tim.com" target="_blank">Creative Tim</a>.
          </div>
        </div>
      </footer>
    </div>
  </div>
  <input type="hidden" id="authToken" value="<?php echo Auth::guard($role)->user()->token ?>" />
  
  <input type="hidden" id="user_role" value="<?php echo $role ?>" />

  <input type="hidden" id="school_id" value="<?php echo Auth::guard($role)->user()->school_id ?>" />
  <!--   Core JS Files   -->
  <script src="<?php echo url('/') ?>/adminAssets/js/moment.js"></script>
  <script
  src="https://code.jquery.com/jquery-3.5.0.min.js"
  integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ="
  crossorigin="anonymous"></script>
  <script src="<?php echo url('/') ?>/adminAssets/js/jquery.datetimepicker.full.min.js"></script>
  <script src="<?php echo url('/') ?>/adminAssets/js/core/popper.min.js"></script>
  <script src="<?php echo url('/') ?>/adminAssets/js/core/bootstrap.min.js"></script>
  <!-- Chart JS -->
  <script src="<?php echo url('/') ?>/adminAssets/js/plugins/chartjs.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="<?php echo url('/') ?>/adminAssets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="<?php echo url('/') ?>/adminAssets/js/now-ui-dashboard.min.js?v=1.5.0" type="text/javascript"></script><!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->
  <script src="<?php echo url('/') ?>/adminAssets/js/dataTable.min.js"></script>
  <script src="<?php echo url('/') ?>/adminAssets/js/select2.js"></script>

  <script src="<?php echo url('/') ?>/adminAssets/js/jquery-ui.min.js"></script>
  <script src="<?php echo url('/') ?>/adminAssets/js/toastr.js"></script>
  <script src="<?php echo url('/') ?>/adminAssets/js/script.js"></script>
  <script src="<?php echo url('/') ?>/adminAssets/js/jquery.sumoselect.min.js"></script>
  <script src="<?php echo url('/') ?>/adminAssets/js/datepicker.js"></script>
  
   @yield('scripts')
</body>

</html>
