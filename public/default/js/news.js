const News = {
	paginator: {
		page: '.post-pagination .page-link:not(".active")'
	},
	load: function (link = "") {
		if (link != "") {
			$.ajax({
				url: link,
				type: "GET",
				dataType: "json",
				data: { lazyload: true },
				beforeSend: function () {
					Loading.show();
				},
				success: function (res) {
					Loading.hide();
					if (res.status == true) {
						$("#loadData").html(res.lists);
						$("#loadDataNewsPagination").html(res.pagination);
					}
				}
			});
		}
	},
	init: function () {
		$(document).on("click", this.paginator.page, function () {
			let page = $(this).data("page");
			Filter.addQSParm("page", page, false);
			News.load(myUrl);
		});
	}
};
const Counter = {
    viewer: function () {
        if (typeof dataCounter != "undefined") {
            var url = dataCounter.url;
            var id = dataCounter.id;
            $.ajax({
                url: url,
                type: "GET",
                data: { id: id },
                dataType: "json",
                success: function (res) {
                    // Done
                }
            });
        }
    }
}
$(document).ready(function () {
	News.init();
	 // Counter
	 Counter.viewer();
});