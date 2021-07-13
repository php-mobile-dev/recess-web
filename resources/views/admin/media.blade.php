@extends('admin.layouts.app')
@section('header-extra')
    <style>
        .container {
        position: relative;
        width: 100%;
        max-width: 400px;
        }

        .container img {
        width: 100%;
        height: auto;
        }

        .container .btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        background-color: #555;
        color: white;
        font-size: 16px;
        padding: 12px 24px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        text-align: center;
        }

        .container .btn:hover {
        background-color: black;
        }
    </style>
@endsection
@section('body')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Add Images
        <small>For App start up images</small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row" style="padding : 5px;">
            <div class="col col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <div class="row">
                    @foreach($medias as $media)
                        <div class="col col-lg-6 col-md-6 col-sm-6 col-xs-12" style="display: inline block;">
                            <div class="container" style="float: left; margin-bottom: 10px;">
                                <img src = "{{asset('/uploads').'/'.$media->media}}" class="img-responsive" height=300 width=300>
                                @if($medias->count()>1)
                                <button class="btn btn-danger dlt-btn" data-id = "{{$media->id}}"><i class="fa fa-trash"></i></button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col col-lg-3 col-md-3 col-sm-12 col-xs-12" style="background-color : rgba(254,92,92,0.7); padding : 5px;">
                <form action="{{ url('/media')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Add an image</label>
                        <div id="image"></div>
                        <input type="file" name="image" class="form-control">
                        
                        <p class="help-block">** Image should be of size 1000*1300</p>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
        

        @if(Session::has('message'))
                <div class="alert alert-default info-container" role="alert">
                    <span class="info-message">{{Session::get('message')}}</span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color :#fff;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
        @endif
    </section>
</div>

@endsection


@section('script_extra')
    <script>
        $(".dlt-btn").click(function(){
            var confirm = window.confirm('Do You really want to delete? ');
            if(confirm)
            {
                var id = $(this).data('id');
                $.get("{{url('/del-media')}}/"+id, function(data, status){
                    location.reload();
                });
            }
        });
    </script>
@endsection