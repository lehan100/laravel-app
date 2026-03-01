const ShoppingCart = {
    selector: {
        button: '#btn-add-cart',
        buttonBuyNow: '#btn-order-checkout',
        quantity: '#qty',
        sale_id: '#sale_id',
        attributes: '.option-group .attributes',
        attributeEntries: '.option-group-entries .attributes',
        hasError: ".has-error",
        form: '#product_addtocart_form',
        token: "#tokenAddToCart"
    },
    message: {
        notSelectOption: '<p class="error text-danger"><i>Đây là trường bắt buộc.</i></p>'
    },
    getQuantity: function () {
        var qty = $(this.selector.quantity).val();
        qty = (qty > 0) ? qty : 1;
        return parseInt(qty);

    },
    getOptions: function () {
        if (this.hasOptions()) {
            var options = new Array();
            $(this.selector.attributes).each(function () {
                var id = $(this).find(".nav-item.active").data("option-id");
                options.push(id);
            });
            return options;
        }
    },
    getOptionEntries: function () {
        if (this.hasOptionsEntries()) {
            var optionEntries = new Array();
            $(this.selector.attributeEntries).each(function () {
                var id = $(this).find(".nav-item.active").data("option-id");
                optionEntries.push(id);
            });
            return optionEntries;
        }
    },
    hasOptions: function () {
        if ($(this.selector.attributes).length) {
            return true;
        }
        return false;
    },
    hasOptionsEntries: function () {
        if ($(this.selector.attributeEntries).length) {
            return true;
        }
        return false;
    },
    removeError: function () {
        $(".error").remove();
    },
    validateAttributes: function () {
        this.removeError();
        var status = true;
        if (this.hasOptions()) {
            $(this.selector.attributes).each(function () {
                if ($(this).find(".nav-item.active").length <= 0) {
                    $(this).addClass(ShoppingCart.selector.hasError).after(ShoppingCart.message.notSelectOption);
                    status = false;
                } else {
                    $(this).removeClass(ShoppingCart.selector.hasError);
                }
            });
        }
        if (this.hasOptionsEntries()) {
            $(this.selector.attributeEntries).each(function () {
                if ($(this).find(".nav-item.active").length <= 0) {
                    $(this).addClass(ShoppingCart.selector.hasError).after(ShoppingCart.message.notSelectOption);
                    status = false;
                } else {
                    $(this).removeClass(ShoppingCart.selector.hasError);
                }
            });
        }
        return status;
    },
    addCart: function (id, qty, options, optionEntries) {
        var link = $(this.selector.form).attr("action");
        var token = $(this.selector.token).val();
        var sale_id = ($(this.selector.sale_id).length > 0) ? $(this.selector.sale_id).val() : null;
        
        $.ajax({
            url: link,
            type: "POST",
            dataType: "json",
            data: { id: id, qty: qty, options: options, option_entries: optionEntries, sale_id: sale_id, _token: token },
            beforeSend: function () {
                Loading.show();
            },
            success: function (res) {
                Loading.hide();
                if (res.status == true) {
                    MiniCart.change(res);
                    ShoppingCart.msgModal(res.item, 'success');
                } else {
                    ShoppingCart.msgModal(null, 'error');
                }

            }
        });
    },
    buyNow: function (id, qty, options, optionEntries) {
        var link = $(this.selector.form).attr("action");
        var token = $(this.selector.token).val();
        var sale_id = ($(this.selector.sale_id).length > 0) ? $(this.selector.sale_id).val() : null;
        $.ajax({
            url: link,
            type: "POST",
            dataType: "json",
            data: { id: id, qty: qty, options: options, option_entries: optionEntries, sale_id: sale_id, _token: token },
            beforeSend: function () {
                Loading.show();
            },
            success: function (res) {
                Loading.hide();
                document.location.href = "/checkout/cart";
            }
        });
    },
    msgModal: function (item, type) {
        if (type == 'success') {
            var msgContent = `
            <div class="modal-body">
            <svg class="checkmark success" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark_circle_success" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark_check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" stroke-linecap="round"></path></svg>
                <h4 class="title my-4 text-center text-success">
                    ĐẶT HÀNG THÀNH CÔNG
                </h4>
                <div class="row justify-content-center mt-4">
                    <div class="col-8">
                        <div class="alert alert-success p-1">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-3 pe-0">
                                    <img src="${item.picture}"
                                        alt="${item.name}">
                                </div>
                                <div class="col-9">
                                    <p class="m-0"><strong>${item.name}</strong></p>
                                </div>
                            </div>
                            <div class="qty">${item.qty}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="/checkout/cart" class="btn btn-custom px-3 py-2">TIẾN HÀNH THANH TOÁN</a>
            </div>
            `;
        }
        if (type == 'error') {
            var msgContent = `
                <div class="modal-body">
                    <svg class="checkmark error" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark_circle_error" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark_check" stroke-linecap="round" fill="none" d="M16 16 36 36 M36 16 16 36
                    "></path></svg>
                    <h4 class="title my-4 text-center text-danger">
                        ĐẶT HÀNG THẤT BẠI
                    </h4>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-custom px-3 py-2" data-bs-dismiss="modal">TIẾP TỤC MUA HÀNG</button>
                </div>
            `;
        }
        var msg = `
                <div id="megAddToCart" class="modal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 p-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                         </div>
                         ${msgContent}
                    </div>
                </div>
            </div>
            `;
        $("#megAddToCart").remove();
        $("body").append(msg);
        $("#megAddToCart").modal("show");
    },
    init: function () {
        $(document).on("click", this.selector.button, function () {
            if (ShoppingCart.validateAttributes()) {
                var options = ShoppingCart.getOptions();
                var optionEntries = ShoppingCart.getOptionEntries();
                var id = $(this).data("id");
                var qty = ShoppingCart.getQuantity();
                ShoppingCart.addCart(id, qty, options, optionEntries);
            }
        });
        $(document).on("click", this.selector.buttonBuyNow, function () {
            if (ShoppingCart.validateAttributes()) {
                var options = ShoppingCart.getOptions();
                var optionEntries = ShoppingCart.getOptionEntries();
                var id = $(this).data("id");
                var qty = ShoppingCart.getQuantity();
                ShoppingCart.buyNow(id, qty, options, optionEntries);
            }
        });
    }
};