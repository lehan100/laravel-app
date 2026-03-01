$.fn.viewportChecker = function (useroptions) {
    var options = {
        classToAdd: 'visible',
        offset: 0,
        callbackFunction: function (elem) {
        }
    };
    $.extend(options, useroptions);

    var $elem = this, windowHeight = $(window).height();

    this.checkElements = function () {
        var viewportTop = Math.max(
                $('html').scrollTop(),
                $('body').scrollTop(),
                $(window).scrollTop()
                );

        var viewportBottom = (viewportTop + windowHeight);
        $elem.each(function () {
            var ob = $(this);
            var elemTop = Math.round(ob.offset().top) + options.offset,
                    elemBottom = elemTop + (ob.height());
            if ((elemTop < viewportBottom) && (elemBottom >= viewportTop)) {
                var obImg = ob.find("img");
                if (!obImg.hasClass("img-lazy")) {
                    obImg.css("opacity", 0);
                    var img = obImg.data("img");
                    obImg.removeAttr("data-img").attr("src", img);
                    $(obImg).on("load", function () {
                        obImg.addClass("img-lazy").css("opacity", 1);
                        ob.find(".warning").remove();
                    });
                }
                options.callbackFunction(ob);
            }
        });
    };
    $(window).scroll(this.checkElements);
    this.checkElements();
    $(window).resize(function (e) {
        windowHeight = e.currentTarget.innerHeight;
    });
};