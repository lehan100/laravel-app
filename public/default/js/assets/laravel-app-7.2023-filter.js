var myUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
const Filter = {
    data: {},
    join: ",",
    getData: function () {
        var vars = {};
        window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
            vars[key] = value;
            Filter.addQSParm(key, value, false);
        });
        this.data = vars;
    },
    autoChecked: function () {
        this.getData();
        $.each(this.data, function (key, value) {
            var arrValue = value.split(Filter.join);
            $('.filter-value[data-alias="' + key + '"]').each(function () {
                var val = $(this).val();
                if (arrValue.indexOf(val) > -1) {
                    $(this).prop('checked', true);
                    $(this).next(".filter-picture").addClass("border");
                }
            });
        });
    },
    addQSParm: function (name, value, lazyload = true) {
        var re = new RegExp("([?&]" + name + "=)[^&]+", "");
        var page = new RegExp("([?&]page=)[^&]+", "");
        function add(sep) {
            myUrl += sep + name + "=" + value;
        }

        function change() {
            myUrl = myUrl.replace(re, "$1" + value);
        }
        function df() {
            myUrl = myUrl.replace(page, "$1" + 1);
        }
        if (myUrl.indexOf("?") === -1) {
            add("?");
        } else {
            if (page.test(myUrl)) {
                df();
            }
            if (re.test(myUrl)) {
                change();
            } else {
                add("&");
            }
        }
        this.pushState(lazyload);
    },
    removeFilter: function (key, lazyload = true) {
        var rtn = myUrl.split("?")[0],
                param,
                params_arr = [],
                queryString = (myUrl.indexOf("?") !== -1) ? myUrl.split("?")[1] : "";
        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }
            if (params_arr.length >= 1) {
                rtn = rtn + "?" + params_arr.join("&");
            }
        }
        myUrl = rtn;
        this.pushState(lazyload);
    },
	resetFilter:function(selector){
		if (selector.hasClass("only")) {
			$(".filter-value.only").not(selector).each(function () {
				if ($(this).is(':checked')) {
					var alias = $(this).data("alias");
					Filter.removeFilter(alias, false);
					$(this).prop('checked', false);
				}
			});
		}
		var alias = selector.data("alias");		
		var data = Filter.getDataAlias(alias);
		if (data.length > 0) {
			Filter.addQSParm(alias, data.join(Filter.join));
		} else {
			Filter.removeFilter(alias, true);
		}
	},
    pushState: function (lazyload = true) {
        window.history.pushState({path: myUrl}, '', myUrl);
        if (lazyload) {
            this.lazyLoad(myUrl);
    }
    },
    getDataAlias: function (alias) {
        var data = [];
        $('.filter-value[data-alias="' + alias + '"]').each(function () {
            if ($(this).is(':checked')) {
                data.push($(this).val());
            }
        });
        return data;
    },
    lazyLoad: function () {
        $.ajax({
            url: myUrl,
            type: "GET",
            dataType: "json",
            data: {lazyload: true},
            beforeSend: function () {
//                Loading.show();
            },
            success: function (res) {
//                Loading.hide();
                $("#lazyLoadProducts").html(res.html);
                Paginator.init();
                Common.viewportChecker();
            }
        });
    },
    lazyLoadAutoPage: function () {
        var elm = $(Paginator.seletorAutoPage);
        var curentPage = elm.data("page");
        var maxPage = elm.data("maxpage");
        if (curentPage <= maxPage) {
            $.ajax({
                url: myUrl,
                type: "GET",
                dataType: "json",
                data: {lazyload: true, autopage: true},
                success: function (res) {
                    $("#lazyLoadProducts>.list-products>.row").append(res.html);
                    elm.data("page", (curentPage + 1));
                    if (maxPage >= curentPage) {
                        elm.remove();
                    }
                    Paginator.init();
                    Common.viewportChecker();
                }
            });
        }
    },
    toggleSiteBar: function () {
        $(".sitebar").toggleClass("d-block");
        $("html").toggleClass("mm-opened");
    },
    init: function () {
        this.autoChecked();
		// Add <input class="d-none filter-value picture only" data-alias="brand" type="checkbox" value="...">
        $(document).on("change", ".filter-value:not(.picture)", function () {         
			Filter.resetFilter($(this));          
        });
        $(document).on("click", '.filter-picture', function () {
            var checkbox = $(this).prev(".filter-value");	
			if (!checkbox.is(':checked')) {
				checkbox.trigger("change").prop('checked', true);
			} else {
				checkbox.prop('checked', false);
			}	
			Filter.resetFilter(checkbox);
			
        });
        $(document).on("click", ".sort-filter", function () {
            Filter.toggleSiteBar();
            Loading.showBlocker();
        });
        $(document).on("click", ".sitebar-close", function () {
            Filter.toggleSiteBar();
            Loading.hideBlocker();
        });
    }

};