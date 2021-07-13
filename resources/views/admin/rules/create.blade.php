@extends('admin.layouts.app')
@section('body')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Create Rules
        </h1>
      <ol class="breadcrumb">
        <li><a href="{{url('/rule')}}"><i class="fa fa-list"></i> Rules</a></li>
      </ol>
    </section>

    <section class="content container-fluid">
        <div class="row">
            <div class="col col-md-1"></div>
            <div class="col col-md-10">
                <form action="{{url('/rule')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Fill the details</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label>Preference</label>
                                
                                    <input type="text" class="form-control" required="required" name = "serial_no" value="{{$last_serial_no + 1}}">
                                
                            </div>
                            <div class="form-group">
                                <label>Choose Text Part</label>
                                <select class="form-control" required="required" name="part" >
                                    @foreach($parts as $part)
                                        <option value="{{$part}}">{{$part}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Choose Parameters</label>
                                <select class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="Select Parameters" data-select2-id="7" tabindex="-1" aria-hidden="true" name="parameters[]">
                                    @foreach($params as $param)
                                        <option data-select2-id="{{$param}}" value="{{$param}}">{{$param}}</option>
                                    @endforeach
                                </select>
                                <p class="help-text">Press Ctrl to select multiple</p>
                            </div>
                            <div class="form-group">
                                <label>Choose Operator</label>
                                    <select class="form-control" required="required" name="operator" >
                                        @foreach($operators as $operator)
                                            <option value="{{$operator}}">{{$operator}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group">
                                <label>No of days</label>
                                <input type="number" class="form-control" name = "days" placeholder="Ignore for Part 1 rules">
                            </div>

                            <div class="form-group">
                                <label>Score</label>
                                <input type="text" class="form-control" required="required" name = "score" placeholder="Enter Score (If you choose BETWEEN, type min value - max value)">
                                <p class="help-text">Example if the score is between 40 & 49, type 40-49</p>
                            </div>

                            <div class="form-group">
                                <label>Quote</label>
                                <input type="text" class="form-control" required="required" name = "quote" placeholder="Enter Quote">
                            </div>
                        </div> 
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col col-md-1"></div>
        </div>

    </section>
</div>
@endsection

@section('script_extra')
<script>
    @if(Session::has('error'))
        alert("{{Session::get('error')}}")
    @endif
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
        CKEDITOR.replace('editor1');
    });
</script>
@endsection
