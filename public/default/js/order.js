var selectEmlement;
$(document).ready(function () {
    callbackFunction();
    $("#frmMainOrder").on('submit', (function (e) {
        e.preventDefault();
        var action = $("#frmMainOrder").attr("action");
        $.ajax({
            url: action,
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            beforeSend: function () {
                $("#loading").show();
            },
            success: function (f) {
                $("#loading").hide();
                $(".invalid-feedback").remove();
                $(".form-control").removeClass("is-invalid is-valid");
                $(".select2").removeClass("is-invalid is-valid");
                if (f.status == false) {
                    $('html, body').animate({scrollTop: $('#home-list').position().top}, 'slow');
//                    if (f.email != "") {
//                        getMessage("email", f.email);
//                    }
                    if (f.phone) {
                        getMessage("phone", f.phone);
                    }else{
						$("input[name='phone']").addClass("is-valid");
					}
                    if (f.fullname) {
                        getMessage("fullname", f.fullname);
                    }else{
						$("input[name='fullname']").addClass("is-valid");
					}
                    if (f.id_city) {
                        getMessage("id_city", f.id_city);
                    }else{
						$("select[name='id_city']").addClass("is-valid").next(".select2").addClass("is-valid");
					}
                    if (f.address) {
                        getMessage("address", f.address);
                    }else{
						$("input[name='address']").addClass("is-valid");
					}
                } else {
                    //console.log(f);
                    document.location.href = baseUrl + "/dat-hang-thanh-cong.html";
                }
            }
        });
    }));
});
function getDistrict(id) {
    var url = baseUrl + '/news/cart/ajax-district';
    jQuery.ajax({
        url: url,
        type: "POST",
        data: {
            id: id
        },
        async: false,
        dataType: "json",
        beforeSend: function () {
            $("#loading").show();
        },
        success: function (f)
        {
            $("#loading").hide();

            if (f.error == 0) {
                jQuery('.district').html(f.html);
            }
			callbackFunction();
            //var $shipping = parseInt(f.shipping);
            //var $total = parseInt($("#hidden_total").val());
            //var $result = $total + $shipping;
            //$("#hidden_shipping").val($shipping);
            //$("#shipping").empty().html(formatNumber($shipping, '.', '.') + " ₫");
            //$("#total").empty().html(formatNumber($result, '.', '.') + " ₫");
        }
    });
}
function getWard(id) {
    var url = baseUrl + '/news/cart/ajax-ward';
    jQuery.ajax({
        url: url,
        type: "POST",
        data: {
            id: id
        },
        async: false,
        dataType: "json",
        success: function (f)
        {
            if (f.error == 0) {
                jQuery('.ward').html(f.html);
            }
			callbackFunction();
            // var $total = parseInt($("#hidden_total").val());
            // var $shipping = parseInt(f.shipping);
            // var $result = $total + $shipping;
            // $("#hidden_shipping").val($shipping);
            // $("#shipping").empty().html(formatNumber($shipping, '.', '.') + " ₫");
            // $("#total").empty().html(formatNumber($result, '.', '.') + " ₫");
        }
    });
}
function upNumber($element,$idp) {
    var $elementNumber = $($element).prevAll(".number");
    var $number = parseInt($elementNumber.val()) + 1;
    var $max = parseInt($elementNumber.data('max'));
	if($number > $max) {$number = $max}
    $elementNumber.val($number);
    var $id = $elementNumber.attr("data-id");
    updateNumber($id, $idp, $number, $max);
}
function downNumber($element,$idp) {
    var $elementNumber = $($element).prevAll(".number");
    var $number = parseInt($elementNumber.val()) - 1;
	var $max = parseInt($elementNumber.data('max'));
    var $id = $elementNumber.attr("data-id");
    if ($number > 0) {
        $elementNumber.val($number);
        updateNumber($id, $idp, $number, $max);
    }
}
function formatNumber(nStr, decSeperate, groupSeperate) {
    nStr += '';
    x = nStr.split(decSeperate);
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
    }
    return x1 + x2;
}
function updateNumber($id, $idp, $number, $max) {
    jQuery.ajax({
        url: baseUrl + '/news/cart/update-cart',
        data: {id: $id, idp:$idp, qty: $number, max: $max},
        type: 'post',
        dataType: "json",
        success: function (f) {
            if (f.status == true) {
                var $shipping = 0;
                // if ($("#hidden_total").length > 0) {
                    // $shipping = parseInt($("#hidden_shipping").val());
                // }
				var maxUpdate = f.max;
				if($number > maxUpdate){
					$(".number[data-id='"+$id+"']").val(maxUpdate);
				}
                $("#total").html(formatNumber(parseInt(f.total_val) + $shipping, '.', '.') + " ₫");
                $("#hidden_total").val(f.total_val);
                $("#cart span").html(f.quantity);
                $("#c_total").html(f.total);
                $("#subtotal").html(f.subtotal);
                $("#shipping").html(f.shipping);
                $(".counter-number").html(f.quantity);
                $("#price_" + $id).html(f.price_quantity);
            }
        }
    });
}
function validateQuantity($seElement, $id, $idp) {
    var $number = parseInt($seElement.value);
    var $max = parseInt($seElement.getAttribute("data-max"));
    if ($number < 1 || isNaN($number)) {
        $("#" + $id).val(1);
        $number = 1;
    }
	if($number > $max) {$number = $max}
    updateNumber($id, $idp, $number, $max);
}
function doChangeAttribute(id, $attr, $val) {
    updateShipping();
    var url = baseUrl + '/news/cart/change-attr';
    jQuery.ajax({
        url: url,
        type: 'POST',
        data: {
            id: id, attr: $attr, val: $val
        },
        dataType: "json",
        beforeSend: function () {
            $("#loading").show();
        },
        success: function (f) {
            $("#loading").hide();
            if (f.empty == true) {
                $("#btn_save_order").attr("disabled", "true");
                $(".load-delete").empty().append("<p class='alert alert-danger'>Giỏ hàng rỗng!</p>");
            } else {
               $(".counter-number").html(f.quantity);
                $("#c_total").text(f.total);
                jQuery('.load-delete').html(f.html);
                var $shipping = 0;
                // if ($("#hidden_total").length > 0) {
                    // $shipping = parseInt($("#hidden_shipping").val());
                // }
                var $total = parseInt($("#hidden_total").val());
				$("#subtotal").html(f.subtotal);
                $("#total").text(formatNumber($total + $shipping, '.', '.') + " ₫");
            }
        }
    });
}
function updateShipping() {
    var $city = $(".id_city").val();
    var $district = $(".id_district").val();
    if ($district)
    {
        getWard($district);
    } else {
        getDistrict($city);
    }
}
function doDelete(id) {
    var url = baseUrl + '/news/cart/delete';
    jQuery.ajax({
        url: url,
        type: 'POST',
        data: {
            id: id
        },
        dataType: "json",
        beforeSend: function () {
            $("#loading").show();
        },
        success: function (f) {
            $("#loading").hide();
            if (f.empty == true) {
                $("#btn_save_order").attr("disabled", "true");
                $(".load-delete").empty().append("<p class='alert alert-danger'>Giỏ hàng rỗng. Hệ thống sẽ trở về trang chủ sau 5s.</p>");
                setTimeout(function(){document.location.href = baseUrl}, 5000);
            } else {
                $(".counter-number").html(f.quantity);
                // $("#c_total").text(f.total);
                jQuery('.load-delete').html(f.html);
                // var $shipping = 0;
                // if ($("#hidden_total").length > 0) {
                    // $shipping = parseInt($("#hidden_shipping").val());
                // }
                // var $total = parseInt($("#hidden_total").val());
				// $("#subtotal").html(f.subtotal);
                // $("#total").text(formatNumber($total + $shipping, '.', '.') + " ₫");
            }
        }
    });
}
function callbackFunction(){
	if ($(".select_single").length) {
        $(".select_single").select2({
            placeholder: $(this).attr('placeholder'),
            allowClear: true
        });
    }
}
function getMessage(name, value) {
    if ($("input[name='" + name + "']").length > 0) {
        selectEmlement = $("input[name='" + name + "']");
        //selectEmlement.parents(".form-group").find("input").each(function () {
//            if ($(this).val() == "") {
//                $(this).after('<span class="form-control-feedback fa fa-exclamation-triangle" aria-hidden="true"></span>');
//            }
       // });
    } else {
        selectEmlement = $("select[name='" + name + "']");
    }
    selectEmlement.next(".select2").addClass("is-invalid");
    selectEmlement.addClass("is-invalid").parents(".col-input").append("<div class='invalid-feedback'>" + value + "</div>");//.parents(".form-group").addClass("has-error");
}
