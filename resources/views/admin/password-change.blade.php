@extends('admin.layouts.app')
@section('body')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Change Password
        </h1>
      <ol class="breadcrumb">
        <li><a href="{{url('/home')}}"><i class="fa fa-list"></i> Home</a></li>
      </ol>
    </section>

    <section class="content container-fluid">
        <div class="row">
            <div class="col col-md-1"></div>
            <div class="col col-md-10">
                <form action="{{url('/admin/change-password')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Fill the details</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label>Old password</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                    <input type="password" class="form-control" placeholder="Enter Old password" name="old_password" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Old password</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                    <input type="password" class="form-control" placeholder="Enter New password" name="password" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Confirm password</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                    <input type="password" class="form-control" placeholder="Confirm New password" name="new_password" required="required">
                                </div>
                            </div>
                        </div> 
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col col-md-1"></div>
        </div>

    </section>
</div>
@endsection

@section('script_extra')
<script>
    @if(Session::has('error'))
        alert("{{Session::get('error')}}")
    @endif
    $(function(){
        $(".custom").click(function(){
            var target = $(this).data("work");
            var x = document.getElementById(target);
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        });

        $('#datepicker').datepicker({
            autoclose: true
        });
        CKEDITOR.replace('editor1');
    });
</script>
@endsection
