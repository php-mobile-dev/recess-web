@extends('admin.layouts.app')

@section('body')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Edit Question
        </h1>
      <ol class="breadcrumb">
        <li><a href="{{url('/video')}}"><i class="fa fa-list"></i> List</a></li>
        <li class="active">Edit</li>
      </ol>
    </section>

    <section class="content container-fluid">
        <div class="row">
            <div class="col col-md-1"></div>
            <div class="col col-md-6">
                <form action="{{url('/video/'.$video->id )}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Change Details</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="name" required="required" value="{{ $video->name }}">
                            </div>
                            <div class="form-group">
                                <label>Video URL</label>
                                <input type="text" class="form-control" name="url" required="required" value="{{ $video->url }}" readonly="readonly"> 
                            </div>
                            <div class="form-group">
                                <label>Lyrics</label>
                                <textarea class="form-control" name="lyrics" required="required" value="{{ $lyrics->lyrics }}">{{ $lyrics->lyrics }}</textarea>
                                <p class="help-block">Use enter for every new line & you can also resize the text area</p>
                            </div>
                            <div class="form-group">
                                <label>Choose Categories</label>
                                <select class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="Select Categories" style="width: 100%;" tabindex="-1" aria-hidden="true" name="category_ids[]">
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}" {{($video_categories->where('id', $category->id)->count() > 0) ? 'selected="selected"' : ''}}>{{$category->name}}</option>
                                    @endforeach
                                </select>
                                <p class="help-block">Hold CTRL to select multiple</p>
                            </div>
                            <div class="form-group">
                                <label>Duration</label>
                                <input type="text" class="form-control" placeholder="Enter the duration of the video (in Seconds)" name="duration" required="required" value="{{ $video->duration }}">
                                <p class="help-block">Enter the duration of the video (in Seconds)</p>
                            </div>
                            <div class="form-group">
                                <label>Artist</label>
                                <input type="text" class="form-control" placeholder="Enter artist's name" name="artist" required="required" value="{{ $video->artist }}">
                            </div>

                            <div class="form-group">
                                <label>Album</label>
                                <input type="text" class="form-control" placeholder="Enter name of the album" name="album" required="required" value="{{ $video->album }}">
                            </div>
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
            <div class="col col-md-4">
                <div class="conntainer-fluid">
                    {!!$video->iframe!!}
                </div>
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
        CKEDITOR.replace('editor1');

    });
</script>
@endsection