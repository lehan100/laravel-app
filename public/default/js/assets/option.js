const Option = {
    selectorOption: '.option-group .nav-item.active',
    priceNew: "#box-price .price-new",
    priceOld: "#box-price .price-old",
    salePecent: "#box-price .sale-pecent",
    seleted: function (selector) {
        $(selector).toggleClass("active");
        $(selector).prevAll("li").removeClass("active");
        $(selector).nextAll("li").removeClass("active");
        $(selector).parents(".option-item").find(".error").remove();
        var price = dataPrice.price;
        var special_price = dataPrice.special_price;
        //        if (typeof special_price != 'undefined') {
        //            special_price = 0;
        //        }
        $(this.selectorOption).each(function () {
            var optionID = $(this).data("option-id");
            var option = dataOption.find(function (e) {
                return e.id == optionID
            });

            var temp_price = option.price;
            var temp_special_price = option.special_price;
            //            if (typeof temp_price != 'undefined') {
            //                price = temp_price;
            //            }
            //            if (typeof temp_special_price != 'undefined') {
            //                special_price = temp_special_price;
            //            }
            if (temp_price > 0) {
                price = temp_price;
            }

            if (temp_special_price > 0) {
                //                var start = new Date(option.special_price_from).setUTCHours(23, 59, 59, 999);
                //                var end = new Date(option.special_price_to).setUTCHours(23, 59, 59, 999);
                //                var current = new Date().setUTCHours(23, 59, 59, 999);
                //                if (current >= start && current <= end) {
                //                    special_price = temp_special_price;
                //                }
                special_price = temp_special_price;
            } else if (temp_price > 0) {
                special_price = 0;
            }
            if (typeof temp_special_price == "undefined" && temp_special_price <= 0) {
                special_price = 0;
            }

        });
        //PriceFix
        var priceFixed = OptionEntries.getPriceFixed();
        if (priceFixed > 0) {
            price += priceFixed;
            special_price += priceFixed;
        }
        this.priceBox({ price: price, special_price: special_price });
    }
    ,
    getPrice: function () {
        var price = dataPrice.price;
        var special_price = dataPrice.special_price;
        $(this.selectorOption).each(function () {
            var optionID = $(this).data("option-id");
            var option = dataOption.find(function (e) {
                return e.id == optionID
            });

            var temp_price = option.price;
            var temp_special_price = option.special_price;

            if (temp_price > 0) {
                price = temp_price;
            }

            if (temp_special_price > 0) {
                special_price = temp_special_price;
            } else if (temp_price > 0) {
                special_price = 0;
            }
            if (typeof temp_special_price == "undefined" && temp_special_price <= 0) {
                special_price = 0;
            }
        });
        return { price: price, special_price: special_price };
    },
    priceBox: function (param) {
        var price = param.price;
        var special_price = param.special_price;
        //console.log(special_price);
        if (price > 0) {
            if (special_price > 0) {
                $(this.priceNew).html(Price.formatNumber(special_price));
                $(this.priceOld).html(Price.formatNumber(price)).show();
                $(this.salePecent).html(Price.getPercent({ price: price, special_price: special_price })).show();
            } else {
                $(this.priceNew).html(Price.formatNumber(price));
                $(this.priceOld).hide();
                $(this.salePecent).hide();
            }
        }
    }
}


