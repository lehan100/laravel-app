const CheckoutQuantity = {
	selector:{
		up:".box-qty .qty-plus",
		down:".box-qty .qty-minus",
		inputNumber: ".box-qty .qty"
	},
    up: function (selector) {
        var element = selector.prev(this.selector.inputNumber);
        var number = parseInt(element.val()) + 1;
        var max = parseInt(element.data('limit'));
        var qty = 1;
        var id = element.data('id');
        if (!element.is(':disabled')) {
            qty = (number < max) ? number : max;
            element.val(qty);
            this.update(id, qty);
        }
    },
    down: function (selector) {
        var element = selector.next(this.selector.inputNumber);
        var id = element.data('id');
        if (!element.is(':disabled')) {
            var min = parseInt(element.attr("min"));
            if (!min) {
                min = 1;
            }
            var qty = parseInt(element.val()) - 1;
            if (qty >= min) {
                element.val(qty);
                this.update(id, qty);
            }else{
				selector.parents(".item-cart").find(".btn-delete-cart").trigger("click");
			}
        }
        return -1;
    },
    keyup: function (selector) {
        var number = selector.val();
        var max = parseInt(selector.data('limit'));
        var qty = 1;
        var id = selector.data('id');
        if (number < max) {
            qty = number;
            selector.val(number);
        } else {
            selector.val(max);
            qty = max;
        }
        selector.val(qty);
        if (qty > 0) {
            this.update(id, qty);
        }
    },
    update: function (id, qty) {
        var token = $("#tokenCheckout").val();
        var action = "/cart/update";
        jQuery.ajax({
            url: action,
            type: 'POST',
            dataType: "json",
            data: { id: id, qty: qty, _token: token },
            success: function (res) {
                if (res.status == true) {
                    //MiniCart.change(res);
                    $("#price_" + id +" .box-price").html(res.htmlPrice);
                    $("#gift_" + id).remove();
                    if (res.html_gift != "") {
                        $("#info_" + id).append(res.html_gift);
                    } 
                    Checkout.calulator(res);
                    var mess = `<div class="message-popup row align-items-center"><div class="col-auto"><div class="success-checkmark">
                            <div class="check-icon mr-2">
                              <span class="icon-line line-tip"></span>
                              <span class="icon-line line-long"></span>
                              <div class="icon-circle"></div>
                              <div class="icon-fix"></div>
                            </div></div>
                          </div><div class="col ps-1">Đã cập nhật thành công</div></div>`;
                    Common.msgpopup(mess);
                }
            }
        });
    },

    start: function () {
        $(document).on("click", this.selector.up, function () {
            CheckoutQuantity.up($(this));
        });
        $(document).on("click", this.selector.down, function () {
            CheckoutQuantity.down($(this));
        });
        $(this.selector.inputNumber).keyup(function () {
            CheckoutQuantity.keyup($(this));
        });
    }
};