<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        html {
            margin:30px 30px 10px 50px;
        }

        .page-break {
            page-break-after: always;
        }

        .floating-left {
            float: left;
            position: relative;
            width:150px;
        }

        .logo {
            position: absolute;
            top:0;
            width: 100px;
            height: 50px;
        }

        .subheading {
            position: absolute;
            top:60px;
            left:0;
        }

        .floating-center {
            float: left;
            width: 400px;
            height: 100px;
            margin: 0 10px;
            /*border: 3px solid #73AD21;*/
        }

        .floating-center h2 {
            font-size: 16px;
            text-align:center;
            font-weight: bold;
            margin: 5px 0;
        }

        .floating-right {
            float: left;
            width: 90px;
            height: 90px;
            margin: 5px;
            padding: 0;
            border-left: 1px solid black;
            text-align: center;
        }

        .right-top {
            height:35px;
            width: 150px;
            margin-bottom: 5px;
        }

        .right-bottom {
            height:40px;
            width:150px;
        }

        .weight {
            font-size: 25px;
            padding: 0;
            margin:0;
        }

        .after-box {
            clear: left;
        }

        h2 {
            margin:0;
            padding:0;
        }

        table {
            font-size: 13px;
            border:1px solid black;
            margin:0;
            padding: 0;
            border-collapse: collapse;
        }

        th {
            border:1px solid black;
            text-align: center;
        }

        td {
            padding: 2px 5px 2px 5px;
        }

        .table-striped tbody tr:nth-child(2n+1) {
            background-color: #d3d3d3;
        }

        .table-striped-2 tbody tr:nth-child(4n+1),
        .table-striped-2 tbody tr:nth-child(4n+2) {
            background-color: #d3d3d3;
        }

        .table-striped-2 tr.bg-white {
            background-color: #f9f9f9;
        }

        .table-striped-2 tr.border-bottom > td {
            border-bottom: 1px solid black;
        }

        td.indent {
            padding-left:20px;
        }

        .text-right {
            text-align: right;
        }
        .text-center {
            text-align:center;
        }
        .text-bold {
            font-weight: bold;
        }
        footer { position: fixed; bottom: 0px; left: 0px; right: 0px; background-color: lightblue; height: 50px; }
    </style>
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>
@include('pdf.bolus', compact('boluses'))
<div class="page-break"></div>
@include('pdf.infusion', compact('infusions'))
</body>
</html>