const OptionEntries = {
    selectorOption: '.option-group-entries .nav-item.active',
    priceNew: "#box-price .price-new",
    priceOld: "#box-price .price-old",
    salePecent: "#box-price .sale-pecent",
    seleted: function (selector) {
        $(selector).toggleClass("active");
        $(selector).prevAll("li").removeClass("active");
        $(selector).nextAll("li").removeClass("active");
        $(selector).parents(".option-item").find(".error").remove();
        var price = parseInt(dataPrice.price);
        var special_price = parseInt(dataPrice.special_price);
        var optionPrice = Option.getPrice();
        if (optionPrice.price > 0) {
            price = optionPrice.price;
            special_price = optionPrice.special_price;
        }
        $(this.selectorOption).each(function () {
            var optionID = $(this).data("option-id");
            var option = dataOptionEntries.find(function (e) {
                return e.id == optionID
            });
            var temp_price = parseInt(option.price);
            price += temp_price;
            special_price += temp_price;
        });

        Option.priceBox({ price: price, special_price: special_price });
    },
    getPriceFixed: function () {
        var priceFixed = 0;
        $(this.selectorOption).each(function () {
            var optionID = $(this).data("option-id");
            var option = dataOptionEntries.find(function (e) {
                return e.id == optionID
            });
            var temp_price = parseInt(option.price);
            if (temp_price > 0) {
                priceFixed += temp_price;
            }
        });
        return priceFixed;
    }
    
}


