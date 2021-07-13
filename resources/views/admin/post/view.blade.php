@extends('admin.layouts.app')
@section('header_extra')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css"
    integrity="sha512-nNlU0WK2QfKsuEmdcTwkeh+lhGs6uyOxuUs+n+0oXSYDok5qy0EI0lt01ZynHq6+p/tbgpZ7P+yUb+r71wqdXg=="
    crossorigin="anonymous" />
@endsection
@section('body')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            View Post(s)
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <img class="profile-user-img img-responsive img-circle" src="{{$data['user']->avatar}}"
                            alt="User profile picture">
                        <h3 class="profile-username text-center">{{$data['user']->name}}</h3>
                        <p class="text-muted text-center">
                            {{$data['user']->purchased == 1 ? 'Purchased User' : 'Free User'}}</p>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Posts</b> <a class="pull-right">{{$data['user']->no_of_posts}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Friends</b> <a class="pull-right">{{$data['user']->no_of_friends}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Events</b> <a class="pull-right">{{$data['user']->no_of_feeds}}</a>
                            </li>
                        </ul>
                    </div>
                </div>



                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">About User</h3>
                    </div>
                    <div class="box-body">
                        <strong><i class="fa fa-book margin-r-5"></i> Biography</strong>

                        <p class="text-muted">
                            {{$data['user']->bio}}
                        </p>
                        <hr>

                        <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>
                        <p class="text-muted">{{$data['user']->address}}</p>
                        <hr>
                    </div>
                </div>
            </div>


            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#feed" data-toggle="tab" aria-expanded="true">Post</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="feed">
                            @foreach($data['post'] as $post)
                            <div class="post clearfix">
                                <div class="user-block">
                                    <img class="img-circle img-bordered-sm" src="{{$data['user']->avatar}}"
                                        alt="User Image">
                                    <span class="username">
                                        <a href="#">{{$data['user']->name}}</a>
                                    </span>
                                    <span
                                        class="description">{{\Carbon\Carbon::parse($post->created_at)->format(env('DATE_FORMAT'))}}</span>
                                </div>
                                <!-- /.user-block -->
                                <p
                                    style="font-size: {{ !empty($post->font_size) ? $post->font_size.'px' : '15px' }}; background-color: {{!empty($post->background_color) ? $post->background_color : '#fff'}}">
                                    {{$post->post_text}}
                                </p>

                                @if($post['media']->count() > 0)
                                <div class="row margin-bottom">
                                    <?php foreach($post['media'] as $media){ ?>
                                    <div class="col-md-4">
                                        @if(stripos($media->mime_type, 'image') !== false)
                                        <a class="grouped_elements" rel="group1" href="{{$media->filename}}"><img
                                                src="{{$media->filename}}" alt="" width="320" height="240" /></a>
                                        @else
                                        <video width="320" height="240" controls>
                                            <source src="{{$media->filename}}" type="{{strtolower($media->mime_type)}}">
                                            Your browser does not support the video tag.
                                        </video>
                                        @endif
                                    </div>
                                    <?php } ?>
                                </div>
                                @endif
                            </div>
                            @endforeach


                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
@endsection
@section('script_extra')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"
    integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA=="
    crossorigin="anonymous"></script>
<script>
$(function() {
    $("a.grouped_elements").fancybox();
});
</script>
@endsection