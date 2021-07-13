@extends('admin.layouts.app')
@section('body')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Add {{isset($heading) ? $heading : ''}}
        </h1>
      <ol class="breadcrumb">
        <li><a href="{{url('/admin/'.$heading)}}"><i class="fa fa-list"></i> List</a></li>
        <li class="active">Add</li>
      </ol>
    </section>

    <section class="content container-fluid">
        <div class="row">
            <div class="col col-md-1"></div>
            <div class="col col-md-10">
                <form action="{{url('/admin/'.$heading )}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Fill the details</h3>
                        </div>
                        <div class="box-body">
                            @foreach($fields as $field => $type)
                                @if(\Request::has('parent_id'))
                                <input type="hidden" name="parent_id" value="{{ \Request::get('parent_id') }}" >
                                @endif
                                @switch($type)
                                    @case('email')
                                    <div class="form-group">
                                        <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input type="email" class="form-control" placeholder="Enter {{ ucfirst(str_replace('_', ' ', $field)) }}" name="{{$field}}" required="required">
                                        </div>
                                    </div>
                                    @break
                                    @case('switch')
                                        <div class="form-group">
                                            <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                            <select class="form-control" required="required" name="{{$field}}" >
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    @break
                                    @case('password')
                                    <div class="form-group">
                                        <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input type="password" class="form-control" placeholder="Enter {{ ucfirst(str_replace('_', ' ', $field)) }}" name="{{$field}}" required="required" id="{{$field}}">
                                            <div class="input-group-addon" >
                                                <a class="custom" data-work="{{$field}}"><i class="fa fa-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    @break

                                    @case('custom')
                                    <div class="form-group">
                                        <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        <textarea id="editor1" name="{{$field}}" rows="10" cols="80" required="required">
                                        </textarea>
                                    </div>
                                    @break

                                    @case('date')
                                    <div class="form-group">
                                        <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="datepicker" name="{{$field}}" required="required">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    @break

                                    @case('file')
                                    <div class="form-group">
                                        <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        <input type="file" name="{{$field}}">
                                        <!-- /.input group -->
                                        @if($heading == 'subtoolbox')
                                                    <p class="help-block">Please upload PNG image in 450 x 450 pixel or more in 1:1 ratio</p>
                                                @endif
                                    </div>
                                    @break

                                    @default
                                        <div class="form-group">
                                            <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                            <input type="text" class="form-control" placeholder="Enter {{ ucfirst(str_replace('_', ' ', $field)) }}" name="{{$field}}" required="required">
                                        </div>
                                @endswitch
                            @endforeach

                            
                        </div> 
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
                @if($errors)
                    @foreach($errors->all() as $error)
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endforeach
                @endif
            </div>
            <div class="col col-md-1"></div>
        </div>

    </section>
</div>
@endsection

@section('script_extra')
<script src="{{asset('bower_components/ckeditor/ckeditor.js')}}"></script>
<script>
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
