const Loading = {
    selector: "#loading",
    selectorBlocker: "#mm-blocker",
    show: function () {
        $(this.selector).show();
    },
    hide: function () {
        $(this.selector).hide();
    },
    showBlocker: function () {
        $(this.selectorBlocker).show();
    },
    hideBlocker: function () {
        $(this.selectorBlocker).hide();
    },
    initBlocker: function () {
        $(this.selectorBlocker).click(function () {
            SearchResult.hide();
            Loading.hideBlocker();
        });
    }
};