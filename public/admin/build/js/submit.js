String.prototype.format = function () {
    return [...arguments].reduce((p, c) => p.replace(/%s/, c), this);
};

// JavaScript Document
function onSubmitActon(formName) {
    var theForm = document.getElementById(formName);
    var check = checkValidate();
    if (check == true) {
        theForm.submit();
    }
}

function onSubmitActonRollback(formName) {
    var theForm = document.getElementById(formName);
   
    
    document.getElementById('rollback').value = 1;
    var check = checkValidate();
    if (check == true) {
        theForm.submit();
    }
}

function checkValidate() {
    var status = true;
    if ($(".option-title").length > 0) {
        $(".option-title").each(function () {
            var val = $(this).val();
            $(this).next(".invalid-feedback").remove();
            if (val == "") {
                status = false;
                $("#v-pills-options-tab").trigger('click');
                $(this).addClass("is-invalid").after("<p class='invalid-feedback'><i>Vui lòng không được để trống</i></p>");
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });
         console.log(1);
    }
    if ($(".option-type").length > 0) {
        
        $(".option-type").each(function () {
            var val = $(this).val();
            $(this).next(".invalid-feedback").remove();
            if (val == "") {
                status = false;
                $("#v-pills-content-tab").trigger("click");
                $("#v-pills-options-tab").trigger('click');
                $(this).addClass("is-invalid").after("<p class='invalid-feedback'><i>Vui lòng không được để trống</i></p>");
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });
    }
    if ($(".option-attr-title").length > 0) {
        $("#v-pills-content-tab").trigger("click");
        $(".option-attr-title").each(function () {
            var val = $(this).val();
            if (val == "") {
                status = false;
                $("#v-pills-content-tab").trigger("click");
                $("#v-pills-options-tab").trigger('click');
                $(this).addClass("is-invalid");
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });
    }

    return status;
}

function onSubmitForm(formName, url, dialog = false, title = "", message = "", attr_class = "alert alert-warning", attr_icon = "fa fa-exclamation-circle") {
    if (dialog) {
        if (attr_class != "") {
            message = "<p class='%s'><span class='%s mr-2'></span>%s</p>".format(attr_class, attr_icon, message);
        }
        $.confirm({
            title: title,
            text: message,
            confirm: function () {
                var theForm = document.getElementById(formName);
                theForm.action = url;
                theForm.submit();
            },
            confirmButton: "Đồng ý",
            cancelButton: "Hủy",
            confirmButtonClass: "btn-success btn-sm",
            cancelButtonClass: "btn-danger btn-sm"
        });
    } else {
        var theForm = document.getElementById(formName);
        theForm.action = url;
        theForm.submit();
    }
    return true;

}
function onActionForm(url, dialog = false, title = "", message = "", attr_class = "alert alert-warning", attr_icon = "fa fa-exclamation-circle") {
    if (dialog) {
        if (attr_class != "") {
            message = "<p class='%s'><span class='%s mr-2'></span>%s</p>".format(attr_class, attr_icon, message);
        }
        $.confirm({
            title: title,
            text: message,
            confirm: function () {
                window.location.href = url;
            },
            confirmButton: "Đồng ý",
            cancelButton: "Hủy",
            confirmButtonClass: "btn-success btn-sm",
            cancelButtonClass: "btn-danger btn-sm"
        });
    } else {
        window.location.href = url;
    }
    return true;

}
function changeStatus($element, url) {
    jQuery.ajax({
        url: url,
        type: "GET",
        async: false,
        dataType: "json",
        success: function (f) {
            if (f.status == true) {
                $($element).replaceWith(f.xhtml)
            }
        }
    });
}
function addFilter(key, $element) {
    var val = $($element).val();
    var url = $($element).data('url');
    var keyFilter = key;
    //if (val > 0) {
    jQuery.ajax({
        url: url,
        type: "GET",
        async: false,
        data: { key: keyFilter, 'val': val },
        dataType: "json",
        success: function (f) {
            window.location.reload();
        }
    });
    //}

}
$(document).ready(function () {
    $("#checkAll").click(function () {
        $('input:checkbox:not(:disabled)').not(this).prop('checked', this.checked);
    });
    $(".dblclick").on("dblclick", function () {
        var link = $(this).data("link");
        if (link != "") {
            document.location.href = link;
        }
    });
    $(".onclick").on("click", function () {
        var link = $(this).data("link");
        if (link != "") {
            document.location.href = link;
        }
    });
    setTimeout(function () {
        $(".alert-fixed").fadeOut(1000);
    }, 3000);

    $("#status").change(function () {

        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });
});