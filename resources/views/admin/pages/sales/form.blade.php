@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    use Illuminate\Support\Carbon;
    use App\Helpers\Price as Price;
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $inputHiddenID = html()->hidden('id', @$item['id'])->attributes( ['id' => 'sale_id']);
    $inputHiddenRollback = html()->hidden('rollback', 0)->attributes( ['id' => 'rollback']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $is_homepage = isset($item['is_homepage']) && $item['is_homepage'] == 1 ? true : false;
    $date_from = isset($item['date_from']) ? Carbon::parse($item['date_from'])->format('d/m/Y') : '';
    $date_to = isset($item['date_to']) ? Carbon::parse($item['date_to'])->format('d/m/Y') : '';
    $elementsGeneral = [
        [
            'label' => html()->label(for:'status', contents:'Duyệt')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->checkbox('status', @$item['status'], $status)->attributes( ['class' => 'js-switch']),
        ],
        [
            'label' => html()->label(for:'is_homepage', contents:'Public Homepage')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->checkbox('is_homepage', @$item['is_homepage'], $is_homepage)->attributes( ['class' => 'js-switch']),
        ],
        [
            'label' => html()->label(for:'name', contents:'Tên chương trình')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->text('name', @$item['name'])->attributes( ['class' => $errors->first('name') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('name') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('name')) : '',
        ],
        [
            'label' => html()->label(for:'description', contents:'Mô tả')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->textarea('description', @$item['description'])->attributes( ['class' => $errors->first('description') ? $formInputAttr . ' is-invalid' : $formInputAttr, 'rows' => 4, 'cols' => 54]),
            'error' => $errors->first('description') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('description')) : '',
        ],
        [
            'label' => html()->label(for:'date_from', contents:'Thời gian bắt đầu')->attributes( ['class' => $formLabelAttr]),
            'element' =>
                '<div class="input-group">' .
                html()->text('date_from', $date_from)->attributes( ['id' => 'from', 'class' => $errors->first('date_from') ? $formInputAttr . ' is-invalid' : $formInputAttr]) .
                '<span id="from-picker" class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span></div>',
            'error' => $errors->first('date_from') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('date_from')) : '',
        ],
        [
            'label' => html()->label(for:'date_to', contents:'Thời gian kết thúc')->attributes(['class' => $formLabelAttr]),
            'element' =>
                '<div class="input-group">' .
                html()->text('date_to', $date_to)->attributes( ['id' => 'to', 'class' => $errors->first('date_to') ? $formInputAttr . ' is-invalid' : $formInputAttr]) .
                '<span id="to-picker" class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span></div>',
            'error' => $errors->first('date_to') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('date_to')) : '',
        ],
        [
            'element' => $inputHiddenID . $inputHiddenRollback,
        ],
    ];

    $elementsAction = [];
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
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-action-tab" data-toggle="pill"
                    data-target="#v-pills-action" type="button" role="tab" aria-controls="v-pills-action"
                    aria-selected="false"><i class="font-l fa fa-money mr-2"></i>Actions</a>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="tab-content p-4 border bg-white" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-config" role="tabpanel"
                    aria-labelledby="v-pills-config-tab">
                    <div class="w-75">{!! FormTemplate::show($elementsGeneral) !!}</div>
                </div>
                <div class="tab-pane fade" id="v-pills-action" role="tabpanel" aria-labelledby="v-pills-action-tab">
                    <div class="w-100">
                        @include('admin.pages.sales.plugin.actions')
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
    {{ html()->form()->close() }}
@endsection
@section('script')
    <!-- Datatables -->
    <script src="{{ asset('admin/DataTables/datatables.min.js') }}"></script>

    <script type="text/javascript">
        function sleep(miliseconds) {
            var currentTime = new Date().getTime();
            while (currentTime + miliseconds >= new Date().getTime()) {}
        }
        const ProductSelect = {
            selector: {
                btnSelectProduct: "#btn-add-product",
            },
            loadData: {
                product: "#list-product",
                hiddenData: "#condition_sales",
                loadProductCheckSku: "#loadProductCheckSku",
                loadDataSelectCopy: "#loadDataSelectCopy"
            },
            inputData: {
                product: "#data-product-ids",
            },
            delete: {
                product: '.condition-product',
                input: "#product_ids_delete"
            },
            add: {
                product: '.condition-cog',
            },
            modal: {
                popup: "#modalProduct",
                title: "#modalProduct .modal-title",
                content: "#modalProduct #loadData",
                popup_sales: "#modalSales",
                popup_sales_title: "#modalSales .modal-title",
                saveProduct: ".modalSaveProduct",
                popup_sales_save: ".btnmodalSales",
                input: {
                    is_quantity_is_uses_product: '.is_quantity_is_uses_product',
                    quantity_is_uses_product: '.quantity_is_uses_product',
                    is_special_price: '.is_special_price',
                    is_copy: '.is_copy',
                    special_price: ".special_price",
                    special_percent: '.special_percent',
                    is_gift: '.is_gift',
                    buy_qty: '.buy_qty',
                    gift_qty: '.gift_qty',
                    gift_sku: '.gift_sku',
                    check_sku_gift: "#check_sku_gift",
                },
                data: {
                    quantity_is_uses_product: 0,
                    final_price: 0,
                    special_price: 0,
                    special_percent: 0,
                    buy_qty: 0,
                    gift_qty: 0,
                    gift_sku: '',
                    product_id: 0,
                    date_from: '',
                    date_to: '',
                }
            },
            dataTable: null,
            dataTableCopy: null,
            dataResult: null,
            dataTableReview: null,
            dataSales: [],
            reset: function() {
                this.modal.data = {
                    quantity_is_uses_product: 0,
                    final_price: 0,
                    special_price: 0,
                    special_percent: 0,
                    buy_qty: 0,
                    gift_qty: 0,
                    gift_sku: '',
                    product_id: 0
                }
            },
            resetSalesInput: function() {
                $(ProductSelect.modal.input.is_quantity_is_uses_product).filter('[value="0"]').prop("checked", true)
                    .trigger("click");
                $(ProductSelect.modal.input.is_special_price).filter('[value="0"]').prop("checked", true)
                    .trigger("click");
                $(ProductSelect.modal.input.is_gift).filter('[value="0"]').prop("checked", true)
                    .trigger("click");
                $(ProductSelect.modal.input.quantity_is_uses_product).val(0);
                $(ProductSelect.modal.input.special_price).val(0);
                $(ProductSelect.modal.input.special_percent).val(0);
                $(ProductSelect.modal.input.buy_qty).val(0);
                $(ProductSelect.modal.input.gift_qty).val(0);
                $(ProductSelect.modal.input.gift_sku).val('');
                $(ProductSelect.loadData.loadProductCheckSku).html("").hide();
            },
            showModal: function(title, content, mode = 'product') {
                $(this.modal.title).html(title);
                $(this.modal.content).html(content);
                $.getScript("{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}", function(data,
                    textStatus, jqxhr) {
                    jQuery(ProductSelect.modal.popup).modal("show");
                });

            },
            hideModal: function() {
                $(this.modal.popup).modal("hide");
            },
            showModalSales: function(title, id) {
                $(this.modal.popup_sales_title).html(title);
                this.modal.data.product_id = id;
                let item = ProductSelect.dataResult.find(function(e) {
                    return e.id == id;
                });
                $("#note_qty").html(item.qty);
                $("#note_price").html(FormatNumber(item.final_price + "") + "&nbsp;₫");
                $(this.modal.input.quantity_is_uses_product).attr("data-max", item.qty);
                $(this.modal.input.special_price).attr("data-max", item.final_price);
                let objIndex = ProductSelect.dataSales.findIndex(obj => obj.product_id == id);
                ProductSelect.resetSalesInput();
                if (objIndex != -1) {
                    let item = ProductSelect.dataSales[objIndex];
                    if (item.quantity_is_uses_product != 0) {
                        $(ProductSelect.modal.input.is_quantity_is_uses_product).filter('[value="1"]').prop(
                                "checked", true)
                            .trigger("click");
                    }
                    if (item.special_price != 0) {
                        $(ProductSelect.modal.input.is_special_price).filter('[value="1"]').prop("checked",
                                true)
                            .trigger("click");
                    }
                    if (item.special_percent != 0) {
                        $(ProductSelect.modal.input.is_special_price).filter('[value="2"]').prop("checked",
                                true)
                            .trigger("click");
                    }
                    if (item.buy_qty != 0 || item.gift_qty != 0 || item.gift_sku != "") {
                        $(ProductSelect.modal.input.is_gift).filter('[value="1"]').prop("checked", true)
                            .trigger("click");
                    }

                    $(ProductSelect.modal.input.quantity_is_uses_product).val(item.quantity_is_uses_product);
                    $(ProductSelect.modal.input.special_price).val(FormatNumber(item.special_price));
                    $(ProductSelect.modal.input.special_percent).val(item.special_percent);
                    $(ProductSelect.modal.input.buy_qty).val(item.buy_qty);
                    $(ProductSelect.modal.input.gift_qty).val(item.gift_qty);
                    $(ProductSelect.modal.input.gift_sku).val(item.gift_sku);
                    if (item.gift_sku == "" || item.gift_sku == null) {
                        $(ProductSelect.modal.input.check_sku_gift).hide();
                    } else {
                        $(ProductSelect.modal.input.check_sku_gift).show().trigger("click");
                    }
                }
                $.getScript("{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}", function(data,
                    textStatus, jqxhr) {
                    jQuery(ProductSelect.modal.popup_sales).modal("show");
                });

            },
            hideModalSales: function() {
                let invalid = false;
                $(ProductSelect.modal.popup_sales).find("input").removeClass("is-invalid");
                if ($(ProductSelect.modal.input.is_quantity_is_uses_product).filter(":checked").val() ==
                    1) {
                    if ($(ProductSelect.modal.input.quantity_is_uses_product).val() < 0 || $(ProductSelect.modal.input.quantity_is_uses_product).val() =='') {
                        invalid = true;
                        $(ProductSelect.modal.input.quantity_is_uses_product).addClass("is-invalid");
                    }

                }
                if ($(ProductSelect.modal.input.is_special_price).filter(":checked").val() == 1) {
                    if ($(ProductSelect.modal.input.special_price).val() == 0) {
                        invalid = true;
                        $(ProductSelect.modal.input.special_price).addClass("is-invalid");
                    }
                }
                if ($(ProductSelect.modal.input.is_special_price).filter(":checked").val() == 2) {
                    if ($(ProductSelect.modal.input.special_percent).val() == 0) {
                        invalid = true;
                        $(ProductSelect.modal.input.special_percent).addClass("is-invalid");
                    }
                }
                if ($(ProductSelect.modal.input.is_gift).filter(":checked").val() == 1) {
                    if ($(ProductSelect.modal.input.buy_qty).val() == 0) {
                        invalid = true;
                        $(ProductSelect.modal.input.buy_qty).addClass("is-invalid");
                    }
                    if ($(ProductSelect.modal.input.gift_qty).val() == 0) {
                        invalid = true;
                        $(ProductSelect.modal.input.gift_qty).addClass("is-invalid");
                    }
                    if ($(ProductSelect.modal.input.gift_sku).val() == "" || $(ProductSelect.modal.input.gift_sku)
                        .val() == null) {
                        invalid = true;
                        $(ProductSelect.modal.input.gift_sku).addClass("is-invalid");
                    }
                }
                if (!invalid) {
                    $(this.modal.popup_sales).modal("hide");
                }
                return invalid;
            },
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
            loadProductSales: function(sale_id = 0) {
                if (sale_id > 0) {
					
                    let url = '{{ route('sales/listSales') }}';
                    $.ajax({
                        url: url,
                        type: "GET",
                        contentType: false,
                        dataType: "json",
                        data: {
                            'id': sale_id
                        },
                        beforeSend: function() {
                            //Loading.show();
                        },
                        success: function(f) {
                            Loading.hide();
                            if (f.status == true && f.data.length > 0) {
								setTimeout(function(){
									ProductSelect.dataSales = f.data;									
									ProductSelect.updateData();
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
											<th class="text-nowrap">Cài đặt</th>
											<th></th>
										</tr>
									</thead>
									<tbody>`;
								
									$.each(ProductSelect.dataSales, function() {
										
										let id = this['product_id'];									
										var item = ProductSelect.dataResult.find(function(e) {
											return e.id == id;
										});
										let objIndex = ProductSelect.dataSales.findIndex(obj => obj
											.product_id == id);
										let xreview = "";
										let xreviewClass = "";
										if (objIndex != -1) {
											xreviewClass = 'alert alert-success p-2';
											let sale = ProductSelect.dataSales[objIndex];
											if (sale.quantity_is_uses_product >= 0) {
												xreview +=
													`<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i><strong>${sale.quantity_is_uses_product}/${item.qty}</strong> sản phẩm</p>`;
											} else {
												xreview +=
													`<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i><strong>${item.qty}/${item.qty}</strong> sản phẩm</p>`;
											}
											if (sale.special_price > 0) {
												xreview +=
													`<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i>Giá bán: <strong>${FormatNumber(sale.special_price)}&nbsp;₫</strong></p>`;
											}
											if (sale.special_percent > 0) {
												xreview +=
													`<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i>Giảm giá: <strong>${sale.special_percent}%</strong></p>`;
											}
											if (sale.buy_qty > 0 || sale.gift_qty > 0) {
												xreview +=
													`<p class="mb-1"><i class="fa fa-check-square-o mr-2"></i>Mua <strong>${sale.buy_qty}</strong> tặng <strong>${sale.gift_qty}</strong> sản phẩm có sku: <strong>${sale.gift_sku}</strong></p>`;
											}
										}
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
													<td>
														${x_price}
													</td>
													<td>${item['category']}</td>
													<td>
														<div class="review ${xreviewClass}">${xreview}</div>
													   <a data-id="${id}" href="javascript:;" class="condition-cog text-nowrap text-info my-1"><i class="fa fa-cog mr-2"></i>Cài đặt</a>
													</td>
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
												"targets": [0, 5, 6]
											}]
										});
								},500);
                            }
                        }
                    });
                }
            },
            loadDataSelectCopy: function() {
                let id = ProductSelect.modal.data.product_id;
                let ids = $(ProductSelect.inputData.product).val();
                ids = ids.split(",");
                let data = this.dataResult;
                //console.log(data);
                let xhtml = `
                            <table id="productDataTableCopy" class=" table table-striped jambo_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="align-middle sorting_disabled text-center"><input type="checkbox" id="checkAllDataTableCopy"></th>
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
                    if (ids.indexOf(this.id.toString()) == -1 && data.id != id) {
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
                    }

                });
                xhtml += `</tbody>
                            </table>
                            `;
                $(ProductSelect.loadData.loadDataSelectCopy).append(xhtml);
                ProductSelect.dataTableCopy = new DataTable('#productDataTableCopy', {
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

                        $('#productDataTableCopy thead .category_id').append(
                            '<select class="form-control form-control-sm no-sort" id="copy_category_id"></select>'
                        );
                        $('#productDataTableCopy thead .stock').html(
                            '<select class="form-control form-control-sm no-sort" id="copy_search_stock"></select>'
                        );
                        $('#productDataTableCopy thead .status').html(
                            '<select class="form-control form-control-sm no-sort" id="copy_search_status"></select>'
                        );
                        $('#productDataTableCopy thead .name').append(
                            '<input type="text" class="form-control form-control-sm no-sort" placeholder="Search by Name" id="copy_search_name"/>'
                        );
                        $('#productDataTableCopy thead .sku').append(
                            '<input type="text" class="form-control form-control-sm no-sort" placeholder="Search by Sku" id="copy_search_sku"/>'
                        );
                        var obj = new Array();
                        $('#copy_category_id').append(
                            '<option value="all">All items</option>');
                        $('#copy_search_stock').append(
                            '<option value="all">All Stocks</option><option value="Còn hàng">Còn hàng</option><option value="Tạm hết hàng">Tạm hết hàng</option>'
                        );
                        $('#copy_search_status').append(
                            '<option value="all">All Status</option><option value="Kích hoạt">Kích hoạt</option><option value="Tạm ẩn">Tạm ẩn</option>'
                        );
                        $.each(data, function() {
                            if (obj.indexOf(this.category_id) == -1) {
                                obj.push(this.category_id);
                                $('#copy_category_id').append(
                                    '<option value="' + this
                                    .category_id +
                                    '">' +
                                    this.category + '</option>');
                            }
                        });

                        // Filter results on select change
                        $('#copy_category_id').on('change', function() {
                            var text = $("#copy_category_id option:selected")
                                .text();
                            if ($(this).val() == 'all') {
                                text = "";
                            }
                            ProductSelect.dataTableCopy.columns(5).search(
                                text).draw();
                        });
                        $('#copy_search_stock').on('change', function() {
                            var text = $("#copy_search_stock option:selected")
                                .text();
                            if ($(this).val() == 'all') {
                                text = "";
                            }
                            ProductSelect.dataTableCopy.columns(6).search(
                                text).draw();
                        });
                        $('#copy_search_status').on('change', function() {
                            var text = $("#copy_search_status option:selected")
                                .text();
                            if ($(this).val() == 'all') {
                                text = "";
                            }
                            ProductSelect.dataTableCopy.columns(7).search(
                                text).draw();
                        });
                        $('th.name').on('change keyup paste', '#copy_search_name',
                            function(e) {
                                var text = $(this).val();
                                ProductSelect.dataTableCopy.columns(2).search(
                                    text).draw();
                            });
                        $('th.sku').on('change keyup paste', '#copy_search_sku',
                            function(e) {
                                var text = $(this).val();
                                ProductSelect.dataTableCopy.columns(3).search(
                                    text).draw();
                            });

                    }
                });
                // ProductSelect.dataTableCopy.on('select', function(e, dt, type, indexes) {
                //     let rowData = ProductSelect.dataTableCopy.rows(indexes).data().toArray();

                //     console.log(rowData);
                // })
                // ProductSelect.dataTableCopy.on('deselect', function(e, dt, type, indexes) {
                //     let rowData = ProductSelect.dataTableCopy.rows(indexes).data().toArray();

                //     console.log(rowData);
                // });
            },
            reviewProductDataTable: function() {
                var data = ProductSelect.dataSales;
                if (data.length > 0) {
                    var xhtml = `
                            <table id="reviewProductDataTable" class="table table-striped jambo_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th width="300px">Tên sản phẩm</th>
                                        <th>Sku</th>
                                        <th>Giá</th>
                                        <th class="text-nowrap">Danh mục</th>
                                        <th class="text-nowrap">Cài đặt</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>`;
                    $.each(data, function() {

                        let id = this['product_id'];
                        let item = ProductSelect.dataResult.find(function(e) {
                            return e.id == id;
                        });
                        let xreview = "";
                        let xreviewClass = 'alert alert-success p-2';
                        let sale = this;
                        if (sale.quantity_is_uses_product >= 0) {
                            xreview +=
                                `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i><strong>${sale.quantity_is_uses_product}/${item.qty}</strong> sản phẩm</p>`;
                        } else {
                            xreview +=
                                `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i><strong>${item.qty}/${item.qty}</strong> sản phẩm</p>`;
                        }
                        if (sale.special_price > 0) {
                            xreview +=
                                `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i>Giá bán: <strong>${FormatNumber(sale.special_price)}&nbsp;₫</strong></p>`;
                        }
                        if (sale.special_percent > 0) {
                            xreview +=
                                `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i>Giảm giá: <strong>${sale.special_percent}%</strong></p>`;
                        }
                        if (sale.buy_qty > 0 || sale.gift_qty > 0) {
                            xreview +=
                                `<p class="mb-1"><i class="fa fa-check-square-o mr-2"></i>Mua <strong>${sale.buy_qty}</strong> tặng <strong>${sale.gift_qty}</strong> sản phẩm có sku: <strong>${sale.gift_sku}</strong></p>`;
                        }
                        if (item.special_price != 0) {
                            xhtml += `
                                <tr>
                                    <td>${item.picture}</td>
                                    <td>${item.name}</td>
                                    <td>${item.sku}</td>
                                    <td>
                                        <div class="text-decoration-line-through text-secondary">${item.price}</div>
                                        <div class="text-danger font-weight-bold">${item.special_price}</div>    
                                    </td>
                                    <td>${item.category}</td>
                                    <td>
                                        <div class="review ${xreviewClass}">${xreview}</div>
                                        <a data-id="${id}" href="javascript:;" class="condition-cog text-nowrap text-info my-1"><i class="fa fa-cog mr-2"></i>Cài đặt</a>
                                    </td>
                                    <td><a data-id="${id}" href="javascript:;" class="condition-product text-nowrap text-danger"><i class="fa fa-trash mr-2"></i>Xóa</a></td>
                                </tr>
                            `;
                        } else {
                            xhtml += `
                                    <tr>
                                        <td>${item.picture}</td>
                                        <td>${item.name}</td>
                                        <td>${item.sku}</td>
                                        <td>
                                            <div class="text-danger font-weight-bold">${item.price}</div>
                                        </td>
                                        <td>${item.category}</td>
                                        <td>
                                            <div class="review ${xreviewClass}">${xreview}</div>
                                            <a data-id="${id}" href="javascript:;" class="condition-cog text-nowrap text-info my-1"><i class="fa fa-cog mr-2"></i>Cài đặt</a>
                                        </td>
                                        <td><a data-id="${id}" href="javascript:;" class="condition-product text-nowrap text-danger"><i class="fa fa-trash mr-2"></i>Xóa</a></td>
                                    </tr>
                                `;
                        }

                    });
                    xhtml += `</tbody>
                            </table>
                            `;
                    $(ProductSelect.loadData.product).empty().html(xhtml);

                    ProductSelect.dataTableReview = new DataTable('#reviewProductDataTable', {
                        columnDefs: [{
                            "orderable": false,
                            "targets": [0, 5, 6]
                        }]
                    });
                }
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
            updateData: function() {
                $(this.loadData.hiddenData).val(JSON.stringify(this.dataSales, null, 2));
            },

            init: function() {
                console.log("Init ProductSelect");
                let sale_id = $("#sale_id").val();
                if (sale_id > 0) {
                    setTimeout(() => {
                        ProductSelect.getProduct("", false);
                        ProductSelect.loadProductSales(sale_id);
                    }, 200);
                }
                $(document).on("click", this.selector.btnSelectProduct, function() {
                    let ids = $(ProductSelect.inputData.product).val();
                    ProductSelect.getProduct(ids);
                    ProductSelect.reset();
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
                $(this.modal.input.special_price).on("change keyup focus paste", function() {
                    var val = GetNumber($(this).val());
                    var max = $(this).data("max");
                    if ($(this).val() == 0) {
                        $(this).val('');
                    }
                    if (val > max) {
                        $(this).val(FormatNumber(max + ""));
                    }
                    $(ProductSelect.modal.input.is_special_price).filter('[value="1"]').prop("checked",
                        true).trigger(
                        "click");
                });
                $("input[name='special_percent']").on("change keyup focus paste", function() {
                    var val = GetNumber($(this).val());
                    val = val.replace(/^0+/, '');
                    $(ProductSelect.modal.input.is_special_price).filter('[value="2"]').prop("checked",
                        true).trigger(
                        "click");
                    if (val == 0) {
                        $(this).val('');
                    }
                    if (val > 100) {
                        $(this).val(100);
                    }
                });

                $(this.modal.input.buy_qty).on(
                    "change keyup focus paste",
                    function() {
                        if ($(this).val() == 0) {
                            $(this).val('');
                        }
                        $(ProductSelect.modal.input.is_gift).filter('[value="1"]').prop("checked", true)
                            .trigger("click");
                    });
                $(this.modal.input.gift_qty).on(
                    "change keyup focus paste",
                    function() {
                        if ($(this).val() == 0) {
                            $(this).val('');
                        }
                        $(ProductSelect.modal.input.is_gift).filter('[value="1"]').prop("checked", true)
                            .trigger("click");
                    });
                $(this.modal.input.gift_sku).on(
                    "change keyup focus paste",
                    function() {
                        if ($(this).val() == '') {
                            $(ProductSelect.modal.input.check_sku_gift).hide();
                        } else {
                            $(ProductSelect.modal.input.check_sku_gift).show();
                        }
                        $(ProductSelect.modal.input.is_gift).filter('[value="1"]').prop("checked", true)
                            .trigger("click");
                    });
                $(this.modal.input.quantity_is_uses_product).on("change keyup focus paste", function() {
                    var val = $(this).val();
                    var max = $(this).data("max");
                    $(ProductSelect.modal.input.is_quantity_is_uses_product).filter('[value="1"]').prop(
                            "checked", true)
                        .trigger("click");
                    if (val < 0) {
                        $(this).val(0);
                    }
                    if (val > max) {
                        $(this).val(max);
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
                        let objIndex = ProductSelect.dataSales.findIndex(obj => obj
                            .product_id == id);
                        if (objIndex != -1) {
                            ProductSelect.dataSales.splice(objIndex, 1);
                            ProductSelect.updateData();
                        }
                        //$(this).parents("tr").remove();
                        ProductSelect.dataTableReview.row($(this).parents('tr')).remove().draw();
                        if (ids.length == 0) {
                            $(ProductSelect.loadData.product).html("Chưa có sản phẩm nào được chọn!");
                        }
                    }
                });

                $(document).on("click", this.modal.saveProduct, function(e) {
                    ProductSelect.hideModal();
                    // Check Data
                    var ids = ProductSelect.dataTable.rows({
                        selected: true
                    }).ids();

                    data = ids.join(",");
                    data = data.replace(/row_/g, "");
                    // Remove item setup data neu bo chon san pham
                    var ids_old = $(ProductSelect.inputData.product).val();
                    ids_old = ids_old.split(",");
                    $.each(ids_old, function(i, e) {
                        let validate = ids.indexOf("row_" + e);
                        if (validate === -1) {
                            let objIndex = ProductSelect.dataSales.findIndex(obj => obj
                                .product_id == e);
                            if (objIndex != -1) {
                                ProductSelect.dataSales.splice(objIndex, 1);
                                ProductSelect.updateData();
                            }

                        }
                    });
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
                                        <th class="text-nowrap">Cài đặt</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>`;
                        $.each(dataSelected, function() {

                            let id = this['DT_RowId'].replace(/row_/g, "");
                            let item = ProductSelect.dataResult.find(function(e) {
                                return e.id == id;
                            });
                            let objIndex = ProductSelect.dataSales.findIndex(obj => obj
                                .product_id == id);
                            let xreview = "";
                            let xreviewClass = "";
                            if (objIndex != -1) {
                                xreviewClass = 'alert alert-success p-2';
                                let sale = ProductSelect.dataSales[objIndex];
                                if (sale.quantity_is_uses_product >= 0) {
                                    xreview +=
                                        `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i><strong>${sale.quantity_is_uses_product}/${item.qty}</strong> sản phẩm</p>`;
                                } else {
                                    xreview +=
                                        `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i><strong>${item.qty}/${item.qty}</strong> sản phẩm</p>`;
                                }
                                if (sale.special_price > 0) {
                                    xreview +=
                                        `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i>Giá bán: <strong>${FormatNumber(sale.special_price)}&nbsp;₫</strong></p>`;
                                }
                                if (sale.special_percent > 0) {
                                    xreview +=
                                        `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i>Giảm giá: <strong>${sale.special_percent}%</strong></p>`;
                                }
                                if (sale.buy_qty > 0 || sale.gift_qty > 0) {
                                    xreview +=
                                        `<p class="mb-1"><i class="fa fa-check-square-o mr-2"></i>Mua <strong>${sale.buy_qty}</strong> tặng <strong>${sale.gift_qty}</strong> sản phẩm có sku: <strong>${sale.gift_sku}</strong></p>`;
                                }
                            }

                            xhtml += `
                                            <tr>
                                                <td>${this[1]}</td>
                                                <td>${this[2]}</td>
                                                <td>${this[3]}</td>
                                                <td>${this[4]}</td>
                                                <td>${this[5]}</td>
                                                <td>
                                                    <div class="review ${xreviewClass}">${xreview}</div>
                                                   <a data-id="${id}" href="javascript:;" class="condition-cog text-nowrap text-info my-1"><i class="fa fa-cog mr-2"></i>Cài đặt</a>
                                                </td>
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
                                "targets": [0, 5, 6]
                            }]
                        });
                    } else {
                        var xhtml = "Chưa có sản phẩm nào được chọn!";
                    }
                });
                $(document).on("click", this.add.product, function() {
                    if (ProductSelect.dataResult.length <= 0) {
                        let ids = $(ProductSelect.inputData.product).val();
                        ProductSelect.getProduct(ids, false);
                    }
                    let id = $(this).data("id");
                    // Check xem co data setup chua, neu chua co thi reset
                    ProductSelect.dataTableCopy = null;
                    $(ProductSelect.modal.input.is_copy).filter('[value="0"]').prop("checked", true)
                        .trigger("click");
                    $(ProductSelect.loadData.loadDataSelectCopy).empty();
                    ProductSelect.showModalSales("Cài đặt khuyến mãi", id);

                });
                $(document).on("click", ProductSelect.modal.input.check_sku_gift, function() {
                    var sku = $(ProductSelect.modal.input.gift_sku).val();
                    if (sku != null) {
                        let sku_list = sku.split(",");
                        $(ProductSelect.loadData.loadProductCheckSku).empty();
                        $.each(sku_list, function() {
                            let sku = this;
                            let item = ProductSelect.dataResult.find(function(e) {
                                return e.sku == sku;
                            });
                            let xhtml = `
                                <div class="col-12 col-md-6">
                                    <div class="border p-2 rounded mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-auto">${item.picture}</div>
                                            <div class="col pl-0">
                                                <p class="mb-1"><span class="badge badge-danger mr-2">Quà tặng</span><strong>${item.name}</strong></p>
                                                ${item.stock} ${item.status}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $(ProductSelect.loadData.loadProductCheckSku).append(xhtml).show();
                        });
                    }
                });
                $(document).on("click", this.modal.popup_sales_save, function() {
                    let invalid = ProductSelect.hideModalSales();
                    let id = ProductSelect.modal.data.product_id;
                    let item = ProductSelect.dataResult.find(function(e) {
                        return e.id == id;
                    });
                    let data = {
                        quantity_is_uses_product: 0,
                        final_price: item.final_price,
                        special_price: 0,
                        special_percent: 0,
                        buy_qty: 0,
                        gift_qty: 0,
                        gift_sku: '',
                        product_id: id
                    };
                    let xhtml = "";
                    if ($(ProductSelect.modal.input.is_quantity_is_uses_product).filter(":checked").val() ==
                        1) {
                        data.quantity_is_uses_product = $(ProductSelect.modal.input
                                .quantity_is_uses_product)
                            .val();
                        xhtml +=
                            `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i><strong>${data.quantity_is_uses_product}/${item.qty}</strong> sản phẩm</p>`;
                    } else {
                        data.quantity_is_uses_product = item.qty;
                        xhtml +=
                            `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i><strong>${item.qty}/${item.qty}</strong> sản phẩm</p>`;
                    }
                    if ($(ProductSelect.modal.input.is_special_price).filter(":checked").val() == 1) {
                        data.special_price = GetNumber($(ProductSelect.modal.input
                            .special_price).val());
                        xhtml +=
                            `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i>Giá bán: <strong>${FormatNumber(data.special_price)}&nbsp;₫</strong></p>`;
                    }
                    if ($(ProductSelect.modal.input.is_special_price).filter(":checked").val() == 2) {
                        data.special_percent = GetNumber($(ProductSelect.modal.input
                            .special_percent).val());
                        xhtml +=
                            `<p class="text-nowrap mb-1"><i class="fa fa-check-square-o mr-2"></i>Giảm giá: <strong>${data.special_percent}%</strong></p>`;
                    }
                    if ($(ProductSelect.modal.input.is_gift).filter(":checked").val() == 1) {
                        data.buy_qty = $(ProductSelect.modal.input.buy_qty).val();
                        data.gift_qty = $(ProductSelect.modal.input.gift_qty).val();
                        data.gift_sku = $(ProductSelect.modal.input.gift_sku).val();
                        if (data.buy_qty > 0 || data.gift_qty > 0) {
                            xhtml +=
                                `<p class="mb-1"><i class="fa fa-check-square-o mr-2"></i>Mua <strong>${data.buy_qty}</strong> tặng <strong>${data.gift_qty}</strong> sản phẩm có sku: <strong>${data.gift_sku}</strong></p>`;
                        }
                    }

                    let dataSales = ProductSelect.dataSales;
                    if (dataSales.length > 0) {
                        let objIndex = ProductSelect.dataSales.findIndex(obj => obj.product_id == id);
                        if (objIndex === -1) {
                            ProductSelect.dataSales.push(data);
                        } else {
                            ProductSelect.dataSales[objIndex].quantity_is_uses_product = data
                                .quantity_is_uses_product;
                            ProductSelect.dataSales[objIndex].special_price = data.special_price;
                            ProductSelect.dataSales[objIndex].special_percent = data.special_percent;
                            ProductSelect.dataSales[objIndex].buy_qty = data.buy_qty;
                            ProductSelect.dataSales[objIndex].gift_qty = data.gift_qty;
                            ProductSelect.dataSales[objIndex].gift_sku = data.gift_sku;
                        }
                    } else {
                        ProductSelect.dataSales.push(data);
                    }
                    // Copy Setup
                    if ($(ProductSelect.modal.input.is_copy).filter(":checked").val() == 1 && !invalid) {
                        var dataSelected = ProductSelect.dataTableCopy.rows({
                            selected: true
                        }).ids();

                        $.each(dataSelected, function() {
                            var id = this.replace(/row_/g, "");
                            var dataCopy = {
                                ...data
                            };
                            var item = ProductSelect.dataResult.find(function(e) {
                                return e.id == id;
                            });
                            var objIndex = ProductSelect.dataSales.findIndex(obj => obj
                                .product_id == id);
                            if (objIndex == -1) {
                                dataCopy.product_id = item.id;
                                dataCopy.final_price = item.final_price;
                                var special_price = dataCopy.special_price;
                                if (special_price != 0) {
                                    special_price = parseInt(dataCopy.special_price
                                        .replace(".",
                                            ""));
                                }

                                if (item.final_price < special_price) {
                                    dataCopy.special_price = 0;
                                }
                                var is_quantity_is_uses_product = $(ProductSelect.modal.input
                                    .is_quantity_is_uses_product).filter(":checked").val();
                                if (item.qty < dataCopy.quantity_is_uses_product ||
                                    is_quantity_is_uses_product == 0) {
                                    dataCopy.quantity_is_uses_product = item.qty;
                                }
                                ProductSelect.dataSales.push(dataCopy);
                            }
                        });
                        ProductSelect.reviewProductDataTable();
                    }
                    // Review message to TableDataSet
                    $(ProductSelect.add.product + '[data-id="' + id + '"]').prev('.review').addClass(
                        "alert alert-success p-2").html(xhtml);
                    //Data
                    ProductSelect.updateData();
                });
                $(document).on("change", this.modal.input.is_copy, function() {
                    let val = $(this).val();
                    if (val == 1) {
                        ProductSelect.loadDataSelectCopy();
                    }
                });
            }
        };
        $(document).ready(function() {
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
        });
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
    <div class="modal" id="modalSales" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <p class="text-right p-2 bg-light"><button type="button" class="btnmodalSales btn btn-primary">Hoàn
                        tất</button></p>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-2 col-sm-3 control-label"><strong>Số lượng </strong>
                            <br>
                            <small><strong><span class="text-success"> Khả
                                        dụng <span id="note_qty"></span></span></strong></small>
                        </label>
                        <div class="col-md-10 col-sm-9 ">
                            <div class="radio">
                                <label>
                                    <input class="is_quantity_is_uses_product" type="radio" checked="" value="0"
                                        name="is_quantity_is_uses_product">
                                    Tất cả số lượng khả dụng có trong kho
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <div class="row align-items-center">
                                        <div class="col-auto pr-0"><input class="is_quantity_is_uses_product" type="radio"
                                                value="1" name="is_quantity_is_uses_product"> </div>
                                        <div class="col-auto pl-2 pr-0">Giới hạn số lượng </div>
                                        <div class="col-auto"> <input type="number" min="0"
                                                class="quantity_is_uses_product form-control form-control-sm"
                                                name="quantity_is_uses_product" value="0" style="width: 80px"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label class="col-md-2 col-sm-3 control-label"><strong>Giảm giá </strong>
                            <br>
                            <small><strong><span class="text-success">Giảm tối đa <span
                                            id="note_price"></span></span></strong></small>
                        </label>
                        <div class="col-md-10 col-sm-9 ">
                            <div class="radio">
                                <label>
                                    <input class="is_special_price" type="radio" checked="" value="0"
                                        name="is_special_price">
                                    Không cài đặt
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <div class="row align-items-center">
                                        <div class="col-auto pr-0"><input class="is_special_price" type="radio"
                                                value="1" name="is_special_price">
                                        </div>
                                        <div class="col-auto pl-2 pr-0">Giá bán</div>
                                        <div class="col-auto"> <input type="text"
                                                class="special_price form-control form-control-sm" name="special_price"
                                                value="0" style="width: 80px"
                                                onkeyup="this.value = FormatNumber(this.value);"></div>
                                    </div>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <div class="row align-items-center">
                                        <div class="col-auto pr-0"><input class="is_special_price" type="radio"
                                                value="2" name="is_special_price">
                                        </div>
                                        <div class="col-auto pl-2 pr-0">Phần trăm</div>
                                        <div class="col-auto"> <input min="0" type="number"
                                                class="special_percent form-control form-control-sm"
                                                name="special_percent" value="0" style="width: 80px"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label class="col-md-2 col-sm-3 control-label"><strong>Tặng sản phẩm</strong>
                            <br>
                            <small class="text-navy">Tặng quà đi kèm khi mua một số lượng nhất định. Sản
                                phẩm tặng điền sku và cách nhau bởi dấu phẩy</small>
                        </label>
                        <div class="col-md-10 col-sm-9 ">
                            <div class="radio">
                                <label>
                                    <input class="is_gift" type="radio" checked="" value="0" name="is_gift">
                                    Không cài đặt
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <div class="row align-items-center">
                                        <div class="col-auto pr-0"><input class="is_gift" type="radio" value="1"
                                                name="is_gift"> </div>
                                        <div class="col-auto pl-2 pr-0">Mua</div>
                                        <div class="col-auto"> <input type="number" min="0"
                                                class="buy_qty form-control form-control-sm" name="buy_qty"
                                                value="0" style="width: 80px"></div>
                                        <div class="col-auto pl-1 pr-0">Tặng</div>
                                        <div class="col-auto"> <input type="number" min="0"
                                                class="gift_qty form-control form-control-sm" name="gift_qty"
                                                value="0" style="width: 80px"></div>
                                        <div class="col-12 my-2"></div>
                                        <div class="col-auto pr-0">SKU sản phẩm tặng là</div>
                                        <div class="col-auto"> <input type="text"
                                                class="gift_sku form-control form-control-sm" name="gift_sku"
                                                placeholder="sku1,sku2,..." style="width: 200px"></div>
                                        <div class="col-auto"><button id="check_sku_gift"
                                                class="btn btn-success btn-sm"><i class="fa fa-check mr-2"></i>Check sản
                                                phẩm</button></div>
                                    </div>
                                    <div id="loadProductCheckSku" class="row align-items-center my-3"
                                        style="display: none"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label class="col-md-2 col-sm-3 control-label"><strong>Sao shép</strong>
                            <br>
                            <small class="text-navy">Áp dụng cài đặt cho sản phẩm khác</small>
                        </label>
                        <div class="col-md-10 col-sm-9">
                            <div class="radio">
                                <label>
                                    <input class="is_copy" type="radio" checked="" value="0" name="is_copy">
                                    Không sao chép
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input class="is_copy" type="radio" value="1" name="is_copy">
                                    Sao chép cài đặt
                                </label>
                            </div>
                            <div id="loadDataSelectCopy" class="mt-3"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnmodalSales" class="btnmodalSales btn btn-primary">Hoàn tất</button>
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

        .review p:last-child {
            margin-bottom: 0 !important
        }
    </style>
@endsection
