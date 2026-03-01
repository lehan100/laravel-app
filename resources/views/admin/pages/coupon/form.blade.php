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
    $inputHiddenRollback = html()->hidden('rollback', 0)->attributes( ['id' => 'rollback']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $is_public = isset($item['is_public']) && $item['is_public'] == 1 ? true : false;
    $is_product_use_coupon = isset($item['is_product_use_coupon']) && $item['is_product_use_coupon'] == 1 ? true : false;
    $uses = isset($item['uses']) ? $item['uses'] : 0;
    $type_text = isset($item['type']) && $item['type'] == 1 ? '<i class="fa fa-percent"></i>' : 'VND';
    $max_uses_user = isset($item['max_uses_user']) ? $item['max_uses_user'] : 0;
    $discount_amount = isset($item['discount_amount']) ? Price::formatNumber($item['discount_amount']) : 0;
    $discount_amount_from = isset($item['discount_amount_from']) ? Price::formatNumber($item['discount_amount_from']) : 0;
    $discount_max = isset($item['discount_max']) ? Price::formatNumber($item['discount_max']) : 0;
    $date_from = isset($item['date_from']) ? Carbon::parse($item['date_from'])->format('d/m/Y') : '';
    $date_to = isset($item['date_to']) ? Carbon::parse($item['date_to'])->format('d/m/Y') : '';
    $elementsGeneral = [
        [
            'label' => html()->label(for:'status', contents:'Duyệt')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->checkbox('status', @$item['status'], $status)->attributes( ['class' => 'js-switch']),
        ],
        [
            'label' => html()->label(for:'is_public', contents:'Mã riêng tư')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->checkbox('is_public', @$item['is_public'], $is_public)->attributes( ['class' => 'js-switch']),
        ],
        [
            'label' => html()->label(for:'name', contents:'Tên mã giảm giá')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->text('name', @$item['name'])->attributes( ['class' => $errors->first('name') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('name') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('name')) : '',
        ],
        [
            'label' => html()->label(for:'coupon_code', contents:'Mã giảm giá')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->text('coupon_code', @$item['coupon_code'])->attributes( ['class' => $errors->first('coupon_code') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('coupon_code') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('coupon_code')) : '',
        ],
        [
            'label' => html()->label(for:'uses', contents:'Số lượng phát hành')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->text('uses', $uses)->attributes( ['class' => $errors->first('uses') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('uses') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('uses')) : '',
        ],
        [
            'label' => html()->label(for:'max_uses_user', contents:'Số lần sử dụng trên mỗi khách hàng')->attributes( ['class' => $formLabelAttr.' pt-0']),
            'element' => html()->text('max_uses_user', $max_uses_user)->attributes( ['class' => $errors->first('max_uses_user') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('max_uses_user') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('max_uses_user')) : '',
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
            'label' => html()->label(for:'date_to', contents:'Thời gian kết thúc')->attributes( ['class' => $formLabelAttr]),
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
    $elementsCondition = html()->checkbox('is_product_use_coupon', @$item['is_product_use_coupon'], $is_product_use_coupon)->attributes( ['class' => 'js-switch']);

    $elementsAction = [
        [
            'label' => html()->label(for:'type', contents:'Phương thức giảm giá')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->select('type', $option_type, @$item['type'])->attributes( ['id' => 'discount_type', 'class' => $errors->first('type') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('type') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('type')) : '',
        ],
        [
            'label' => html()->label(for:'discount_amount', contents:'Giảm giá')->attributes( ['class' => $formLabelAttr]),
            'element' =>
                '<div class="input-group">' .
                html()->text('discount_amount', $discount_amount)->attributes( ['id' => 'discount_amount', 'class' => $errors->first('discount_amount') ? $formInputAttr . ' is-invalid' : $formInputAttr]) .
                '<span id="discount_amount_picker" class="input-group-addon">
                ' .
                $type_text .
                '
                    </span></div>',
            'error' => $errors->first('discount_amount') ? sprintf('<div class="invalid-feedback d-block">%s</div>', $errors->first('discount_amount')) : '',
        ],
        [
            'label' => html()->label(for:'discount_amount_from', contents:'Áp dụng cho đơn hàng từ ?')->attributes( ['class' => $formLabelAttr. ' pt-0']),
            'element' =>
                '<div class="input-group">' .
                html()->text('discount_amount_from', $discount_amount_from)->attributes( ['id' => 'discount_amount_from', 'class' => $errors->first('discount_amount_from') ? $formInputAttr . ' is-invalid' : $formInputAttr]) .
                '<span id="discount_amount_picker" class="input-group-addon">
                VND
                    </span></div>',
            'error' => $errors->first('discount_amount_from') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('discount_amount_from')) : '',
        ],
        [
            'label' => html()->label(for:'discount_max', contents:'Mức giảm giá tối đa')->attributes(['class' => $formLabelAttr]),
            'element' =>
                '<div class="input-group">' .
                html()->text('discount_max', $discount_max)->attributes( ['id' => 'discount_max', 'class' => $errors->first('discount_max') ? $formInputAttr . ' is-invalid' : $formInputAttr]) .
                '<span id="discount_amount_picker" class="input-group-addon">
                VND
                    </span></div>',
            'error' => $errors->first('discount_max') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('discount_max')) : '',
        ],
    ];
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
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-conditions-tab" data-toggle="pill"
                    data-target="#v-pills-conditions" type="button" role="tab" aria-controls="v-pills-conditions"
                    aria-selected="true"><i class="font-l fa fa-check-circle mr-2"></i>Conditions</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-action-tab" data-toggle="pill"
                    data-target="#v-pills-action" type="button" role="tab" aria-controls="v-pills-action"
                    aria-selected="false"><i class="font-l fa fa-money mr-2"></i>Action</a>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="tab-content p-4 border bg-white" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-config" role="tabpanel"
                    aria-labelledby="v-pills-config-tab">
                    <div class="w-75">{!! FormTemplate::show($elementsGeneral) !!}</div>
                </div>
                <div class="tab-pane fade" id="v-pills-conditions" role="tabpanel" aria-labelledby="v-pills-conditions-tab">
                    <div class="w-100">
                        <div class="border p-3 rounded mb-3 bg-warning">
                            <div class="form-group row align-items-center m-0">
                                <div class="col-auto">
                                    {!! $elementsCondition !!}
                                </div>
                                <label for="is_product_use_coupon"
                                    class="col-form-label col-md text-left text-white p-0 label-align font-weight-bold">Kiểm
                                    tra sản phẩm có cho phép dùng mã giảm giá không?</label>

                            </div>
                            {!! FormTemplate::show($elementsCondition) !!}
                        </div>
                        @include('admin.pages.coupon.plugin.conditions')
                    </div>
                </div>
                <div class="tab-pane fade" id="v-pills-action" role="tabpanel" aria-labelledby="v-pills-action-tab">
                    <div class="w-75">{!! FormTemplate::show($elementsAction) !!}</div>
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
    <script src="{{ asset('admin/DataTables/datatables.min.js') }}"></script>

    <script type="text/javascript">
        const Condition = {
            selector: {
                button: {
                    addProduct: ".btn-condition-product",
                    addCategory: ".btn-condition-category",
                },
                delete: {
                    product: '.condition-product',
                    category: '.condition-category',
                },
                inputData: {
                    product: "#input-condition-product",
                    category: "#input-condition-category",
                },
                loadData: {
                    category: "#list-category",
                    product: "#list-product",
                },
                modal: {
                    popup: "#modalCondition",
                    title: "#modalCondition .modal-title",
                    content: "#modalCondition .modal-body",
                    saveCategory: "#modalSaveCategory",
                    saveProduct: "#modalSaveProduct",
                },
                dataTable: null,
                dataTableReview: null,
            },
            showModal: function(title, content, mode = 'product') {
                $(this.selector.modal.title).html(title);
                $(this.selector.modal.content).html(content);
                if (mode == 'product') {
                    $(Condition.selector.modal.saveCategory).hide();
                    $(Condition.selector.modal.saveProduct).show();
                } else {
                    $(Condition.selector.modal.saveCategory).show();
                    $(Condition.selector.modal.saveProduct).hide();
                }
                $.getScript("{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}", function(data,
                    textStatus, jqxhr) {
                    jQuery(Condition.selector.modal.popup).modal("show");
                });

            },
            hideModal: function() {
                $(this.selector.modal.popup).modal("hide");
            },
            loadCategory: function(ids) {
                let url = '{{ route('category/getItemsCategory') }}';
                var token = '{{ csrf_token() }}';
                var form = new FormData();
                form.append('_token', token);
                form.append('ids', ids);
                $.ajax({
                    url: url,
                    type: "POST",
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: "json",
                    data: form,
                    beforeSend: function() {
                        Loading.show();
                    },
                    success: function(f) {
                        Loading.hide();
                        if (f.status == true && f.data.length > 0) {
                            var xhtml = "";
                            $.each(f.data, function() {
                                xhtml +=
                                    `<div class="breadcrumb item-category rounded p-2 border"><strong>${this.name}</strong> <a data-id="${this.id}" href="javascript:;" class="condition-category text-danger ml-3"><i class="fa fa-trash mr-2"></i>Xóa</a></div>`;
                            });
                            $(Condition.selector.loadData.category).html(xhtml);
                        }
                    }
                });
            },
            getCategory: function(dataSelected = '') {
                let url = '{{ route('category/categorySelect') }}';
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
                            Condition.showModal("Danh mục", f.html, 'category');
                            $.getScript("{{ asset('admin/vendors/vakata-jstree/dist/jstree.min.js') }}",
                                function(data, textStatus, jqxhr) {
                                    $('#menutree .jstree-checked').attr('data-jstree',
                                        '{"selected": true }');
                                    $('#menutree').jstree({
                                        "core": {
                                            "animation": 0,
                                            "check_callback": true,
                                        },
                                        "checkbox": {
                                            "three_state": true
                                        },
                                        "plugins": [
                                            "dnd", "types", "checkbox"
                                        ]
                                    });
                                });
                        }
                    }
                });
            },
            getProduct: function(dataSelected = '') {
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
                            let xhtml = `
                            <table id="productDataTable" class=" table table-striped jambo_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="no-sort sorting_disabled text-center"><input type="checkbox" id="checkAllDataTable"></th>
                                        <th class="no-sort">Picture</th>
                                        <th>Name</th>
                                        <th>Sku</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>`
                            $.each(f.data, function() {
                                if (this.special_price != 0) {
                                    xhtml += `
                                            <tr id="row_${this.id}">
                                                <td></td>
                                                <td>${this.picture}</td>
                                                <td>${this.name}</td>
                                                <td>${this.sku}</td>
                                                <td>
                                                    <div class="text-decoration-line-through text-secondary">${this.price}</div>
                                                    <div class="text-danger font-weight-bold">${this.special_price}</div>
                                                </td>
                                                <td>${this.stock}</td>
                                                <td>${this.status}</td>
                                            </tr>
                                        `;
                                } else {
                                    xhtml += `
                                            <tr id="row_${this.id}">
                                                <td></td>
                                                <td>${this.picture}</td>
                                                <td>${this.name}</td>
                                                <td>${this.sku}</td>
                                                <td>
                                                    <div class="text-danger font-weight-bold">${this.price}</div>
                                                </td>
                                                <td>${this.stock}</td>
                                                <td>${this.status}</td>
                                            </tr>
                                        `;
                                }
                            });
                            xhtml += `</tbody>
                            </table>
                            `;
                            Condition.showModal('Sản phẩm', xhtml);
                            Condition.selector.dataTable = new DataTable('#productDataTable', {
                                columnDefs: [{
                                    orderable: false,
                                    className: 'select-checkbox',
                                    targets: 0
                                }, {
                                    "orderable": false,
                                    "targets": [0, 1]
                                }],

                                select: {
                                    style: 'multi',
                                    selector: 'td:first-child'
                                }
                            });
                            let selected = $(Condition.selector.inputData.product).val();
                            if (selected != "") {
                                selected = selected.split(",");
                                $.each(selected, function() {
                                    let rowid = "#row_" + this;
                                    Condition.selector.dataTable.rows(rowid).select();
                                });
                            }
                        }
                    }
                });
            },
            init: function() {
                $(document).on("click", this.selector.button.addCategory, function() {
                    let ids = $(Condition.selector.inputData.category).val();
                    Condition.getCategory(ids);
                });
                $(document).on("click", this.selector.delete.category, function() {
                    let id = $(this).data('id');
                    let ids = $(Condition.selector.inputData.category).val();
                    ids = ids.split(",");
                    var index = ids.indexOf(id.toString());
                    if (index > -1) {
                        ids.splice(index, 1);
                        let data = ids.join(",");
                        $(Condition.selector.inputData.category).val(data);
                        $(this).parents(".item-category").remove();
                        if (ids.length == 0) {
                            $(Condition.selector.loadData.category).html("Chưa có danh mục nào được chọn!");
                        }
                    }
                });
                $(document).on("click", Condition.selector.modal.saveCategory, function(e) {
                    var ids = [];
                    var data = "";
                    var selectedNodes = $('#menutree').jstree("get_selected", true);
                    $.each(selectedNodes, function() {
                        ids.push(this.id);
                    });
                    Condition.hideModal();
                    data = ids.join(",");
                    $(Condition.selector.inputData.category).val(data);
                    Condition.loadCategory(data);
                });
                $(document).on("click", Condition.selector.modal.saveProduct, function(e) {
                    Condition.hideModal();
                    var ids = Condition.selector.dataTable.rows({
                        selected: true
                    }).ids();
                    data = ids.join(",");
                    data = data.replace(/row_/g, "");
                    $(Condition.selector.inputData.product).val(data);
                    //Append Data
                    var dataSelected = Condition.selector.dataTable.rows({
                        selected: true
                    }).data();
                    if (dataSelected.length > 0) {
                        var xhtml = `
                            <table id="reviewProductDataTable" class="table table-striped jambo_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Sku</th>
                                        <th>Giá</th>
                                        <th class="text-nowrap">Tình trạng</th>
                                        <th class="text-nowrap">Trạng thái</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>`
                        $.each(dataSelected, function() {
                            let id = this['DT_RowId'].replace(/row_/g, "");
                            xhtml += `
                                            <tr>
                                                <td>${this[1]}</td>
                                                <td>${this[2]}</td>
                                                <td>${this[3]}</td>
                                                <td>${this[4]}</td>
                                                <td>${this[5]}</td>
                                                <td>${this[6]}</td>
                                                <td><a data-id="${id}" href="javascript:;" class="condition-product text-nowrap text-danger"><i class="fa fa-trash mr-2"></i>Xóa</a></td>
                                            </tr>
                                        `;
                        });
                        xhtml += `</tbody>
                            </table>
                            `;

                    } else {
                        var xhtml = "Chưa có sản phẩm nào được chọn!";
                    }
                    $(Condition.selector.loadData.product).html(xhtml);
                    Condition.selector.dataTableReview = new DataTable('#reviewProductDataTable', {
                        columnDefs: [{
                            "orderable": false,
                            "targets": [0, 6]
                        }]
                    });
                });
                $(document).on("click", this.selector.button.addProduct, function() {
                    let ids = $(Condition.selector.inputData.product).val();
                    Condition.getProduct(ids);
                });
                $(document).on("click", "#checkAllDataTable", function() {
                    // if ($(this).is(":checked")) {
                    //     Condition.selector.dataTable.rows().select();
                    // } else {
                    //     Condition.selector.dataTable.rows().deselect();
                    // }
                    if ($(this).is(":checked")) {
                        Condition.selector.dataTable.rows({
                            search: 'applied'
                        }).every(function(rowIdx, tableLoop, rowLoop) {
                            if (Condition.selector.dataTable.rows({
                                    selected: true
                                }).count() < 20) {
                                Condition.selector.dataTable.row(this).select();
                            }
                        });
                    } else {
                        Condition.selector.dataTable.rows({
                            search: 'applied'
                        }).every(function(rowIdx, tableLoop, rowLoop) {
                            if (Condition.selector.dataTable.rows({
                                    selected: false
                                }).count() < 20) {
                                Condition.selector.dataTable.row(this).deselect();
                            }
                        });

                    }
                });

                $(document).on("click", this.selector.delete.product, function() {
                    let id = $(this).data('id');
                    let ids = $(Condition.selector.inputData.product).val();
                    ids = ids.split(",");
                    var index = ids.indexOf(id.toString());
                    if (index > -1) {
                        ids.splice(index, 1);
                        let data = ids.join(",");
                        $(Condition.selector.inputData.product).val(data);
                        //$(this).parents("tr").remove();
                        Condition.selector.dataTableReview.row($(this).parents('tr')).remove().draw();
                        if (ids.length == 0) {
                            $(Condition.selector.loadData.product).html("Chưa có sản phẩm nào được chọn!");
                        }

                    }
                });
                // On Load
                if ($("#reviewProductDataTable").length > 0) {
                    Condition.selector.dataTableReview = new DataTable('#reviewProductDataTable', {
                        columnDefs: [{
                            "orderable": false,
                            "targets": [0, 6]
                        }]
                    });
                }
            }
        }

        const CouponAction = {
            selector: {
                discount_type: "#discount_type",
                discount_amount: "#discount_amount",
                discount_amount_from: "#discount_amount_from",
                discount_amount_picker: '#discount_amount_picker',
                discount_max: "#discount_max"
            },
            init: function() {
                $(this.selector.discount_type).on("change", function() {
                    var type = parseInt($(this).val());
                    $(CouponAction.selector.discount_amount).val(0);
                    $(CouponAction.selector.discount_max).val(0);
                    if (type == 1) {
                        $(CouponAction.selector.discount_amount_picker).html(
                            "<i class='fa fa-percent'></i>");
                    } else {
                        $(CouponAction.selector.discount_amount_picker).html("VND");
                    }
                });
                $(this.selector.discount_amount).on("change keyup paste", function() {
                    var type = parseInt($(CouponAction.selector.discount_type).val());
                    var val = GetNumber($(this).val());
                    val = val.replace(/^0+/, '');
                    if (type == 0) {
                        $(this).val(FormatNumber(val));
                        $(CouponAction.selector.discount_max).val(FormatNumber(val));
                    }
                    if (type == 1) {
                        $(this).val(FormatNumber(val));
                        if (val > 100) {
                            $(this).val(100);
                        }
                    }
                });
                $(this.selector.discount_amount_from).on("change keyup paste", function() {
                    var val = GetNumber($(this).val());
                    val = val.replace(/^0+/, '');
                    $(this).val(FormatNumber(val));
                });
                $(this.selector.discount_max).on("change keyup paste", function() {
                    var val = GetNumber($(this).val());
                    val = val.replace(/^0+/, '');
                    $(this).val(FormatNumber(val));
                });
            }
        };
        $(document).ready(function() {
            CouponAction.init();
            Condition.init();
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
    <div class="modal" id="modalCondition" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadData">

                </div>
                <div class="modal-footer">
                    <button type="button" id="modalSaveCategory" class="btn btn-primary">Hoàn tất</button>
                    <button type="button" id="modalSaveProduct" class="btn btn-primary">Hoàn tất</button>
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
