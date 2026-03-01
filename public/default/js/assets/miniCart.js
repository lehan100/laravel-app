const MiniCart = {
    selector: {
        event: '.shopping-cart',
        popup: "#mm-cart",
        miniCart: "#my_cart",
        quantity: "#icon-cart .my-quantity-cart"
    },
    show: function () {
        $(this.selector.popup).stop().slideDown('fast');
        Loading.showBlocker();
    },
    hide: function () {
        $(this.selector.popup).stop().slideUp('fast');
        Loading.hideBlocker();
    },
    change: function (res) {
        $(this.selector.miniCart).html(res.html);
        $(this.selector.quantity).html(res.quantity);
        $(this.selector.miniCart + " .img-thumbnail").each(function () {
            $(this).attr("src", $(this).data("src"));
        });
        $("#mCustomScrollbar").mCustomScrollbar("stop");
        
    },
    load: function () {
        var link = "/cart/minicart";
        $.ajax({
            url: link,
            type: "GET",
            dataType: "json",
            success: function (res) {
                MiniCart.change(res);
            }
        });
    },
    start: function () {
        $(this.selector.event).hover(function () {
            MiniCart.show();
            $(".sitebar").removeClass("d-block");
        }, function () {
            MiniCart.hide();
            $(".sitebar").removeClass("d-block");
        });
        $(window).on("load", function () {
            MiniCart.load();
        });
    }
};