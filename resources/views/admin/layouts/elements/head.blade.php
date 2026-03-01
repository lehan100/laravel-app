<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="robots" content="index, nofollow">  
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/x-icon" href="{{asset('media/favicon.png')}}" />
<title>@yield('title')</title>

<link href="{{asset('admin/vendors/bootstrap/dist/css/bootstrap.css')}}" rel="stylesheet">

<link href="{{asset('admin/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">

<link href="{{asset('admin/vendors/nprogress/nprogress.css')}}" rel="stylesheet">

<link href="{{asset('admin/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">

<link href="{{asset('admin/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css')}}" rel="stylesheet">

<link href="{{asset('admin/vendors/jqvmap/dist/jqvmap.min.css')}}" rel="stylesheet" />

<link href="{{asset('admin/vendors/switchery/dist/switchery.min.css')}}" rel="stylesheet" />

<link href="{{asset('admin/vendors/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">

<link href="{{asset('admin/vendors/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">

@if (isset($isDataTable) && $isDataTable)
<link href="{{asset('admin/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('admin/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('admin/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('admin/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('admin/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet"> 
@endif
<link href="{{asset('admin/vendors/jquery-ui/jquery-ui.css')}}" rel="stylesheet"/>
<link href="{{asset('admin/build/css/jquery-msgpopup.css')}}" rel="stylesheet"/>
<link href="{{asset('admin/build/css/custom.css')}}" rel="stylesheet"/>
@yield("style")