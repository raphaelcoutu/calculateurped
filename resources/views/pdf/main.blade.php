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

        .floating-logo {
            float: left;
            width: 150px;
            height: 75px;
            margin: 10px;
            /*border: 3px solid #73AD21;*/
        }

        .floating-center {
            float: left;
            width: 375px;
            height: 100px;
            margin: 10px;
            /*border: 3px solid #73AD21;*/
        }

        .floating-center h2 {
            font-size: 22px;
        }

        .floating-right {
            float: left;
            width: 150px;
            height: 100px;
            margin: 10px;
            padding: 0;
            /*border: 3px solid #73AD21;*/
            border-left: 1px solid black;
            text-align: center;
        }

        .right-top {
            /*border:1px solid red;*/
            height:35px;
            width: 150px;
            margin-bottom: 15px;
        }

        .right-bottom {
            height:65px;
            width:150px;
        }

        .weight {
            font-size: 32px;
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
            border:2px solid black;
            margin-bottom:5px;
            padding:5px;
            border-collapse: collapse;
        }

        th {
            border:1px solid black;
            text-align: center;
        }

        td {
            border:1px solid black;
            padding: 2px 5px 2px 5px;
        }

        table tr:nth-child(odd) td{
            background-color: white;
        }

        table tr:nth-child(even) td{
            background-color: #d3d3d3;
        }
        footer { position: fixed; bottom: 0px; left: 0px; right: 0px; background-color: lightblue; height: 50px; }
    </style>
    <title>Médicament transport</title>
</head>
<body>
@include('pdf.header', compact('patient'))
@include('pdf.bolus', compact('boluses'))
<div class="page-break"></div>
@include('pdf.header', compact('patient'))
@include('pdf.infusion', compact('infusions'))
</body>
</html>