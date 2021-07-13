
@extends('admin.layouts.app')
@section('body')
  <div class="content-wrapper">
    <div class="content">
      <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
          <ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#home">Email</a></li>
  <li><a data-toggle="tab" href="#menu1">Push</a></li>
</ul>
<div class="tab-content">
  <div id="home" class="tab-pane fade in active">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Compose New Message</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="form-group">
          <input class="form-control" placeholder="To:">
        </div>
        <div class="form-group">
          <input class="form-control" placeholder="Subject:">
        </div>
        <div class="form-group">
              <textarea class="form-control"></textarea>
        </div>
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
        <div class="pull-right">
          <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
        </div>
        <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
      </div>
      <!-- /.box-footer -->
    </div>
  </div>
  <div id="menu1" class="tab-pane fade">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Send push</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="form-group">
          <input class="form-control" placeholder="To:">
        </div>
        <div class="form-group">
              <textarea class="form-control"></textarea>
        </div>
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
        <div class="pull-right">
          <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
        </div>
        <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
      </div>
      <!-- /.box-footer -->
    </div>
  </div>
</div>
        </div>
        <div class="col-md-1"></div>

      </div>
    </div>
  </div>
@endsection
