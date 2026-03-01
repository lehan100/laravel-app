{{ html()->form('POST', route("$controllerName/filter"))->attributes([
    'accept-charset' => 'UTF-8',
    'enctype' => 'multipart/form-data',
    'class' => 'p-3 m-0',
    'id' => 'appFormFilter',
])->open() }}
@php
    $from = html()->text('from', @$filter['from'])->attributes( ['class' => 'form-control', 'id' => 'from','placeholder'=>'From']);
    $to = html()->text('to', @$filter['to'])->attributes( ['class' => 'form-control', 'id' => 'to','placeholder'=>'To']);
    $data_payment_method = config('configs.location.payment_method');
    $payment_method = html()->select('payment_method', $data_payment_method, @$filter['payment_method'])->attributes( ['class' => 'form-control']);
    $data_order_status = config('configs.location.order_status');
    $order_status = html()->select('order_status', $data_order_status, @$filter['order_status'])->attributes( ['class' => 'form-control']);
    $data_shipping_status = config('configs.location.shipping_status');
    $shipping_status = html()->select('shipping_status', $data_shipping_status, @$filter['shipping_status'])->attributes( ['class' => 'form-control']);
    $data_payment_status = config('configs.location.payment_status');
    $payment_status = html()->select('payment_status', $data_payment_status, @$filter['payment_status'])->attributes( ['class' => 'form-control']);
@endphp
<div class="row align-items-end">
    <div class="col-6 col-md">
        <div class="row align-items-center mb-3">
            <div class="col-12 font-weight-bold mb-2">
                Date From
            </div>
            <div class="col">
                <div class='input-group mb-0'>
                    {!! $from !!}
                    <span id="from-picker" class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="row align-items-center mb-3">
            <div class="col-12 font-weight-bold mb-2">
                Date To
            </div>
            <div class="col">
                <div class='input-group mb-0'>
                    {!! $to !!}
                    <span id="top-picker" class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md ">
        <div class="row align-items-center mb-3">
            <div class="col-12 font-weight-bold mb-2">
                Payment Method
            </div>
            <div class="col">
                {!! $payment_method !!}
            </div>
        </div>
        <div class="row align-items-center mb-3">
            <div class="col-12 font-weight-bold mb-2">
                Payment Status
            </div>
            <div class="col">
                {!! $payment_status !!}
            </div>
        </div>
        
    </div>
    <div class="col-6 col-md ">
        <div class="row align-items-center mb-3">
            <div class="col-12 font-weight-bold mb-2">
                Order Status
            </div>
            <div class="col">
                {!! $order_status !!}
            </div>
        </div>
        <div class="row align-items-center mb-3">
            <div class="col-12 font-weight-bold mb-2">
                Shipping Status
            </div>
            <div class="col">
                {!! $shipping_status !!}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-auto mb-3">
        <p><button type="submit" name="button" value="submit" class="btn btn-warning mb-0 w-100"><i
                class="fa fa-filter mr-2"></i>Lọc
            dữ liệu</button></p>
        <button type="submit" name="button" value="reset" class="btn btn-danger mb-0 w-100"><i
                class="fa fa-close mr-2"></i>Reset</button>
    </div>
</div>
{{ html()->form()->close() }}
@section('script')
    <script>
        $(function() {
            var dateFormat = "yy/mm/dd",
                from = $("#from")
                .datepicker({
                    dateFormat: dateFormat,
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true
                })
                .on("change", function() {
                    to.datepicker("option", "minDate", getDate(this));
                }),
                to = $("#to").datepicker({
                    dateFormat: dateFormat,
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true
                })
                .on("change", function() {
                    from.datepicker("option", "maxDate", getDate(this));
                });

            function getDate(element) {
                var date;
                try {
                    date = $.datepicker.parseDate(dateFormat, element.value);
                } catch (error) {
                    date = null;
                }

                return date;
            }
            $('#from-picker').click(function(event) {
                event.preventDefault();
                $('#from').focus();
            });
            $('#top-picker').click(function(event) {
                event.preventDefault();
                $('#to').focus();
            });
        });
    </script>
@endsection
