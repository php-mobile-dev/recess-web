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
        {{isset($heading) ? $heading : ''}}
        <small>List of all {{isset($heading) ? $heading : ''}}</small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Statistics</h3>
                        <div class="pull-right">
                            @if($model !== 'setting')
                            <a class="btn btn-primary" href="{{url($model.'/create')}}">
                                <i class="fa fa-plus"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <span class="imports-checkbox" style="display: none;">
                            <button type="button" class="btn btn-danger no-radius btn-xs">Delete</button>
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
    <script src="{{asset('app.js')}}"></script>
@endsection