const Sort = {
    selector: ".sort-item",
    activeClass: 'btn-info active text-white',
    inactiveClass: 'btn-outline-info',
    add: function (value) {
        Filter.addQSParm("mode", value, true);
    },
    remove: function () {
        $(this.selector).removeClass(this.activeClass).addClass(this.inactiveClass);
        Filter.removeFilter("mode", false);
    },
    init: function () {
        $(this.selector).click(function () {
            var sort = $(this).data("sort");
            Sort.remove();
            if (sort != "position") {
                Sort.add(sort);
            }
            if (sort == "position") {
                Filter.lazyLoad();
            }
            $(this).addClass(Sort.activeClass);
        });
    }
};

