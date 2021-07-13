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
        <small>List of all Sub Toolbox for <b>{{$toolbox->name}}</b></small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Sub Toolboxes</h3>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{url('/subtoolbox/create').'?parent_id='.$toolbox->id}}">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        @foreach($subtoolboxes as $sub_tool)
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col col-md-2">
                                        <img src="{{$sub_tool->image}}" height=100 width=100 alt="no image">
                                    </div>
                                    <div class="col col-md-10">
                                        <div class="box" style="padding:10px;">
                                            <div class="box-body no-padding">
                                                <table class="table table-condensed">
                                                    <tbody>
                                                        <tr>
                                                            <td><b>Name</b></td>
                                                            <td>{{$sub_tool->name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Active</b></td>
                                                            <td>
                                                                @if($sub_tool->active)
                                                                    <small class="label bg-green">Active</small>
                                                                @else
                                                                    <small class="label bg-primary">Inactive</small>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm" href="{{url('subtoolbox/'.$sub_tool->id.'/edit')}}">Edit</a>
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-success btn-sm" href="{{url('content').'?parent_id='.$sub_tool->id}}">Contents</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xs-3"></div>
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
    
@endsection