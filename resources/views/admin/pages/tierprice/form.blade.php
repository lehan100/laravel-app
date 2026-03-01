@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    use Illuminate\Support\Carbon;
    use App\Helpers\Price as Price;
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $option_type = config('coupon.option_type');
    $inputHiddenID = html()->hidden('id', @$item['id']);
    $inputHiddenRollback = html()
        ->hidden('rollback', 0)
        ->attributes(['id' => 'rollback']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $status_value = isset($item['status']) ? $item['status'] : 0;
    $date_from = isset($item['date_from']) ? Carbon::parse($item['date_from'])->format('d/m/Y') : '';
    $date_to = isset($item['date_to']) ? Carbon::parse($item['date_to'])->format('d/m/Y') : '';
    $elementsGeneral = [
        [
            'label' => html()
                ->label(for: 'status', contents: 'Duyệt')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->checkbox('status', $status_value, $status)
                ->attributes(['class' => 'js-switch']),
        ],

        [
            'label' => html()
                ->label(for: 'date_from', contents: 'Thời gian bắt đầu')
                ->attributes(['class' => $formLabelAttr]),
            'element' =>
                '<div class="input-group">' .
                html()
                    ->text('date_from', $date_from)
                    ->attributes([
                        'id' => 'from',
                        'class' => $errors->first('date_from') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                    ]) .
                '<span id="from-picker" class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span></div>',
            'error' => $errors->first('date_from')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('date_from'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'date_to', contents: 'Thời gian kết thúc')
                ->attributes(['class' => $formLabelAttr]),
            'element' =>
                '<div class="input-group">' .
                html()
                    ->text('date_to', $date_to)
                    ->attributes([
                        'id' => 'to',
                        'class' => $errors->first('date_to') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                    ]) .
                '<span id="to-picker" class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span></div>',
            'error' => $errors->first('date_to')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('date_to'))
                : '',
        ],
        [
            'element' => $inputHiddenID . $inputHiddenRollback,
        ],
    ];
    $defaultLoadDataProduct = @$item->items ? 'loadDefaultDataProduct' : '';
    $defaultLoadDataOption = @$item->items ? 'loadDefaultData' : '';
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    @include ('admin.templates.notify')
    {{ html()->form('POST', route("$controllerName/save"))->attributes([
            'accept-charset' => 'UTF-8',
            'enctype' => 'multipart/form-data',
            'id' => 'appForm',
        ])->open() }}
    <div class="row">
        <div class="col-12 col-md-3 mb-3">
            <div class="nav flex-column nav-pills bg-white" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="border rounded-0 py-3 nav-link active" id="v-pills-config-tab" data-toggle="pill"
                    data-target="#v-pills-config" type="button" role="tab" aria-controls="v-pills-config"
                    aria-selected="true"><i class="font-l fa fa-cogs mr-2"></i>General</a>
                <a class="border rounded-0 py-3 nav-link @php echo $defaultLoadDataProduct; @endphp"
                    id="v-pills-product-tab" data-toggle="pill" data-target="#v-pills-product" type="button" role="tab"
                    aria-controls="v-pills-product" aria-selected="true"><i
                        class="font-l fa fa-clipboard mr-2"></i>Products</a>
                <a class="border rounded-0 py-3 nav-link @php echo $defaultLoadDataOption; @endphp" id="v-pills-action-tab"
                    data-toggle="pill" data-target="#v-pills-action" type="button" role="tab"
                    aria-controls="v-pills-action" aria-selected="false"><i class="font-l fa fa-money mr-2"></i>Action</a>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="tab-content p-4 border bg-white" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-config" role="tabpanel"
                    aria-labelledby="v-pills-config-tab">
                    <div class="w-75">{!! FormTemplate::show($elementsGeneral) !!}</div>
                </div>
                <div class="tab-pane fade show" id="v-pills-product" role="tabpanel" aria-labelledby="v-pills-product-tab">
                    <div>@include('admin.pages.tierprice.plugins.product')</div>
                </div>
                <div class="tab-pane fade show" id="v-pills-action" role="tabpanel" aria-labelledby="v-pills-action-tab">
                    @include('admin.pages.tierprice.plugins.option')
                </div>
            </div>
        </div>
    </div>
    </div>
    {{ html()->form()->close() }}
@endsection
@section('script')
    <script src="{{ asset('admin/vendors/vakata-jstree/dist/jstree.min.js') }}" type="text/javascript"></script>
    <!-- Datatables -->
    <!--
                                                                        
                                                                        Load Data Tier Price Option
                                                                        -->
    <script src="{{ asset('admin/DataTables/datatables.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            OptionTierPrice.init();
            ProductSelect.init();
            var dateFormat = "dd/mm/yy",
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
            $('#to-picker').click(function(event) {
                event.preventDefault();
                $('#to').focus();
            });
            $('#from').trigger("change");
            //Tabs
            $(".nav-pills a").click(function() {
                let id = $("#id").val();
                let productID = '@php echo $defaultLoadDataProduct; @endphp';
                let optionID = '@php echo $defaultLoadDataOption; @endphp';
                if ($(this).hasClass(productID)) {
                    ProductSelect.loadDataProducts(id);
                    $(this).removeClass(productID);
                }
                if ($(this).hasClass(optionID)) {
                    OptionTierPrice.loadDataOptions(id);
                    $(this).removeClass(optionID);
                }
            });

        });
        const OptionTierPrice = {
            selector: {
                type: "#type",
                buttom: "#add_option",
                delete: ".option_delete",
                loadValueTable: '#loadValueTable',
                loadOptionTierPrice: '#loadDataPrice',
                inputDelete: "#tier_price_option_delete",
                inputHiddenData: "#tier_price_option_data",
            },
            data: {
                qty: [],
                dataOption: []
            },
            gernerateHTML(special_price_class = '', special_percent_class = '') {
                var html = ` <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th width="120">Qty Order</th>
                            <th class="special-price ${special_price_class}">Special Price</th>
                            <th class="special-percent ${special_percent_class}">Special Percent</th>
                            <th width="50px">Xóa</th>
                        </tr>
                    </thead>
                    <tbody id='loadDataPrice'></tbody></table>`;
                return html;
            },
            gernerateHTMLBody(special_price_class = '', special_percent_class = '', qty = '', option_id = -1,
                special_price = '', special_percent = '') {
                var html = `
                        <tr>
                            <td><input type="text" name="option_qty[]" value="${qty}" class="option_qty form-control" placeholder="Qty">
                            </td>
                            <td class="special-price ${special_price_class}">
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" name="option_special_price[]" class="form-control"
                                        placeholder="Special Price" value="${special_price}" aria-label="Special Price" aria-describedby="basic-addon1">
                                </div>
                            </td>
                            <td class="special-percent ${special_percent_class}">
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <input type="text" name="option_special_percent[]" class="form-control"
                                        placeholder="Special Percent" value="${special_percent}" aria-label="Special Percent" aria-describedby="basic-addon1">
                                </div>
                            </td>
                            <td>
                                <button data-id='${option_id}' type="button" class="option_delete btn btn-default p-0 m-0 text-danger" title="Delete Value"><i
                                        class="fa fa-trash-o"></i></button>
                                        <input type="hidden" name="tier_price_option_id[]" value="${option_id}">
                                        </td>
                        </tr>
                 `;
                return html;
            },
            verifyData: function() {
                OptionTierPrice.data.qty = [];
                $(".option_qty").each(function() {
                    let val = $(this).val();
                    OptionTierPrice.data.qty.push(val);
                });
            },
            error: function() {
                $(this.selector.type).addClass("is-invalid");
                $(".type-error").remove();
                $(this.selector.type).after(
                    `<div class="invalid-feedback type-error text-left">Vui lòng không được để trống</div>`);
            },
            isvalid: function() {
                $(this.selector.type).addClass("is-valid").removeClass("is-invalid");
                $(".type-error").remove();
            },
            validation: function() {
                var type = $(this.selector.type).val();
                if (type == '') {
                    this.error();
                } else {
                    this.isvalid();
                }
            },
            loadDataOptions: function(tier_price_id = -1) {
                if (tier_price_id > 0) {
                    let url = '{{ route('tierPrice/listOptions') }}';
                    $.ajax({
                        url: url,
                        type: "GET",
                        contentType: false,
                        dataType: "json",
                        data: {
                            'id': tier_price_id
                        },
                        beforeSend: function() {
                            Loading.show();
                        },
                        success: function(f) {
                            Loading.hide();
                            if (f.status == true && f.data.length > 0) {
                                OptionTierPrice.data.dataOption = f.data;
                                $.each(OptionTierPrice.data.dataOption, function(e) {
                                    let type = this['type'];
                                    let order_qty = this['order_qty'];
                                    let id = this['id'];
                                    let special_price = this['special_price'];
                                    let special_percent = this['special_percent'];
                                    if (e == 0) {
                                        $(OptionTierPrice.selector.type).val(type).trigger(
                                            'change');
                                        if (type == 0) {
                                            $(OptionTierPrice.selector.loadValueTable).html(
                                                OptionTierPrice.gernerateHTML('',
                                                    'd-none'));
                                        } else if (type == 1) {
                                            $(OptionTierPrice.selector.loadValueTable).html(
                                                OptionTierPrice.gernerateHTML(
                                                    'd-none', ''));
                                        }
                                    }
                                    if (type == 0) {
                                        $(OptionTierPrice.selector.loadOptionTierPrice).append(
                                            OptionTierPrice.gernerateHTMLBody('',
                                                'd-none', order_qty, id, special_price));
                                    } else if (type == 1) {
                                        $(OptionTierPrice.selector.loadOptionTierPrice).append(
                                            OptionTierPrice.gernerateHTMLBody(
                                                'd-none', '', order_qty, id, '',
                                                special_percent));
                                    }
                                });
                            }
                        }
                    });
                }
            },
            events: function() {
                $(document).on('change', this.selector.type, function() {
                    OptionTierPrice.verifyData();
                    OptionTierPrice.validation();
                    var type = $(this).val();
                    if (type == 0) {
                        $(OptionTierPrice.selector.loadValueTable).html(OptionTierPrice.gernerateHTML('',
                            'd-none'));
                    } else if (type == 1) {
                        $(OptionTierPrice.selector.loadValueTable).html(OptionTierPrice.gernerateHTML(
                            'd-none', ''));
                    }
                    if (OptionTierPrice.data.qty.length > 0) {
                        $(OptionTierPrice.selector.loadOptionTierPrice).html('');
                        $.each(OptionTierPrice.data.qty, function() {
                            if (type == 0) {
                                $(OptionTierPrice.selector.loadOptionTierPrice).append(
                                    OptionTierPrice.gernerateHTMLBody('',
                                        'd-none', this));
                            } else if (type == 1) {
                                $(OptionTierPrice.selector.loadOptionTierPrice).append(
                                    OptionTierPrice.gernerateHTMLBody(
                                        'd-none', '', this));
                            }
                        });
                    }
                });
                $(document).on('click', this.selector.buttom, function() {
                    var type = $(OptionTierPrice.selector.type).val();
                    OptionTierPrice.validation();
                    if (type == 0) {
                        $(OptionTierPrice.selector.loadOptionTierPrice).append(OptionTierPrice
                            .gernerateHTMLBody('', 'd-none', ''));
                    } else if (type == 1) {
                        $(OptionTierPrice.selector.loadOptionTierPrice).append(OptionTierPrice
                            .gernerateHTMLBody('d-none', '', ''));
                    }
                });
                $(document).on('click', this.selector.delete, function() {
                    var id = $(this).data("id");
                    if (id > -1) {
                        let tier_price_option_delete = $(OptionTierPrice.selector.inputDelete).val();
                        if (tier_price_option_delete != "") {
                            tier_price_option_delete = tier_price_option_delete.split(",");
                        } else {
                            tier_price_option_delete = [];
                        }
                        tier_price_option_delete.push(id);
                        $(OptionTierPrice.selector.inputDelete).val(tier_price_option_delete.join(","));
                    }
                    $(this).parents("tr").remove();
                });
            },
            init: function() {
                this.events();
            }

        };
        const ProductSelect = {
            selector: {
                btnSelect: "#selectProduct"
            },
            modal: {
                popup: "#modalProduct",
                title: "#modalProduct .modal-title",
                content: "#modalProduct #loadData",
                saveProduct: ".modalSaveProduct",
            },
            inputData: {
                product: "#data-product-ids",
            },
            delete: {
                product: '.condition-product',
                input: "#product_ids_delete"
            },
            loadData: {
                product: "#loadDataProduct",
            },
            dataTable: null,
            dataTableReview: null,
            dataResult: null,
            getProduct: function(dataSelected = '', datatable = true) {
                let url = '{{ route('product/productSelect') }}';
                $.ajax({
                    url: url,
                    type: "GET",
                    contentType: false,
                    dataType: "json",
                    data: {
                        'selected': dataSelected
                    },
                    beforeSend: function() {
                        Loading.show();
                    },
                    success: function(f) {
                        Loading.hide();
                        if (f.status == true) {
                            ProductSelect.dataResult = f.data;
                            if (datatable == true) {
                                ProductSelect.modalDataTable(f.data);
                            }
                        }
                    }
                });
            },
            modalDataTable: function(data) {
                let xhtml = `
                            <table id="productDataTable" class=" table table-striped jambo_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="align-middle sorting_disabled text-center"><input type="checkbox" id="checkAllDataTable"></th>
                                        <th class="align-middle">Picture</th>
                                        <th class="align-middle name"></th>
                                        <th class="align-middle sku"></th>
                                        <th class="align-middle dataPrice">Price</th>
                                        <th width="100" class="align-middle category_id"></th>
                                        <th class="align-middle stock">Stock</th>
                                        <th class="align-middle status">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   `;

                $.each(data, function() {
                    if (this.special_price != 0) {
                        xhtml += `
                                            <tr id="row_${this.id}">
                                                <td></td>
                                                <td>${this.picture}</td>
                                                <td>${this.name}<span class="d-none">${this.alias}</span></td>
                                                <td>${this.sku}</td>
                                                <td>
                                                    <div class="text-decoration-line-through text-secondary">${this.price}</div>
                                                    <div class="text-danger font-weight-bold">${this.special_price}</div>
                                                </td>
                                                <td>${this.category}</td>
                                                <td>${this.stock}</td>
                                                <td>${this.status}</td>
                                            </tr>
                                        `;
                    } else {
                        xhtml += `
                                            <tr id="row_${this.id}">
                                                <td></td>
                                                <td>${this.picture}</td>
                                                <td>${this.name}<span class="d-none">${this.alias}</span></td>
                                                <td>${this.sku}</td>
                                                <td>
                                                    <div class="text-danger font-weight-bold">${this.price}</div>
                                                </td>
                                                <td>${this.category}</td>
                                                <td>${this.stock}</td>
                                                <td>${this.status}</td>
                                            </tr>
                                        `;
                    }

                });
                xhtml += `</tbody>
                            </table>
                            `;
                ProductSelect.showModal('Sản phẩm', xhtml);
                ProductSelect.dataTable = new DataTable('#productDataTable', {
                    columnDefs: [{
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    }, {
                        "orderable": false,
                        "targets": [0, 1, 2, 3, 5, 6, 7]
                    }],

                    select: {
                        style: 'multi',
                        selector: 'td:first-child'
                    },
                    initComplete: function(settings, json) {
                        // Add select filter

                        $('#productDataTable thead .category_id').append(
                            '<select class="form-control form-control-sm no-sort" id="category_id"></select>'
                        );
                        $('#productDataTable thead .stock').html(
                            '<select class="form-control form-control-sm no-sort" id="search_stock"></select>'
                        );
                        $('#productDataTable thead .status').html(
                            '<select class="form-control form-control-sm no-sort" id="search_status"></select>'
                        );
                        $('#productDataTable thead .name').append(
                            '<input type="text" class="form-control form-control-sm no-sort" placeholder="Search by Name" id="search_name"/>'
                        );
                        $('#productDataTable thead .sku').append(
                            '<input type="text" class="form-control form-control-sm no-sort" placeholder="Search by Sku" id="search_sku"/>'
                        );
                        var obj = new Array();
                        $('#category_id').append(
                            '<option value="all">All items</option>');
                        $('#search_stock').append(
                            '<option value="all">All Stocks</option><option value="Còn hàng">Còn hàng</option><option value="Tạm hết hàng">Tạm hết hàng</option>'
                        );
                        $('#search_status').append(
                            '<option value="all">All Status</option><option value="Kích hoạt">Kích hoạt</option><option value="Tạm ẩn">Tạm ẩn</option>'
                        );
                        $.each(data, function() {
                            if (obj.indexOf(this.category_id) == -1) {
                                obj.push(this.category_id);
                                $('#category_id').append(
                                    '<option value="' + this
                                    .category_id +
                                    '">' +
                                    this.category + '</option>');
                            }
                        });

                        // Filter results on select change
                        $('#category_id').on('change', function() {
                            var text = $("#category_id option:selected")
                                .text();
                            if ($(this).val() == 'all') {
                                text = "";
                            }
                            ProductSelect.dataTable.columns(5).search(
                                text).draw();
                        });
                        $('#search_stock').on('change', function() {
                            var text = $("#search_stock option:selected")
                                .text();
                            if ($(this).val() == 'all') {
                                text = "";
                            }
                            ProductSelect.dataTable.columns(6).search(
                                text).draw();
                        });
                        $('#search_status').on('change', function() {
                            var text = $("#search_status option:selected")
                                .text();
                            if ($(this).val() == 'all') {
                                text = "";
                            }
                            ProductSelect.dataTable.columns(7).search(
                                text).draw();
                        });
                        $('th.name').on('change keyup paste', '#search_name',
                            function(e) {
                                var text = $(this).val();
                                ProductSelect.dataTable.columns(2).search(
                                    text).draw();
                            });
                        $('th.sku').on('change keyup paste', '#search_sku',
                            function(e) {
                                var text = $(this).val();
                                ProductSelect.dataTable.columns(3).search(
                                    text).draw();
                            });
                    }
                });
                let selected = $(ProductSelect.inputData.product).val();
                if (selected != "") {
                    selected = selected.split(",");
                    $.each(selected, function() {
                        let rowid = "#row_" + this;
                        ProductSelect.dataTable.rows(rowid).select();
                    });
                }
            },
            showModal: function(title, content, mode = 'product') {
                $(this.modal.title).html(title);
                $(this.modal.content).html(content);
                $.getScript("{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}",
                    function(data,
                        textStatus, jqxhr) {
                        jQuery(ProductSelect.modal.popup).modal("show");
                    });

            },
            hideModal: function() {
                $(this.modal.popup).modal("hide");
            },
            loadDataProducts: function(tier_price_id = -1) {
                if (tier_price_id > 0) {
                    this.getProduct('', false);
                    let url = '{{ route('tierPrice/listProducts') }}';
                    $.ajax({
                        url: url,
                        type: "GET",
                        contentType: false,
                        dataType: "json",
                        data: {
                            'id': tier_price_id
                        },
                        beforeSend: function() {
                            Loading.show();
                        },
                        success: function(f) {
                            Loading.hide();
                            if (f.status == true && f.data.length > 0) {
                                if (f.data.length > 0) {
                                    let ids = f.data.map(function(e) {
                                        return e.product_id;
                                    });
                                    $(ProductSelect.inputData.product).val(ids.join(","));
                                    var xhtml = `
                            <table id="reviewProductDataTable" class="table table-striped jambo_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th width="300px">Tên sản phẩm</th>
                                        <th>Sku</th>
                                        <th>Giá</th>
                                         <th class="text-nowrap">Danh mục</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>`;
                                    $.each(f.data, function() {
                                        let id = this['product_id'];
                                        var item = ProductSelect.dataResult.find(function(e) {
                                            return e.id == id;
                                        });
                                        // let rowid = "#row_" + id;
                                        // ProductSelect.dataResult.rows(rowid).select();

                                        let x_price = "";
                                        if (item['special_price'] != 0) {
                                            x_price =
                                                `<div class="text-decoration-line-through text-secondary">${item['price']}</div>
														<div class="text-danger font-weight-bold">${item['special_price']}</div>`;
                                        } else {
                                            x_price =
                                                `<div class="text-danger font-weight-bold">${item['price']}</div>`;
                                        }
                                        xhtml += `
                                        <tr>
                                            <td>${item['picture']}</td>
                                            <td>${item['name']}</td>
                                            <td>${item['sku']}</td>
                                            <td>${x_price}</td>
                                            <td>${item['category']}</td>
                                            <td><a data-id="${id}" href="javascript:;" class="condition-product text-nowrap text-danger"><i class="fa fa-trash mr-2"></i>Xóa</a></td>
                                        </tr>
                                    `;
                                    });
                                    xhtml += `</tbody>
                            </table>
                            `;
                                    $(ProductSelect.loadData.product).html(xhtml);

                                    ProductSelect.dataTableReview = new DataTable(
                                        '#reviewProductDataTable', {
                                            columnDefs: [{
                                                "orderable": false,
                                                "targets": [0, 5]
                                            }]
                                        });
                                } else {
                                    var xhtml = "Chưa có sản phẩm nào được chọn!";
                                }
                            }
                        }
                    });
                }
            },
            events: function() {
                $(document).on("click", this.selector.btnSelect, function(e) {
                    ProductSelect.getProduct();
                });
                $(document).on("click", this.modal.saveProduct, function(e) {
                    ProductSelect.hideModal();
                    // Check Data
                    var ids = ProductSelect.dataTable.rows({
                        selected: true
                    }).ids();

                    data = ids.join(",");
                    data = data.replace(/row_/g, "");
                    $(ProductSelect.inputData.product).val(data);
                    // Append Data
                    var dataSelected = ProductSelect.dataTable.rows({
                        selected: true
                    }).data();
                    if (dataSelected.length > 0) {
                        var xhtml = `
                            <table id="reviewProductDataTable" class="table table-striped jambo_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th width="300px">Tên sản phẩm</th>
                                        <th>Sku</th>
                                        <th>Giá</th>
                                         <th class="text-nowrap">Danh mục</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>`;
                        $.each(dataSelected, function() {

                            let id = this['DT_RowId'].replace(/row_/g, "");
                            let item = ProductSelect.dataResult.find(function(e) {
                                return e.id == id;
                            });

                            xhtml += `
                                            <tr>
                                                <td>${this[1]}</td>
                                                <td>${this[2]}</td>
                                                <td>${this[3]}</td>
                                                <td>${this[4]}</td>
                                                <td>${this[5]}</td>
                                                <td><a data-id="${id}" href="javascript:;" class="condition-product text-nowrap text-danger"><i class="fa fa-trash mr-2"></i>Xóa</a></td>
                                            </tr>
                                        `;
                        });
                        xhtml += `</tbody>
                            </table>
                            `;
                        $(ProductSelect.loadData.product).html(xhtml);

                        ProductSelect.dataTableReview = new DataTable('#reviewProductDataTable', {
                            columnDefs: [{
                                "orderable": false,
                                "targets": [0, 5]
                            }]
                        });
                    } else {
                        var xhtml = "Chưa có sản phẩm nào được chọn!";
                    }

                });
                $(document).on("click", "#checkAllDataTable", function() {
                    if ($(this).is(":checked")) {
                        ProductSelect.dataTable.rows({
                            search: 'applied'
                        }).every(function(rowIdx, tableLoop, rowLoop) {
                            if (ProductSelect.dataTable.rows({
                                    selected: true
                                }).count() < 20) {
                                ProductSelect.dataTable.row(this).select();
                            }
                        });
                    } else {
                        ProductSelect.dataTable.rows({
                            search: 'applied'
                        }).every(function(rowIdx, tableLoop, rowLoop) {
                            if (ProductSelect.dataTable.rows({
                                    selected: false
                                }).count() < 20) {
                                ProductSelect.dataTable.row(this).deselect();
                            }
                        });

                    }
                });
                $(document).on("click", this.delete.product, function() {
                    let id = $(this).data('id');
                    let ids = $(ProductSelect.inputData.product).val();
                    ids = ids.split(",");
                    var index = ids.indexOf(id.toString());
                    if (index > -1) {
                        ids.splice(index, 1);
                        let data = ids.join(",");

                        let product_ids_delete = $(ProductSelect.delete.input).val();
                        if (product_ids_delete != "") {
                            product_ids_delete = product_ids_delete.split(",");
                        } else {
                            product_ids_delete = [];
                        }
                        product_ids_delete.push(id);
                        $(ProductSelect.delete.input).val(product_ids_delete.join(","));

                        $(ProductSelect.inputData.product).val(data);
                        //$(this).parents("tr").remove();
                        ProductSelect.dataTableReview.row($(this).parents('tr')).remove().draw();
                        if (ids.length == 0) {
                            $(ProductSelect.loadData.product).html(
                                "Chưa có sản phẩm nào được chọn!");
                        }
                    }
                });
            },
            init: function() {
                this.events();

            }
        };
    </script>
    <div class="modal" id="modalProduct" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <p class="text-right p-2 bg-light"><button type="button" class="modalSaveProduct btn btn-primary">Hoàn
                        tất</button></p>
                <div class="modal-body" id="loadData"></div>
                <div class="modal-footer">
                    <button type="button" id="modalSaveProduct" class="modalSaveProduct btn btn-primary">Hoàn tất</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('style')
    <link href="{{ asset('admin/DataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/vendors/vakata-jstree/dist/themes/default/style.css') }}" rel="stylesheet">
    <style>
        .sorting_disabled:before,
        .sorting_disabled::after {
            display: none !important;
        }

        .sorting_disabled {
            padding: 0.75rem !important;
        }
    </style>
@endsection
