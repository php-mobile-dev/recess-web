<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{env('APP_NAME')}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/app_icon.png') }}">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/app_icon.png') }}">
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @font-face {
            font-family: GothamNarrowBook;
            src: url("{{asset('fonts/GothamNarrowBook.otf')}}") format("opentype");
        }

        body {
            background-color: #ffffff;
            font-family: 'GothamNarrowBook', sans-serif;
            font-size: 14px;
        }

        .wrapper {
            padding: 10px;
            margin: 0px;
            clear: both;
        }

        .content {
            margin-top: 0px;
            padding: 5px;
            line-height: 1.6;
        }

        h1.header {
            color: #000000;
            text-transform: capitalize;
            font-size: 14px;
            margin-bottom: 25px;
            font-weight: bold;
            text-shadow: 1px 0px #dedede;
        }

        .header img {
            float: left;
            width: 60px;
            height: 60px;
        }

        .header h1 {
            position: relative;
            top: 18px;
            left: 15%;
            color: #c4302e;
            font-size: 25px;
        }

        .accordion {
            background-color: #ffffff !important;
            color: #5386bd;
            cursor: pointer;
            padding: 10px;
            width: 100%;
            text-align: left;
            outline: none;
            font-size: 15px;
            transition: .6s;
            border: solid 1px;
            border-radius: 12px;
            margin-bottom: 12px;
            text-shadow: 1px 0px #dedede;
            box-shadow: 0 3px 6px 0 rgb(0 0 0 / 8%), 0 7px 9px 0 rgb(0 0 0 / 7%);
            display: flex;
            align-items: center;
        }

        p.accordian-btn-text {
            width: 90%;
        }

        .panel {
            background-color: transparent;
            padding: 0 18px;
            display: none;
            overflow: hidden;
            margin-bottom: 10px;
            -webkit-box-shadow: none;
            box-shadow: none;
        }

        .accordian-indicator {
            text-decoration: none;
            float: right;
            margin-right: 2px;
            margin-left: 7px;
            background-color: #ee2665;
            color: #ffffff;
            border-radius: 50%;
            padding: 2px;
            font-size: 10px;
            font-weight: 100;
            display: block;
            height: 20px;
            width: 20px;
            text-align: center;
            vertical-align: center;
        }
    </style>
</head>

<body>
    @if(!empty($page_content))
    {!! $page_content !!}
    @endif

    <script type="text/javascript">
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                // var panel = this.nextElementSibling;
                // if (panel.style.display === "block") {
                //     panel.style.display = "none";
                // } else {
                //     panel.style.display = "block";
                // }
                this.classList.toggle("active");
                var node = document.createElement("a");
                node.classList.add('accordian-indicator');
                let child_innerHtml = ["fa", "fa-plus"];
                if (this.classList.contains("active")) {
                    child_innerHtml = ["fa", "fa-minus"];
                }
                var i_node = document.createElement("i");
                i_node.classList.add(...child_innerHtml);
                node.appendChild(i_node);
                this.removeChild(this.children[1]);
                this.appendChild(node);

                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                }
            });
        }
    </script>
</body>

</html>