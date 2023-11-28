<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        html {
            margin:20px 20px 20px 50px;
            font-family: 'Times New Roman', Times, serif;
            font-weight: normal;
        }

        .page-break {
            page-break-after: always;
        }

        .floating-left {
            float: left;
            position: relative;
            width:100px;
        }

        .logo {
            position: absolute;
            top:0;
            width: 100px;
            height: 50px;
        }

        .checkbox {
            padding: 0 0;
            border: 1px solid black;
        }

        .page-heading {
            position: absolute;
            top:50px;
            left:0;
        }

        .page-subheading {
            position: absolute;
            top:75px;
            left:5px;
        }

        .floating-center {
            float: left;
            width: 470px;
            height: 80px;
            margin: 0 10px;
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
            height: 80px;
            margin: 0px;
            padding: 0;
            border-left: 1px solid black;
            text-align: center;
        }

        .right-top {
            height:45px;
            width: 150px;
            margin-bottom: 5px;
        }

        .right-bottom {
            height:30px;
            width:170px;
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

        .table {
            font-size: 13px;
            margin:0;
            padding: 0;
            border-collapse: collapse;
        }

        th {
            border:1px solid black;
            text-align: center;
        }

        td {
            padding: 0 5px 2px 5px;
        }

        .table-striped tbody tr:nth-child(2n+1) {
            background-color: #d3d3d3;
        }

        .table-striped tr.bg-white {
            background-color: #f9f9f9;
        }

        .table-striped tr.border-bottom > td {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .table {
            border: 1px solid black;
        }

        .table-subsection > td {
            background-color: #f9f9f9;
            border: 1px solid black;
        }

        .striped {
            background-color: #d3d3d3;
        }

        .text-right {
            text-align: right;
        }
        .text-center {
            text-align:center;
        }
        p {
            margin:0;
            padding: 0;
        }
        .note {
            font-size: 13px;
            padding-bottom: 5px;
            font-style: italic;
        }
        .mt-10 {
            margin-top: 10px;
        }
        footer { position: fixed; bottom: 0px; left: 0px; right: 0px; background-color: lightblue; height: 50px; }
    </style>
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>
@include('pdf.bolus', compact('calcBoluses'))
<div class="page-break"></div>
@include('pdf.infusion', compact('calcInfusions'))
</body>
</html>
