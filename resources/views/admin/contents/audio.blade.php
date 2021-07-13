@extends('admin.layouts.app')
@section('body')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Content in {{$sub_toolbox->name}}
        </h1>
    </section>

    <section class="content container-fluid">
        <div class="row">
            <div class="col col-md-1"></div>
            <div class="col col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Fill the details</h3>
                    </div>
                    <div class="box-body">
                        @if($contents->count() == 0)
                        <div id="add">
                            <form action="{{url('/content' )}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{$sub_toolbox->id}}" name="parent_id">
                                <div class="form-group">  
                                    <label>Change banner</label>
                                    <input type="file" name="banner" required="required">
                                    <p class="help-text">Please upload PNG image in 1440 [w] x 2560 [h] pixel or more in the same ratio</p>
                                </div>
                                <div class="form-group"> 
                                        <label>Add Audio files</label>
                                        <input type="file" name="filename" required="required">
                                </div>
                                <div class="form-group">
                                    <label>Duration</label>
                                    <input type="number" class="form-control" placeholder="Enter Duration" name="duration" required="required" value="{{ old('duration') }}">
                                </div>
                                <div class="form-group">
                                    <label>Narrator</label>
                                    <input type="text" class="form-control" placeholder="Enter Narrator" name="narrator" required="required" value="{{ old('narrator') }}">
                                </div>
                                <div class="form-group">
                                    <label>Composer</label>
                                    <input type="text" class="form-control" placeholder="Enter Composer" name="composer" required="required" value="{{ old('composer') }}">
                                </div>
                                <div class="form-group">
                                    <label>Text</label>
                                    <textarea id="editor1" name="text" rows="10" cols="80" required="required">
                                    </textarea>
                                </div>
                                <div class="form-group"><button type="submit" class="btn btn-primary">Submit</button></div>

                            </form>
                        </div> 
                        @else
                        <?php $obj = $contents->first(); ?>
                        <div id="edit">
                            <form action="{{url('/content/'.$obj->id)}}" method="POST" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="form-group">
                                    <div class="row">  
                                        <div class="col col-sm-3">
                                            <label>Banner Image</label>
                                            @if(!empty($obj->banner))
                                                <img src="{{$obj->banner}}" height=120 width=120 >
                                            @endif
                                        </div>
                                        <div class="col col-sm-3">
                                            <label>Change banner</label>
                                            <input type="file" name="banner">
                                            <p class="help-text">Please upload PNG image in 1440 [w] x 2560 [h] pixel or more in the same ratio</p>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">  
                                        <div class="col col-sm-3">
                                            <label>Audio File</label>
                                            @if(!empty($obj->filename))
                                            <audio controls class="iru-tiny-player" data-title="Sample Audio File">
                                                <source src="{{$obj->filename}}" type="audio/mpeg">
                                            </audio>
                                            @endif
                                        </div>
                                        <div class="col col-sm-3">
                                            <label>Change Audio</label>
                                            <input type="file" name="filename">
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Duration</label>
                                    <input type="number" class="form-control" placeholder="Enter Duration" name="duration" required="required" value="{{ $obj->duration }}">
                                </div>
                                <div class="form-group">
                                    <label>Narrator</label>
                                    <input type="text" class="form-control" placeholder="Enter Narrator" name="narrator" required="required" value="{{ $obj->narrator }}">
                                </div>
                                <div class="form-group">
                                    <label>Composer</label>
                                    <input type="text" class="form-control" placeholder="Enter Composer" name="composer" required="required" value="{{ $obj->composer }}">
                                </div>
                                <div class="form-group">
                                    <label>Text</label>
                                    <textarea id="editor1" name="text" rows="10" cols="80" required="required"> {{ $obj->text }}
                                    </textarea>
                                </div>

                                <div class="form-group"><button type="submit" class="btn btn-primary">Submit</button></div>
                            </form>
                        </div>
                        @endif
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
        CKEDITOR.replace('editor1')
    });
</script>
@endsection