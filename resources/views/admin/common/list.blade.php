@extends('admin.layouts.app')
@section('header_extra')
    <style>
        .input-sm {
            margin-left : 20px !important;
        }
        #add_btn{
            border-radius: 50%;
            position: absolute;
            bottom: 50px;
            right : 10px;
        }
        label select {
            width: 53px !important;
        }
        td.dataTables_empty {
            width: 100%;
            text-align: center;
            font-size: 20px;
            background: #fff;
            color: #125aa6;
        }
        .info-message {
            color : #fff;
        }
        .info-container{
            background : rgba(238, 38, 101, 0.5);
        }
        select.form-control.input-sm {
            /* min-width: 42px; */
            padding: 5px;
        }
        table.dataTable tr th.select-checkbox.selected::after {
            content: "✔";
            margin-top: -11px;
            margin-left: -4px;
            text-align: center;
            text-shadow: rgb(176, 190, 217) 1px 1px, rgb(176, 190, 217) -1px -1px, rgb(176, 190, 217) 1px -1px, rgb(176, 190, 217) -1px 1px;
        }
        .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover{
            background-color: #ee2665; border-color: #ff7ca5;
        }
    </style>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    
@endsection
@section('body')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        @if($model !== 'category')
        {{isset($heading) ? $heading : ''}}
        <small>List of all {{isset($heading) ? $heading : ''}}</small>
        @else
        Categories
        <small>List of all Categories</small>
        @endif
      </h1>
    </section>

    <div class="modal fade" id="modal-default" style="display: none;">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
            <h4 class="modal-title">Delete</h4>
            </div>
            <div class="modal-body">
            <p>Do you really want to delete the selected rows ? 
                @if($model=='video')
                    <strong>Please Note that all the words which belongs to this category will also be deleted</strong>
                @endif
            </p>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-info pull-left" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-danger" id="confirm_delete">Delete</button>
            </div>
        </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Statistics</h3>
                        <div class="pull-right">
                            @if(!in_array($model, ['setting', 'content', 'event', 'activitycategory', 'report']))
                                <a class="btn btn-primary" href="{{url('/admin/'.$model.'/create')}}">
                                    <i class="fa fa-plus"></i>
                                </a>
                            @endif

                            @if($model == 'dailyquote')
                                @include('admin.components.csvUploader')
                            @endif
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <span class="example-checkbox pull-right" style="display: none;">
                            <button type="button" class="btn btn-danger no-radius" data-toggle="modal" data-target="#modal-default"">Delete <i class="fa fa-trash"></i></button>
                        </span>
                        <table id="example" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" name="select_all" value="1" id="example-select-all" />
                                        <!-- <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="select_all" value="1" id="example-select-all" class="custom-control-input" />
                                            <label class="custom-control-label" for="example-select-all">&nbsp;</label>
                                        </div> -->
                                    </th>
                                    @foreach($list_column as $col)
                                        @if($col!='id')
                                            <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
                                        @endif
                                    @endforeach
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="data_header" name="data_header" value="{{$data_string}}" />
        

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
    <script src="{{asset('bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function(){
            var columns = $("#data_header").val();
            columns = jQuery.parseJSON(columns);
            var ids = [];
            var example = $('#example').DataTable( {
                ajax: {
                    url: "{{ url('/admin/list/'.$model) }}",
                    dataSrc: 'data'
                },
                columns: columns,
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return '<div class="custom-control custom-checkbox"><input type="checkbox" name="id[]" id="' + $('<div/>').text(data).html() + '" value="' + $('<div/>').text(data).html() + '" class="custom-control-input"><label for="' + $('<div/>').text(data).html() + '" class="custom-control-label">&nbsp;</label></div>';
                    }
                }],
                select: {
                    style: 'os',
                    selector: 'td:first-child'
                },
                bsort: false
            });

            @if(!in_array($model, ['webcontent', 'setting', 'toolbox', 'group']))
                // Handle click on "Select all" control
                $('#example-select-all').on('click', function(){
                    var rows = example.rows({ 'search': 'applied' }).nodes();
                    $('input[type="checkbox"]', rows).prop('checked', this.checked);
                    if (this.checked) {
                        $('.example-checkbox').show();
                    }
                    else {
                        $('.example-checkbox').hide();
                    }
                });

                // Handle click on checkbox to set state of "Select all" control
                $('#example tbody').on('change', 'input[type="checkbox"]', function () {
                    var checked_checkbox = $('#example tbody input[type="checkbox"]:checked').length;
                    var all_checkbox = $('#example tbody input[type="checkbox"]').length;
                    if (checked_checkbox !== all_checkbox) {
                        $('#example-select-all').prop('checked', false);
                    }
                    else {
                        $('#example-select-all').prop('checked', true);
                    }
                    if (checked_checkbox) {
                        $('.example-checkbox').show();
                    }
                    else {
                        $('.example-checkbox').hide();
                    }
                });

                $('#confirm_delete').on('click', function () {
                    ids = $('#example tbody input[type="checkbox"]:checked').serializeArray();
                    console.log("{{url('/admin')}}/{{$model}}/delete");
                    $.ajax({
                        method: "POST",
                        url: "{{url('/admin')}}/{{$model}}/del",
                        data: {id: ids, _token: "{{csrf_token()}}"}
                    }).done(function (msg) {
                        if (msg['response'] === true) {
                            window.location.reload();
                        }else{
                            alert('You Can not delete all the records');
                            window.location.reload();
                        }
                    });
                });

                $('select[name=user_length]').css({
                    'width': '35px',
                    'display': 'inline-block',
                    'margin-left': '5px',
                    'margin-right': '5px',
                    'padding-left' : '6px'
                });
            @endif
        });
        
    </script>
@endsection