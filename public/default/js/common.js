var MenuMobile = {
    root: "#menuMobile",
    selector: {
        menu: " .main-menu>li",
        menuEvent: " .dropdown a",
        close: " .icon-close",
        show: ".navicon-menu"
    },
    initSelector: function () {
        this.selector.menu = this.root + this.selector.menu;
        this.selector.menuEvent = this.root + this.selector.menuEvent;
        this.selector.close = this.root + this.selector.close;
    },
    reset: function () {
        $(this.selector.menu).removeClass("active");
        $(this.selector.menu + ":first-child").addClass("active");
    },
    onShow: function () {
        $(this.root).addClass("active");
        $("body").addClass('overflow-hidden');
        this.reset();
    },
    onClose: function () {
        $(MenuMobile.root).removeClass("active");
        $("body").removeClass('overflow-hidden');
    },
    handleActive: function (sec) {
        $(this.selector.menu).removeClass("active");
        $(sec).parents("li").addClass("active");
    },
    initAction: function () {
        $(this.selector.show).click(function () {
            MenuMobile.onShow();
        });
        $(this.selector.close).click(function () {
            MenuMobile.onClose();
        });
        $(document).on("click", this.selector.menuEvent, function () {
            MenuMobile.handleActive(this);
        });
    },
    init: function () {
        this.initSelector();
        this.initAction();
        this.onClose();
    }
}



const Common = {
    navbar: function () {
        var selector = "#menu-dept";
        var selectorMenu = "#list-menu";
        $(selector).hover(function () {
            SearchResult.hide();
            $(selectorMenu).stop().slideDown('fast');
            Loading.showBlocker();
        }, function () {
            $(selectorMenu).stop().slideUp('fast');
            Loading.hideBlocker();
        });
        
    },
    mmImage: function () {
        $(".mm-img").each(function () {
            $(this).addClass("show");
            var $objImg = $(this).find("img");
            var $img = $objImg.data("img");
            $objImg.removeAttr("data-img").attr("src", $img).css("opacity", 1);
        });
    },
    viewportChecker: function () {
        jQuery('.mm-ani').viewportChecker({});
    },
    formatNumber: function (nStr, decSeperate, groupSeperate) {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    },
    msgpopup: function (mess, type = 'success') {
        $().msgpopup({
            text: mess,
            type: type,
            time: 6000,
            x: true
        });
    },
    selectorFixed: '#navigation,.navbar-mobi',
    selectorGoTop: '#goTop',
    selectorAddThis: '.addThis_listSharing',
    scroll: function () {
        var num = $(".header").height();
        var classFixed = 'fixed-top';
        $(window).bind('scroll', function () {
            if ($(window).scrollTop() > num) {
                $(Common.selectorFixed).addClass(classFixed);
            } else {
                $(Common.selectorFixed).removeClass(classFixed);
            }

            if ($(this).scrollTop() > 100) {
                $(Common.selectorGoTop).fadeIn();
            } else {
                $(Common.selectorGoTo).fadeOut();
            }

            if (jQuery(window).scrollTop() > 100) {
                jQuery(Common.selectorAddThis).addClass('is-show');
            } else {
                jQuery(Common.selectorAddThis).removeClass('is-show');
            }
        });
    },
    init: function () {
        this.navbar();
        this.mmImage();
        this.scroll();
        
        $(Common.selectorGoTop).click(function () {
            $('body,html').scrollTop(0);
        });
//        $('.user-support').click(function (event) {
//            $('.social-button-content').slideToggle();
//        });
//        $("#box-contact").click(function () {
//            var element = $(this).find(".box-contact-container");
//            if (element.hasClass("isButtonShow")) {
//                element.removeClass("isButtonShow");
//            } else {
//                element.addClass("isButtonShow");
//            }
//        });
//        $(".video-scroll").click(function () {
//            var linkYoutube = $(this).data('href');
//            $('#video-show').attr('src', linkYoutube);
//        });

    }

};
$(document).ready(function () {
    Common.init();
    //Carousel
    Carousel.start();
    //Search
    SearchResult.start();
    //MiniCart
    MiniCart.start();
    //Blocker
    Loading.initBlocker();
    //viewportChecker
    Common.viewportChecker();
    MenuMobile.init();
    $(document).on({
        mouseenter: function () {
            var eml = $(this).find("img");
            var img = eml.data("hover");
            if (img != "") {
                eml.attr("src", img);
            }
        },
        mouseleave: function () {
            var eml = $(this).find("img");
            var img = eml.data("src");
            eml.attr("src", img);
        }
    }, '.img-hover');
    //search

});
$(window).on("load", function () {
    $(".hangle-button").removeAttr("disabled");
    $('.toogle-tooltip').tooltip();
});