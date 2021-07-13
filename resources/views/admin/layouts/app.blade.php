

<!DOCTYPE html>

<html>

<head>

  <meta charset="utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>{{ env('APP_NAME') }}</title>

  <!-- Tell the browser to be responsive to screen width -->

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="stylesheet" href="{{asset('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">

  <!-- Font Awesome -->

  <link rel="stylesheet" href="{{asset('bower_components/font-awesome/css/font-awesome.min.css')}}">

  <!-- Ionicons -->

  <link rel="stylesheet" href="{{asset('bower_components/Ionicons/css/ionicons.min.css')}}">

  <link rel="stylesheet" href="{{asset('bower_components/jvectormap/jquery-jvectormap.css')}}">

  <!-- Theme style -->

  <link rel="stylesheet" href="{{asset('dist/css/AdminLTE.min.css')}}">

  <link rel="stylesheet" href="{{asset('dist/css/skins/_all-skins.min.css')}}">

  <link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/app_icon.png')}}">

  <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/app_icon.png')}}">

  <!-- Google Font -->

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <link rel="stylesheet" href="{{asset('css/flags.css')}}" />
  <link rel="stylesheet" href="{{asset('css/autocomplete.css')}}" />
  <script src="{{asset('bower_components/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('js/autocomplete.js')}}"></script>
  <style>

    @font-face {
        font-family: GothamNarrowBook;
        src: url("{{asset('fonts/GothamNarrowBook.otf')}}") format("opentype");
    }

    body {

        font-family: 'GothamNarrowBook', sans-serif;

    }

    #loadingDiv {

            position: fixed;

            display: none;

            width: 100%;

            height: 100%;

            top: 0;

            left: 0;

            right: 0;

            bottom: 0;

            background-color: rgba(254, 92, 92,0.4);

            z-index: 9999;

            cursor: pointer;

            text-align: center;

        }

        #loadingDiv img {

            position: absolute;

            top: 0;

            right: 0;

            bottom: 0;

            left: 0;

            margin: auto;

            width: 100px;

            height: 100px;

        }

        .chip {
          display: inline-block;
          padding: 0 25px;
          height: 30px;
          font-size: 10px;
          line-height: 30px;
          border-radius: 15px;
          background-color: #f1f1f1;
        }

  </style>

  @section('header_extra')

    @show

</head>

<!--

BODY TAG OPTIONS:

=================

Apply one or more of the following classes to get the

desired effect

|---------------------------------------------------------|

| SKINS         | skin-purple                               |

|               | skin-black                              |

|               | skin-purple                             |

|               | skin-yellow                             |

|               | skin-red                                |

|               | skin-green                              |

|---------------------------------------------------------|

|LAYOUT OPTIONS | fixed                                   |

|               | layout-boxed                            |

|               | layout-top-nav                          |

|               | sidebar-collapse                        |

|               | sidebar-mini                            |

|---------------------------------------------------------|

-->

<body class="hold-transition skin-yellow sidebar-mini">

<div id="loadingDiv">

    <img src="{{asset('images/loader.gif')}}">

</div>

<div class="wrapper">



  @include('admin.partials.header')

  

  @include('admin.partials.sidebar')



  @section('body')

    @show



  @include('admin.partials.footer')



</div>

<!-- ./wrapper -->



<!-- REQUIRED JS SCRIPTS -->



<!-- jQuery 3 -->



<!-- Bootstrap 3.3.7 -->

<script src="{{asset('bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>



<!-- FastClick -->

<script src="{{asset('bower_components/fastclick/lib/fastclick.js')}}"></script>



<!-- Sparkline -->

<script src="{{asset('bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>

<!-- jvectormap  -->

<script src="{{asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>

<script src="{{asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>

<!-- SlimScroll -->

<script src="{{asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>

<!-- ChartJS -->

<script src="{{asset('bower_components/chart.js/Chart.js')}}"></script>

<!-- date-range-picker -->

<script src="{{asset('bower_components/moment/min/moment.min.js')}}"></script>

<script src="{{asset('bower_components/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

<!-- bootstrap datepicker -->

<script src="{{asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>



<!-- AdminLTE App -->

<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.12/js/bootstrap-select.min.js" integrity="sha256-+o/X+QCcfTkES5MroTdNL5zrLNGb3i4dYdWPWuq6whY=" crossorigin="anonymous"></script> -->

<script>

  $(function(){

    // $("#lang_select").selectpicker();
    // AJAX Interceptor

    var $loading = $('#loadingDiv').hide();

    $(document).ajaxStart(function () {

        $loading.show();

    }).ajaxStop(function () {

        $loading.hide();

    });

  }); 



</script>

@section('script_extra')

@show

</body>

</html>