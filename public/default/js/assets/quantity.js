const Quantity = {
    selectorUp: ".box-qty .qty-plus",
    selectorDown: ".box-qty .qty-minus",
    inputNumber: ".box-qty .qty",
    tierPrice: ".tier_prices",
    up: function (selector) {
        var element = selector.prev(this.inputNumber);
        var number = parseInt(element.val()) + 1;
        var max = parseInt(element.data('limit'));
        var qty = 0;
        if (!element.is(':disabled')) {
            if (number < max) {
                qty = number;
            } else {
                qty = max;
            }
            element.val(qty);
            this.changeTierPrice(qty);
        }

    },
    down: function (selector) {
        var element = selector.next(this.inputNumber);
        if (!element.is(':disabled')) {
            var min = parseInt(element.attr("min"));
            if (!min) {
                min = 1;
            }
            var number = parseInt(element.val()) - 1;
            if (number >= min) {
                element.val(number);
                this.changeTierPrice(number);
            }
        }
    },
    keyup: function (selector) {
        var number = selector.val();
        var max = parseInt(selector.data('limit'));
        var qty = 0;
        if (number < max) {
            qty = number;
        } else {
            qty = max;
        }
        if (number < 1 || isNaN(number)) {
            qty = 1;
        }
        selector.val(qty);
        this.changeTierPrice(qty);

    },
    changeTierPrice: function (qty = 0) {
        if ($(this.tierPrice).length > -1) {
            $(this.tierPrice + " li").each(function () {
                var qtyBuy = $(this).data("buy");
                if (qtyBuy <= qty) {
                    $(this).addClass("text-success");
                } else {
                    $(this).removeClass("text-success");
                }

            });
        }
    },
    start: function () {
        $(document).on("click", this.selectorUp, function () {
            Quantity.up($(this));
        });
        $(document).on("click", this.selectorDown, function () {
            Quantity.down($(this));
        });
        $(this.inputNumber).keyup(function () {
            Quantity.keyup($(this));
        });
    }
};