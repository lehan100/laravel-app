var MenuMobile = {
    root: "#menuMobile",
    selector: {
        menu: " .main-menu>li",
        menuEvent: " .dropdown a",
        close: " .icon-close",
        show: "#doMenu"
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
$(document).ready(function () {
    MenuMobile.init();
});

