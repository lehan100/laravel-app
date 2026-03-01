const SearchResult = {
    selector: ".search-result",
    show: function () {
        $(this.selector).show();
        $(".sitebar").removeClass("d-block");
        $("html").addClass("mm-opened");
    },
    hide: function () {
        $(this.selector).hide();
        $(".sitebar").removeClass("d-block");
        $("html").removeClass("mm-opened");
    },
    remove: function () {
        $(this.selector).empty();
    },
    miniSearch: function (key) {
        var token = $("#tokenSearch").val();
        var action = "/product/ajax-search";
        jQuery.ajax({
            url: action,
            type: 'POST',
            dataType: "json",
            data: {keyword: key, _token: token},
            success: function (res) {
                Loading.showBlocker();
                SearchResult.show();
                $(SearchResult.selector).html(res.html);
            }
        });
    },
    start: function () {
        var searchHandler;
        var searchDelay = 1000;
        $(document).on("keyup paste", ".keySearch", function (e) {
            var elm = $(this);
            SearchResult.remove();
            if (e.ctrlKey) {
                if (e.keyCode == 65 || e.keyCode == 97) {
                    e.stopPropagation();
                }
            } else {
                clearTimeout(searchHandler);
                searchHandler = setTimeout(function () {
                    var key = elm.val();
                    if (key !== "" && key.length > 1) {
                        SearchResult.miniSearch(key);
                    } else {
                        Loading.hideBlocker();
                        SearchResult.hide();
                    }
                }, searchDelay);
            }

        });
    }
};