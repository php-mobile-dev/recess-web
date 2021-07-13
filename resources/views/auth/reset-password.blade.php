<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{env('APP_NAME')}}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{asset('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('bower_components/font-awesome/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('bower_components/Ionicons/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/css/AdminLTE.min.css')}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('plugins/iCheck/square/blue.css')}}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/app_icon.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/app_icon.png')}}">
    <style>
        @font-face {
            font-family: GothamNarrowBook;
            src: url("fonts/GothamNarrowBook.otf") format("opentype");
        }

        html,
        body {
            font-family: 'GothamNarrowBook', sans-serif;
        }

        .login-logo a,
        .register-logo a {
            color: #125aa6;
        }

        .login-box-body,
        .register-box-body {
            background: rgba(255, 255, 255, 0.5);
            padding: 10px;
            border-top: 0;
            color: #ee2665;
            height: 300px !important;
        }

        .login-page {
            background: #c6e2ff
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="/"><b>Rec</b>ess</a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            @if(!$error)
            <p class="login-box-msg" style="color: #e97238">
            <h1>Password Reset</h1>
            <p>
                You have requested a password reset for your Recess Account
                <b>{{$email}}</b>
            </p>
            </p>

            <form action="{{ url('/webview/res-password') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{$id}}">
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="New Password" name="password" value="{{ old('password') }}" required autofocus>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat" style="background: #ee2665; border-color: #ee2665;">Submit</button>
                </div>
            </form>
            @elseif(Session::has('message'))
            <p class="login-box-msg" style="color: #ee2665">
            <h1>Success!</h1>
            <p>
                You have successfully changed password for your account. Please log into your Recess app.
            </p>
            </p>
            @else
            <p class="login-box-msg" style="color: #ee2665">
            <h1>404</h1>
            <p>
                This link is not right.. return back to <a href="{{url('/')}}">Home</a>
            </p>
            </p>
            @endif


        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery 3 -->
    <script src="{{asset('bower_components/jquery/dist/jquery.min.js')}}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{asset('bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <!-- iCheck -->
    <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
    <script>
        $(function() {
            @if($errors->any())
            alert('{{$errors->first()}}');
            @endif
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            });
        });
    </script>
</body>

</html>