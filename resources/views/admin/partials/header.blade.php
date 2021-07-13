 <!-- Main Header -->
 <header class="main-header">

     <!-- Logo -->
     <a href="{{url('/admin/dashboard')}}" class="logo">
         <!-- mini logo for sidebar mini 50x50 pixels -->
         <span class="logo-mini">Recess</span>
         <!-- logo for regular state and mobile devices -->
         <span class="logo-lg"><b>Recess</b> App</span>
     </a>

     <!-- Header Navbar -->
     <nav class="navbar navbar-static-top" role="navigation">
         <!-- Sidebar toggle button-->
         <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
             <span class="sr-only">Toggle navigation</span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
         </a>
         <!-- Navbar Right Menu -->
         <div class="navbar-custom-menu">
             <ul class="nav navbar-nav">
                 <!-- User Account Menu -->
                 <li class="dropdown user user-menu">
                     <!-- Menu Toggle Button -->
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                         <!-- The user image in the navbar-->
                         <img src="{{asset('images/app_icon.png')}}" class="user-image" alt="User Image">
                         <!-- hidden-xs hides the username on small devices so only the image appears. -->
                         <span class="hidden-xs">ADMIN</span>
                     </a>
                     <ul class="dropdown-menu">
                         <!-- The user image in the menu -->
                         <li class="user-header">
                             <img src="{{asset('images/app_icon.png')}}" class="img-circle" alt="User Image">

                             <p>
                                 Admin - {{ env('APP_NAME') }}
                                 <small>{{Carbon\Carbon::now()->format('Y')}}</small>
                             </p>
                         </li>

                         <!-- Menu Footer-->
                         <li class="user-footer">
                             <div class="pull-left">
                                 <a href="{{url('admin/change-password')}}" class="btn btn-default btn-flat">Change
                                     Password</a>
                             </div>
                             <div class="pull-right">
                                 <a class="btn btn-primary" href="{{ route('logout') }}" onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();">
                                     {{ __('Logout') }}
                                 </a>

                                 <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                     style="display: none;">
                                     @csrf
                                 </form>
                             </div>
                         </li>
                     </ul>
                 </li>
             </ul>
         </div>
     </nav>
 </header>