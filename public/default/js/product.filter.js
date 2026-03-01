$(document).ready(function(){
    $("#toolbar .dropdown-item").click(function(){
        var text = $(this).data("text");
        var elm = $(this).parents(".dropdown-menu");
        var idButtom = elm.data("id");
        elm.find("a").removeClass("active");
        $(this).addClass("active");
        $("#"+idButtom).html(text);
    }); 
});
function getUrlVars()
{
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}
var myUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;

var data = getUrlVars();
Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key))
            size++;
    }
    return size;
};
var size = Object.size(data);
if (size > 0) {
    for (var $k in data) {
        if (typeof data[$k] !== 'function') {
//         console.log("Key is " + k + ", value is " + data[k]);
            addQSParm($k, data[$k]);
        }
    }
}
//console.log(myUrl);
//data.forEach(function($key,$element){
//    console.log($key);
//    console.log($element);
//});

function addQSParm(name, value) {
    var re = new RegExp("([?&]" + name + "=)[^&]+", "");
    var page = new RegExp("([?&]page=)[^&]+", "");   
    /*
    function add(sep) {
        myUrl += sep + name + "=" + encodeURIComponent(value);
    }

    function change() {
        myUrl = myUrl.replace(re, "$1" + encodeURIComponent(value));
    }
	*/
	 function add(sep) {
        myUrl += sep + name + "=" + value;
    }

    function change() {
        myUrl = myUrl.replace(re, "$1" + value);
    }
    function df() {
        myUrl = myUrl.replace(page, "$1" + encodeURIComponent(1));
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
}
/*
 function updateURL() {
 if (history.pushState) {
 var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?para=hello';
 window.history.pushState({path: newurl}, '', newurl);
 }
 }*/
function addFilter(n, v) {
    addQSParm(n, v);
    loadAjax(myUrl);
    //document.location.href = myUrl;
}
function removeFilter(key) {
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
    loadAjax(rtn);
    //document.location.href = rtn;
}
function loadAjax(url) {
    window.history.pushState({path: url}, '', url);
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        data: {load: 1},
        beforeSend: function () {
            $("#loading").show();
        },
        success: function (f) {
           $("#loading").hide();
		   $('html, body').animate({scrollTop: $('.mainContent').position().top-50}, 'slow');
           $("#loadProduct").html(f.main);
        }
    });
}