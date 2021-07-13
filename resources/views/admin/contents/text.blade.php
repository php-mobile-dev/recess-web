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
            color: #403d99;
        }
        .info-message {
            color : #fff;
        }
        .info-container{
            background : #403d99;
        }
        table.dataTable tr th.select-checkbox.selected::after {
            content: "✔";
            margin-top: -11px;
            margin-left: -4px;
            text-align: center;
            text-shadow: rgb(176, 190, 217) 1px 1px, rgb(176, 190, 217) -1px -1px, rgb(176, 190, 217) 1px -1px, rgb(176, 190, 217) -1px 1px;
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
        <small>List of all Contents for <b>{{$sub_toolbox->name}}</b></small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Sub Toolboxes</h3>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{url('/content/create').'?parent_id='.$sub_toolbox->id}}">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col col-md-12">
                                    <div class="box">
                                        <div class="box-body">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <th> ID </th>
                                                    <th> Text </th>
                                                    <th> Action </th>
                                                </thead>
                                                <tbody>
                                                    @foreach($contents as $content)
                                                        <tr>
                                                            <td>{{$content->id}}</td>
                                                            <td>{!! $content->text !!}</td>
                                                            <td>
                                                                <a class="btn btn-info btn-sm" href="{{url('/content/'.$content->id.'/edit')}}"><i class="fa fa-pencil"></i></a>
                                                                <a class="btn btn-danger btn-sm dlt-btn" href="{{url('/del/content/'.$content->id)}}"><i class="fa fa-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
        $(function(){
            $('.dlt-btn').click(function(e){
                e.preventDefault();
                var href = $(this).attr('href');
                if (window.confirm("Are you sure you want to delete?")) { 
                    window.location.href = href;
                }
            });
        })
    </script>
@endsection