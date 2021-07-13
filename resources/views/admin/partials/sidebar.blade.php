<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel" style="background: #202020">
            <div class="pull-left image">
                <img src="{{asset('images/app_icon.png')}}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>Admin</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>


        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MENU</li>

            <li class="{{ (\Request::path() == 'admin/dashboard') ? 'active' : '' }}">
                <a href="{{url('/admin/dashboard')}}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <li class="{{ strpos(\Request::path(), 'admin/user')!==false ? 'active' : '' }}">
                <a href="{{url('admin/user')}}"><i class="fa fa-user" aria-hidden="true"></i> <span>Users</span></a>
            </li>

            <li class="{{ strpos(\Request::path(), 'activitycategory')!==false ? 'active' : '' }}">
                <a href="{{url('/admin/activitycategory')}}"><i class="fa fa-tasks" aria-hidden="true"></i>
                    <span>Category</span></a>
            </li>

            <li class="{{ strpos(\Request::path(), 'activitytype')!==false ? 'active' : '' }}">
                <a href="{{url('/admin/activitytype')}}"><i class="	fa fa-server" aria-hidden="true"></i> <span>Activity
                        Type</span></a>
            </li>

            <li class="{{ strpos(\Request::path().'/', 'admin/activity/')!==false ? 'active' : '' }}">
                <a href="{{url('/admin/activity')}}"><i class="	fa fa-signing" aria-hidden="true"></i> <span>List of
                        Activities</span></a>
            </li>

            <li class="{{ strpos(\Request::path(), 'event')!==false ? 'active' : '' }}">
                <a href="{{url('/admin/event')}}"><i class="fa fa-street-view" aria-hidden="true"></i> <span>Activities
                        Posted</span></a>
            </li>

            <li class="{{ strpos(\Request::path(), 'report')!==false ? 'active' : '' }}">
                <a href="{{url('/admin/report')}}"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    <span>Reports</span></a>
            </li>

            <li class="{{ strpos(\Request::path(), 'setting')!==false ? 'active' : '' }}">
                <a href="{{url('/admin/setting')}}"><i class="fa fa-wrench" aria-hidden="true"></i>
                    <span>Settings</span></a>
            </li>

            <li class="{{ strpos(\Request::path(), 'content')!==false ? 'active' : '' }}">
                <a href="{{url('/admin/content')}}"><i class="fa fa-file-text-o" aria-hidden="true"></i>
                    <span>Content</span></a>
            </li>

            <!-- <li class="treeview {{ strpos(\Request::path(), 'label')!==false ? 'menu-open' : '' }}" style="height: auto;">
      <a href="#">
        <i class="fa fa-share"></i> <span>Language Settings</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu" style="display: {{ (strpos(\Request::path(), 'label')!==false || strpos(\Request::path(), 'alert')!==false )? 'block' : 'none' }};">
        <li class="{{ strpos(\Request::path(), 'alert')!==false ? 'active' : '' }}"><a href="{{url('/alert')}}"><i class="fa fa-circle-o"></i>Alert Popups</a></li>
        <li class="{{ strpos(\Request::path(), 'label')!==false ? 'active' : '' }}"><a href="{{url('/label')}}"><i class="fa fa-circle-o"></i>Labels</a></li>
      </ul>
    </li> -->
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>