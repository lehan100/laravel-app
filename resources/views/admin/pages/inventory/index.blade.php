@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    $active_status = config('configs.active_status');
    $stock_status = config('configs.stock_status');
    $IMAGE = new \App\Helpers\Product\Image();
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    <div class="x_panel p-0 border-0">
        <div class="x_content p-0 m-0">
            @include ('admin.pages.inventory.toolbar.filter')
            @if (count($items) > 0)
                @include ('admin.templates.notify')
                <table class="table table-striped jambo_table ">
                    <thead>
                        <tr>
                            @include('admin.templates.thead.column', ['name' => 'Sản phẩm'])
                            @include('admin.templates.thead.column', [
                                'name' => 'Mã sản phẩm',
                                'width' => '130',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => 'Trạng thái',
                                'width' => '120',
                            ])
                            @include('admin.templates.thead.column', ['name' => 'Tình trạng', 'width' => '120'])
                            @include('admin.templates.thead.column', [
                                'name' => 'Đã bán',
                                'width' => '120',
                                'class' => 'text-center',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => '<a class="border-bottom" data-toggle="tooltip" data-placement="bottom" title="Tổng số lượng hàng hiện có trong kho">Có sẵn</a>',
                                'width' => '120',
                                'class' => 'text-center',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => '<a class="border-bottom" data-toggle="tooltip" data-placement="bottom" title="Số lượng hàng trong kho có thể bán">Đang bán</a>',
                                'width' => '120',
                                'class' => 'text-center',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => '<a class="border-bottom" data-toggle="tooltip" data-placement="bottom" title="Hàng trong kho nằm trong đơn hàng chưa hoàn thành">Đã có khách đặt</a>',
                                'width' => '160',
                                'class' => 'text-center',
                            ])
                            @include('admin.templates.thead.action')
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $val)
                            @php
                                $available_quantity = $val->available_quantity;
                                $sold_quantity = $val->sold_quantity;
                                $order_quantity = $val->order_quantity;
                                $id = $val->id;
                                //Product
                                $product = $val->product;
                                $sku = $product->sku;
                                $hit_order = $product->hit_order;
                                $productName = $product->name;
                                $image = $IMAGE->getLinkDefault($product, 'small');
                                $link = route('product/form', ['id' => $product->id]);
                                $status = $active_status[$product->status];
                                $htmlStatus = sprintf('<button class="%s" type="button"><i class="%s mr-2"></i>%s</button>', $status['class'], $status['icon'], $status['name']);
                                $stock = $stock_status[$product->stock];
                                $htmlStock = sprintf('<button class="%s" type="button"><i class="%s mr-2"></i>%s</button>', $stock['class'], $stock['icon'], $stock['name']);
                                $inputHiddenID = html()->hidden('id', $val->id);
                                $inputHiddenProductID = html()->hidden('product_id', $product->id);
                            @endphp
                            {{ html()->form('POST', route('inventory/save'))->attributes([
                                'accept-charset' => 'UTF-8',
                                'enctype' => 'multipart/form-data',
                                'class' => 'form-horizontal form-label-left',
                                'id' => 'appForm_' . $val->id,
                                'id' => 'appForm',
                            ])->open() }}
                            {!! $inputHiddenID !!}
                            {!! $inputHiddenProductID !!}
                            <tr class="ondblclick">
                                <td>
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <img width="60" src="{{ $image }}" alt="{{ $productName }}">
                                        </div>
                                        <div class="col">
                                            <strong>{{ $productName }} <a target="_blank" href="{{ $link }}"><i
                                                        class="ml-2 text-info fa fa-eye"></i></a></strong>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $sku }}</td>
                                <td>{!! $htmlStatus !!}</td>
                                <td>{!! $htmlStock !!}</td>
                                <td class="text-center">{!! $hit_order !!}</td>
                                <td class="text-center"><input data-order-qty="{{ $order_quantity }}"
                                        id="available_quantity_{{ $id }}"
                                        data-before="#sold_quantity_{{ $id }}" type="number"
                                        name="available_quantity" readonly
                                        class="form-control-plaintext text-center available_quantity"
                                        value="{{ $available_quantity }}"></td>
                                <td class="text-center"><input data-order-qty="{{ $order_quantity }}"
                                        id="sold_quantity_{{ $id }}"
                                        data-before="#available_quantity_{{ $id }}" type="number"
                                        name="sold_quantity" readonly
                                        class="form-control-plaintext text-center sold_quantity"
                                        value="{{ $sold_quantity }}"></td>
                                <td class="text-center">{{ $order_quantity }}</td>
                                <td class="text-center nowrap">
                                    <button data-link="{{ route('inventory/history', ['id' => $id]) }}" type="button"
                                        class="btn-history btn-sm btn btn-warning"><i class="fa fa-history"></i></button>
                                    <button type="button" class="btn-edit btn-sm btn btn-info"><i
                                            class="fa fa-pencil mr-2"></i>Cập nhật</button>
                                    <button type="submit" class="btn-save btn-sm btn btn-success"><i
                                            class="fa fa-save mr-2"></i>Lưu lại</button>
                                    <button type="button" class="btn-close btn-sm btn btn-danger"><i
                                            class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            {{ html()->form()->close() }}
                        @endforeach
                    </tbody>
                </table>
                @include('pagination.pagination_admin')
            @else
                @include('admin.templates.list_empty')
            @endif
        </div>
    </div>
@endsection
@section('style')
    <style>
        .border-bottom{
            border-bottom: 1px dotted #dee2e6!important;
        }
        .btn-save,
        .btn-close {
            display: none
        }

        .nowrap {
            white-space: nowrap
        }

        tr.active {

            & .btn-save,
            .btn-close {
                display: inline-block;
                margin: auto;
            }

            & .btn-edit {
                display: none;
            }
        }

        .table.opended tr:not(.active) {
            opacity: .3;
            position: relative;
            left: 0;

            &:after {
                content: "";
                width: 100%;
                height: 100%;
                position: absolute;
                top: 0;
                left: 0;
                background-color: rgba(0, 0, 0, .5);
            }
        }

        .timeline {
            max-height: 500px;
            overflow-y: scroll;
            & .tag {
                height: 40px;

                &:after {
                    border-top: 20px solid transparent;
                    border-bottom: 20px solid transparent;
                }

                & span {
                    white-space: pre-wrap;
                }
            }

            & h2.title:before {
                top: 18px;
            }
        }
    </style>
@endsection
@section('script')
    <script>
        const TableEvent = {
            selector: {
                table: '.table',
                tableActive: 'opended',
                tableTrActive: 'active',
                btnEdit: ".btn-edit",
                btnClose: ".btn-close",
                btnHistory: ".btn-history",
                modalHistory: "#modalHistory",
                form: '.appForm',
                input: {
                    active: 'form-control',
                    inactive: 'form-control-plaintext',
                    inactive_attr: 'readonly',
                    available_qty: '.available_quantity',
                    sold_qty: '.sold_quantity'
                }
            },
            opended: function(selector) {
                $(this.selector.table + " tr").removeClass("active");
                $(selector).parents("tr").toggleClass(this.selector.tableTrActive);
                $(this.selector.table).addClass(this.selector.tableActive);
                $(selector).parents("tr").find("input").removeClass(this.selector.input.inactive).addClass(this
                    .selector.input
                    .active).removeAttr(this.selector.input.inactive_attr);
            },
            closed: function(selector) {
                $(this.selector.table + " tr").removeClass("active");
                $(this.selector.table).removeClass(this.selector.tableActive);
                $(selector).parents("tr").find("input").removeClass(this.selector.input.active).addClass(this
                    .selector.input
                    .inactive).attr(this.selector.input.inactive_attr, true);
            },
            closed_all: function() {
                $(TableEvent.selector.table + " tr").removeClass("selected");
                $(TableEvent.selector.table + " tr.active").addClass("selected").removeClass("active");
                $(TableEvent.selector.table).removeClass(TableEvent.selector.tableActive);
                $(this.selector.table + " tr").find("input").removeClass(this.selector.input.active).addClass(this
                    .selector.input
                    .inactive).attr(this.selector.input.inactive_attr, true);
            },
            history: function(link = '') {
                if (link != '') {
                    $.ajax({
                        url: link,
                        type: "GET",
                        dataType: "json",
                        beforeSend: function() {
                            $("#loading").show();
                        },
                        success: function(res) {
                            $("#loading").hide();
                            if (res.status == true) {
                                TableEvent.modalHistory(res.data);
                            }
                        }
                    });
                }
            },
            modalHistory: function(data) {
                var timeline = '';
                $.each(data.timeline, function(i, elm) {
                    timeline += `
                   <li>
                        <div class="block">
                            <div class="tags">
                                <a href="" class="tag">
                                    <span>${elm.date}</span>
                                </a>
                            </div>
                            <div class="block_content">
                                <h2 class="title">
                                    <a>${elm.comment}</a>
                                </h2>
                                <div class="byline">
                                    by <a>${elm.modify}</a>
                                </div>
                             </div>
                        </div>
                    </li>
                   `;
                });
                var xhtml = `
                    <div class="text-center px-4 border-bottom">
                        <p><img src="${data.product.picture}"/></p>
                        <p><strong>${data.product.name}</strong></p>
                    </div>
                    <ul id="list-timeline" class="d-print-none list-unstyled timeline my-4 text-left">
                        ${timeline}
                    </ul>
                `;
                $(this.selector.modalHistory + " .modal-body").html(xhtml);
                $(this.selector.modalHistory).modal("show");
            },
            changeSold: function(available, sold, order_qty = 0) {
                let available_qty = parseInt($(available).val());
                let sold_qty = parseInt($(sold).val());
                order_qty = parseInt(order_qty);
                if (available_qty != (sold_qty - order_qty)) {
                    $(sold).val(available_qty - order_qty);
                }
            },
            changeAvailable: function(available, sold, order_qty = 0) {
                let available_qty = parseInt($(available).val());
                let sold_qty = parseInt($(sold).val());
                order_qty = parseInt(order_qty);
                if (available_qty < (sold_qty + order_qty)) {
                    $(available).val(sold_qty + order_qty);
                }
            },
            msgpopup: function(mess, type = 'success') {
                $().msgpopup({
                    text: mess,
                    type: type,
                    time: 6000,
                    x: true
                });
            },
            init: function() {
                let selector = this;
                $(this.selector.btnEdit).click(function() {
                    selector.opended(this);
                });
                $(this.selector.btnClose).click(function() {
                    selector.closed(this);
                });
                $(this.selector.btnHistory).click(function() {
                    var link = $(this).data("link");
                    selector.history(link);
                });
                $(document).stop().on("change paste keyup", this.selector.input.available_qty, function() {
                    let available = this;
                    let sold = $(this).data("before");
                    let order_qty = $(this).data('order-qty');
                    TableEvent.changeSold(available, sold, order_qty);
                });
                $(document).stop().on("change paste keyup", this.selector.input.sold_qty, function() {
                    let sold = this;
                    let available = $(this).data("before");
                    let order_qty = $(this).data('order-qty');
                    TableEvent.changeAvailable(available, sold, order_qty);
                });
            }
        };
        $(document).ready(function() {
            TableEvent.init();
            $(".appForm").on('submit', (function(e) {
                var selectorForm = $(this);
                e.preventDefault();
                var link = $(this).attr("action");
                $.ajax({
                    url: link,
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: "json",
                    beforeSend: function() {
                        $("#loading").show();
                    },
                    success: function(res) {
                        $("#loading").hide();
                        if (res.status == true) {
                            var mess = `<div class="message-popup row align-items-center"><div class="col-auto"><div class="checkmark">
                            <svg class="checkmark success" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark_circle_success" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark_check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" stroke-linecap="round"></path></svg></div>
                          </div><div class="col ps-1">Đã cập nhật thành công</div></div>`;
                            TableEvent.msgpopup(mess);
                            TableEvent.closed_all();

                        } else {
                            var mess = `<div class="message-popup row align-items-center"><div class="col-auto"><div class="checkmark">
                            <svg class="checkmark error" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark_circle_error" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark_check" stroke-linecap="round" fill="none" d="M16 16 36 36 M36 16 16 36
                                "></path></svg>
                          </div>
                          </div><div class="col ps-1">Đã cập nhật thất bại</div></div>`;
                            TableEvent.msgpopup(mess, 'error');
                        }

                    }
                });
            }));
        });
    </script>
    <div class="modal" tabindex="-1" id="modalHistory" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-history mr-2"></i>Lịch sử cập nhật</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection
