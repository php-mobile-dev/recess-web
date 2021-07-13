@extends('admin.layouts.app')
@section('header_extra')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
    <style>
        .tag {
            font-size: 14px;
            padding: .3em .4em .4em;
            margin: 0 .1em;
            width: 100%;
        }
        .tag a {
            color: #bbb;
            cursor: pointer;
            opacity: 0.6;
        }
        .tag a:hover {
            opacity: 1.0
        }
        .tag .remove {
            vertical-align: bottom;
            top: 0;
        }
        .tag a {
            margin: 0 0 0 .3em;
        }
        .tag a .glyphicon-white {
            color: #fff;
            margin-bottom: 2px;
        }
        div#selected_synonym {
            margin-bottom: 10px;
        }
    </style>
@endsection
@section('body')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Add Word
        </h1>
      <ol class="breadcrumb">
        <li><a href="{{url('/video')}}"><i class="fa fa-list"></i> List</a></li>
        <li class="active">Add</li>
      </ol>
    </section>

    <section class="content container-fluid">
        <div class="row">
            <div class="col col-md-1"></div>
            <div class="col col-md-10">
                <form action="{{url('/video' )}}" method="POST">
                    @csrf
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Fill the details</h3>
                        </div>
                        <div class="box-body">

                            <div class="form-group">
                                <label>Youtube Url</label>
                                <input type="text" class="form-control" placeholder="Enter Youtube Url" name="url" required="required" value="{{ old('url') }}">
                            </div>

                            <div class="form-group">
                                <label>Choose Categories</label>
                                <select class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="Select Categories" style="width: 100%;" tabindex="-1" aria-hidden="true" name="category_ids[]">
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                                <p class="help-block">Hold CTRL to select multiple</p>
                            </div>


                            <div class="form-group">
                                <label>Lyrics</label>
                                <textarea class="form-control" name="lyrics"></textarea>
                                <p class="help-block">Use enter for every new line & you can also resize the text area</p>
                            </div>

                            <div class="form-group">
                                <label>Duration</label>
                                <input type="text" class="form-control" placeholder="Enter the duration of the video (in Seconds)" name="duration" required="required" value="{{ old('duration') }}">
                                <p class="help-block">Enter the duration of the video (in Seconds)</p>
                            </div>

                            <div class="form-group">
                                <label>Artist</label>
                                <input type="text" class="form-control" placeholder="Enter artist's name" name="artist" required="required" value="{{ old('artist') }}">
                            </div>

                            <div class="form-group">
                                <label>Album</label>
                                <input type="text" class="form-control" placeholder="Enter name of the album" name="album" required="required" value="{{ old('album') }}">
                                
                            </div>

                        </div> 
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    <input type="hidden" name="synonyms">
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- </script><script> -->
    var finalArray = [];
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

        CKEDITOR.replace('editor1')
    });

    $( function() {
        var availableTagsJson = $("#autosuggest").val();
        var availableTags = JSON.parse(availableTagsJson);
        $("#synonym").autocomplete({
            source: availableTags,

            select: function (e, ui) {
                var selectedItem = ui.item.value;
                console.log(selectedItem);
                finalArray.push(selectedItem);
                render();
            }
        });

        $('#synonym').keypress(function (e) {
            var key = e.which;
            if(key == 13) 
            {
                var value = $('#synonym').val();
                console.log(value);
                finalArray.push(value);
                $('#synonym').val('');
                render();  
                e.preventDefault();
            }
        }); 
    });
    function removeTag(removeItem)
    {
        finalArray = jQuery.grep(finalArray, function(value) {
            return value != removeItem;
        });
        console.log(finalArray);
        render();
    }
    function render(){
        $("#selected_synonym").html('');
        $.each(finalArray, function( index, selectedItem ) {
            $("#selected_synonym").append('<span class="tag label label-info">\
                <span>'+selectedItem+'</span>\
                <a data-word="'+selectedItem+'" onClick="removeTag(this.getAttribute(\'data-word\'));"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a>\
                </span>'
            );
        });
        $("#synonym").val('');
        $("[name=synonyms]").val(finalArray);
    }
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#img_viewer').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
            $('#img_viewer').fadeIn();
        }
    }

    $("#imgInp").change(function() {
        readURL(this);
    });

@endsection
