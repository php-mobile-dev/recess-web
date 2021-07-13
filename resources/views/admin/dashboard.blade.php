@extends('admin.layouts.app')
@section('body')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>All the statistical information will be availble here</small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3>{{$events}}</h3>

                <p>Total No of Events</p>
              </div>
              <div class="icon">
                <i class="ion ion-information"></i>
              </div>
              <a href="{{url('admin/event')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
              <div class="inner">
                <h3>{{$users}}</h3>

                <p>Total number of Users</p>
              </div>
              <div class="icon">
                <i class="ion ion-ios-contact"></i>
              </div>
              <a href="{{url('/admin/user')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3>{{$feeds}}</h3>

                <p>No of Feeds</p>
              </div>
              <div class="icon">
                <i class="ion ion-key"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
              <div class="inner">
                <h3>{{$subscribers}}</h3>

                <p>Number of Subscribed Users</p>
              </div>
              <div class="icon">
                <i class="ion ion-happy"></i>
              </div>
              <a href="{{url('/admin/user')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
      </div>
    </section>

  </div>
  <!-- /.content-wrapper -->
@endsection
@section('script_extra')
<script src="{{asset('bower_components/ckeditor/ckeditor.js')}}"></script>

  <script>
    $(function(){
      @if(Session::has('message'))
        alert("{{Session::get('message')}}");
      @endif
    });
    $('#datepicker').datepicker({
            autoclose: true
        });
    $('#datepicker1').datepicker({
        autoclose: true
    });
    CKEDITOR.replace('editor1').config.allowedContent = true;
    CKEDITOR.replace('editor2').config.allowedContent = true;

  </script>
@endsection