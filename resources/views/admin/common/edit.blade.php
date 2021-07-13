@extends('admin.layouts.app')

@section('body')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Edit {{isset($heading) ?  $heading : ''}}
        </h1>
      <ol class="breadcrumb">
        <li><a href="{{url('/admin/'.$heading)}}"><i class="fa fa-list"></i> List</a></li>
        <li class="active">Edit</li>
      </ol>
    </section>

    <section class="content container-fluid">
        <div class="row">
            <div class="col col-md-1"></div>
            <div class="col col-md-10">
                <form action="{{url('/admin/'.$heading.'/'.$obj->id)}}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="box box-primary">
                        <div class="box-header with-border">
                        <h3 class="box-title">Fill the details</h3>
                        </div>
                        <div class="box-body">
                            @foreach($fields as $field => $type)
                                @switch($type)
                                    @case('email')
                                    <div class="form-group">
                                        <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input type="email" class="form-control" placeholder="Enter {{$field}}" name="{{$field}}" required="required" value="{{ $obj->{$field} }}">
                                        </div>
                                    </div>
                                    @break
                                    @case('switch')
                                        <div class="form-group">
                                            <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                            <select class="form-control" required="required" name="{{$field}}" >
                                                <option value="">Select</option>
                                                <option value="1" {{$obj->{$field} == 1 ? 'selected="selected"' : ''}}>Yes</option>
                                                <option value="0" {{$obj->{$field} == 0 ? 'selected="selected"' : ''}}>No</option>
                                            </select>
                                        </div>
                                    @break
                                    @case('password')
                                    <div class="form-group">
                                        <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input type="password" class="form-control" placeholder="Enter {{$field}}" name="{{$field}}" id="{{$field}}">
                                            <div class="input-group-addon" >
                                                <a class="custom" data-work="{{$field}}"><i class="fa fa-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    @break

                                    @case('custom')
                                    @if($heading == 'emailtemplate')
                                    <div class="form-group">
                                        <label>Dynamic variables for this template</label>
                                            @foreach(@json_decode($obj['variables'], true) as $variable => $variable_description)
                                            <div class="chip">
                                                <a href="JavaScript:Void(0);" data-toggle="tooltip" title="{{$variable_description}}">{{$variable}}</a>
                                            </div>
                                            @endforeach
                                    </div>
                                    @endif
                                    <div class="form-group">
                                        <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        <textarea id="editor1" name="{{$field}}" rows="10" cols="80" required="required"> {{ $obj->{$field} }}
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
                                        <input type="text" class="form-control pull-right calender" id="datepicker" name="{{$field}}" required="required"  value="{{ Carbon\Carbon::parse($obj->{$field})->format('m/d/Y') }}">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    @break

                                    @case('file')
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col col-sm-3">
                                                <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                                @if(!empty($obj->{$field}))
                                                    <img src="{{$obj->$field}}" height=120 width=120 >
                                                @endif
                                            </div>
                                            <div class="col col-sm-3">
                                                <label>Change {{$field}}</label>
                                                <input type="file" name="{{$field}}">
                                                @if($heading == 'toolbox')
                                                    <p class="help-block">Please select image in 1:1 ratio for better experience (360px * 360px)</p>
                                                @endif
                                                @if($heading == 'subtoolbox')
                                                    <p class="help-block">Please upload PNG image in 450 x 450 pixel or more in 1:1 ratio</p>
                                                @endif
                                                @if($field == 'banner')
                                                    <p class="help-block">Please select image of dimension 1440 x 2560 pixels for better experience</p>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                    @break

                                    @case('number')
                                        <div class="form-group">
                                            <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                            <input type="number" class="form-control" placeholder="Enter {{$field}}" name="{{$field}}" required="required" value="{{ $obj->{$field} }}">
                                        </div>
                                    @break

                                    @default
                                        <div class="form-group">
                                            <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                            <input type="text" class="form-control" placeholder="Enter {{$field}}" name="{{$field}}" required="required" value="{{ $obj->{$field} }}">
                                            @if($field == 'duration')
                                                <p class="help-block">Mention duration in seconds</p>
                                            @endif
                                        </div>
                                @endswitch
                            @endforeach

                            @if($heading == 'event')
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" required="required" name="status" >
                                        <option value="">Select</option>
                                        <option value="active" {{$obj->status == 'active' ? 'selected="selected"' : ''}}>Active</option>
                                        <option value="cancelled" {{$obj->status == 'cancelled' ? 'selected="selected"' : ''}}>Cancelled</option>
                                        <option value="complete" {{$obj->status == 'complete' ? 'selected="selected"' : ''}}>Complete</option>
                                        <option value="postponed" {{$obj->status == 'postponed' ? 'selected="selected"' : ''}}>Postponed</option>
                                    </select>
                                </div>
                            @endif
                            
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
        $('.calender').datepicker({
            autoclose: true
        });
        CKEDITOR.replace('editor1').config.allowedContent = true;

        $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
        });
    });
</script>
@endsection