<script type="text/javascript" src="{{asset('admin/vendors/jquery/dist/jquery.min.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/jquery-ui/jquery-ui.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/fastclick/lib/fastclick.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/nprogress/nprogress.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/Chart.js/dist/Chart.min.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/gauge.js/dist/gauge.min.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/switchery/dist/switchery.min.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/moment/min/moment.min.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/DateJS/build/date.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/vendors/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js')}}"></script>

@if (isset($isDataTable) && $isDataTable)
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-buttons/js/buttons.flash.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/vendors/datatables.net-scroller/js/dataTables.scroller.min.js')}}"></script> 
@endif
<script src="{{asset('admin/build/js/jquery.confirm.min.js')}}"></script>
<script src="{{asset('admin/build/js/jquery-msgpopup.js')}}"></script>
@yield("script")
<script src="{{asset('admin/build/js/tablednd.js')}}"></script>
<script src="{{asset('admin/build/js/currency.js')}}"></script>
<script src="{{asset('admin/build/js/submit.js')}}"></script>
<script src="{{asset('admin/build/js/custom.js')}}"></script>

<div id="loading" style="display:none"><span class="loading"></span></div>