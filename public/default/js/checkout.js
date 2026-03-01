const Checkout = {
    selector: {
        delete: ".btn-delete-cart",
        btn_submit: ".btn-checkout",
        caculator: {
            subtotal: '#subtotal',
            shipping: '#shipping',
            total: '#total'
        }
    },
    delete: function (id, selector) {
        if (id != "") {
            var token = $("#tokenCheckout").val();
            var action = "/cart/delete";
            jQuery.ajax({
                url: action,
                type: 'POST',
                dataType: "json",
                data: { id: id, _token: token },
                beforeSend: function () {
                    Loading.show();
                },
                success: function (res) {

                    if (res.status == true) {
                        if ($(Coupon.selector.button).length > 0) {
                            $(Coupon.selector.button).trigger("click");
                        }
                        Loading.hide();
                        MiniCart.change(res);
                        Checkout.calulator(res);
                        var mess = `<div class="message-popup row align-items-center"><div class="col-auto"><div class="success-checkmark">
                                 <div class="check-icon mr-2">
                                   <span class="icon-line line-tip"></span>
                                   <span class="icon-line line-long"></span>
                                   <div class="icon-circle"></div>
                                   <div class="icon-fix"></div>
                                 </div></div>
                               </div><div class="col ps-1">Đã xóa sản phẩm thành công</div></div>`;
                        Common.msgpopup(mess);
                        if (res.redirect != null) {
                            document.location.href = res.redirect;
                        }
                        selector.parents(".item-cart").remove();
                    } else {
                        Loading.hide();
                        var mess = `<div class="message-popup row align-items-center">
                                <div class="col-auto"><i class="bi bi-x-circle be-2"></i></div><div class="col ps-1">Xóa sản phẩm thất bại</div></div>`;
                        Common.msgpopup(mess, 'error');
                    }
                }
            });
        }
    },
    calulator: function (res) {
        var selector = this.selector.caculator;
        var subTotal = parseInt(res.subTotal);
        var shipping = res.shipping;
        //var discount = res.discountCode || 0;
        var discount = (res.coupon && typeof res.coupon.discount != "undefined") ? res.coupon.discount : 0;
        // discount = (typeof discount == undefined) ? discount : 0;
        let total_purchase = subTotal + shipping - discount;
        console.log(total_purchase);
        //subTotal = subTotal - discount;
        //var total = (shipping != null) ? (subTotal + shipping) : subTotal;
        $(selector.subtotal).html(Price.formatNumber(subTotal));
        $(selector.total).html(Price.formatNumber(total_purchase));
        if (shipping != null) {
            $(selector.shipping).html(Price.formatNumber(parseInt(shipping)));
        } else {
            $(selector.shipping).html('Có thể phát sinh');
        }
    },
    start: function () {
        $(document).on("click", this.selector.delete, function () {
            var id = $(this).data("id");
            Checkout.delete(id, $(this));
        });
    }
};
const Address = {
    selector: {
        city: "#city",
        district: '#district',
        ward: "#ward",
        address: '#address'
    },
    title: {
        city: "Tỉnh/Thành phố",
        district: 'Quận/Huyện',
        ward: "Phường/Xã"
    },
    link: {
        city: "/province/get-city",
        district: '/province/get-district',
        ward: '/province/get-ward',
        shipping: '/checkout/get-shipping'
    },
    selectOptions: { width: '100%' },
    loadCity: function () {
        var link = this.link.city;
        $.ajax({
            url: link,
            type: "GET",
            dataType: "json",
            success: function (res) {
                let Class = Address;
                let value = (typeof dataProvince !== "undefined") ? dataProvince.city_id : '';
                let options = Class.domOptions(res.data, Class.title.city, value, false);
                $(Class.selector.city).html(options).select2(Class.selectOptions);
                if (typeof dataProvince !== "undefined" && dataProvince.city_id !== "undefined") {
                    Address.loadDistrict(dataProvince.city_id);
                }
                if (typeof dataProvince !== "undefined" && dataProvince.district_id !== "undefined") {
                    Address.loadWard(dataProvince.district_id);
                }
            }
        });
    },
    loadDistrict: function (id = 0) {
        if (id > 0) {
            let link = this.link.district;
            $.ajax({
                url: link,
                type: "GET",
                data: { id: id },
                dataType: "json",
                success: function (res) {
                    let Class = Address;
                    let value = (typeof dataProvince !== "undefined" && dataProvince.district_id) ? dataProvince.district_id : '';
                    let options = Class.domOptions(res.data, Class.title.district, value);
                    $(Class.selector.district).html(options).removeAttr("disabled").select2(Class.selectOptions);
                }
            });
        }
    },
    loadWard: function (id = 0) {
        if (id > 0) {
            let link = this.link.ward;
            $.ajax({
                url: link,
                type: "GET",
                data: { id: id },
                dataType: "json",
                success: function (res) {
                    let Class = Address;
                    let value = (typeof dataProvince !== "undefined" && dataProvince.ward_id) ? dataProvince.ward_id : '';
                    let options = Class.domOptions(res.data, Class.title.ward, value);
                    $(Class.selector.ward).html(options).removeAttr("disabled").select2(Class.selectOptions);
                }
            });
        }
    },
    getShipping: function (city_id = "", district_id = "", ward_id = "", address = "") {
        if (city_id != "" && district_id != "" && ward_id != "") {
            let link = this.link.shipping;
            $.ajax({
                url: link,
                type: "GET",
                data: { city_id: city_id, district_id: district_id, ward_id: ward_id, address: address },
                dataType: "json",
                beforeSend: function () {
                    Loading.show();
                },
                success: function (res) {
                    Loading.hide();
                    Checkout.calulator(res.data);
                }
            });
        }
    },
    domOptions: function (data, title = "", value = '', type = true) {
        let xhtml = `<option value="">${title}</option>`;
        //let group = "";
        if (data.length > 0) {
            data.forEach(function (item) {
                // let key = item.name.slice(0, 1);
                // if (group != "" && key != group) {
                //     xhtml += `</optgroup>`;
                // }
                // if (key != group) {
                //     xhtml += `<optgroup label="${key}">`;
                //     group = key;
                // }
                var checked = (value != "" && item.id == value) ? 'selected' : '';
                // if (type == true) {
                //     xhtml += `<option ${checked} value="${item.id}">${item.type} ${item.name}</option>`;
                // } else {
                xhtml += `<option ${checked} value="${item.id}">${item.name}</option>`;
                //}
            });
        }
        return xhtml;
    },
    reset: function (selector, title) {
        let xhtml = `<option value="">${title}</option>`;
        $(selector).html(xhtml).attr('disabled', 'true').select2(this.selectOptions);
    },
    init: function () {
        if (typeof dataProvince != "undefined") {
            // $(this.selector.city).select2(this.selectOptions);
            if ($(this.selector.district).length) { $(this.selector.district).select2(this.selectOptions); }
            if ($(this.selector.ward).length) { $(this.selector.ward).select2(this.selectOptions); }
        }

    },
    start: function () {
        let Class = Address;
        // $(window).on("load", function () {
        //     Class.loadCity();
        // });
        $(document).ready(function () {
            Class.loadCity();
            Class.init();
        });
        $(document).on("change", this.selector.city, function () {
            let id = $(this).val();
            Class.reset(Class.selector.district, Class.title.district);
            Class.reset(Class.selector.ward, Class.title.ward);
            Class.loadDistrict(id);
        });
        $(document).on("change", this.selector.district, function () {
            let id = $(this).val();
            Class.reset(Class.selector.ward, Class.title.ward);
            Class.loadWard(id);
        });
        $(document).on("change", this.selector.ward, function () {
            let city_id = $(Class.selector.city).val();
            let district_id = $(Class.selector.district).val();
            let ward_id = $(Class.selector.ward).val();
            let address = $(Class.selector.address).val();
            Class.getShipping(city_id, district_id, ward_id, address);
        });
    }
};
const CheckoutValidate = {
    selector: {
        row: ".form-input",
        error: 'has-error',
        message: 'message-error'
    },
    reset: function () {
        $(this.selector.row).removeClass(this.selector.error);
        $(`${this.selector.row} .${this.selector.message}`).remove();
        return this;
    },
    message(selector, message) {
        selector = $(".form-control[name='" + selector + "']").parents(this.selector.row).addClass(this.selector.error);
        selector.append(`<p class="` + this.selector.message + `">${message}</p>`);
    },
};
const Coupon = {
    selector: {
        input: "#coupon_code",
        button: "#btn-coupon",
        total: "#total"
    },
    link: "/coupon/verify",
    is_submit: false,
    addCouponCode: function (coupon_code = "", redirect = "") {
        if (coupon_code != "") {
            let link = this.link;
            let token = $('input[name="_token"]').val();
            var form = new FormData();
            form.append('_token', token);
            form.append('coupon_code', coupon_code);
            $.ajax({
                url: link,
                type: "POST",
                data: form,
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
                beforeSend: function () {
                    Loading.show();
                },
                success: function (res) {
                    Loading.hide();
                    if (res.status == true) {
                        let subtotal = res.subtotal;
                        let shipping = res.shipping;
                        let discount = (res.coupon && typeof res.coupon.discount != "undefined") ? res.coupon.discount : 0;
                        //console.log(discount);
                        // discount = (typeof discount == undefined) ? discount : 0;
                        let total_purchase = subtotal + shipping - discount;
                        $(Coupon.selector.total).html(Price.formatNumber(total_purchase));
                        $("#discount_price").remove();
                        if (discount > 0) {
                            let xhtmlCoupon = `
                            <tr id="discount_price">
                                <td>Giảm giá (${res.coupon.coupon_info.coupon_code})</td>
                                <td class="text-end text-danger"><strong>- ${Price.formatNumber(discount)}</strong></td>
                             </tr>
                            `;
                            $(Coupon.selector.total).parents("tr").before(xhtmlCoupon);
                        }
                        if (res.verify.status == true && redirect != "" && Coupon.is_submit == false) {
                            document.location.href = redirect;
                        }

                        if (res.verify.status == true) {
                            var mess = `<div class="message-popup row align-items-center"><div class="col-auto"><div class="success-checkmark">
                                 <div class="check-icon mr-2">
                                   <span class="icon-line line-tip"></span>
                                   <span class="icon-line line-long"></span>
                                   <div class="icon-circle"></div>
                                   <div class="icon-fix"></div>
                                 </div></div>
                               </div><div class="col ps-1">${res.verify.message}</div></div>`;
                            //Common.msgpopup(mess);
                        } else {
                            var mess = `<div class="message-popup row align-items-center">
                                <div class="col-auto"><i class="bi bi-x-circle be-2"></i></div><div class="col ps-1">${res.verify.message}</div></div>`;
                            Common.msgpopup(mess, 'error');
                            $(Coupon.selector.input).val("");
                        }
                    }
                }
            });
        }
    },
    start: function () {
        $(document).on("click", this.selector.button, function () {
            Coupon.is_submit = true;
            let coupon = $(Coupon.selector.input).val();
            let pattern = /^[a-z0-9]+$/i;
            CheckoutValidate.reset();
            if (!pattern.test(coupon)) {
                CheckoutValidate.message("coupon_code", 'Mã giảm giá không hợp lệ');
                return;
            }
            $("#formCart").trigger("submit");
            //Coupon.addCouponCode(coupon);
        });
        $(document).stop().on("change keyup paste", this.selector.input, function (e) {
            let coupon = $(this).val();
            if (coupon != "") {
                $(Coupon.selector.button).removeAttr("disabled");
            } else {
                $(Coupon.selector.button).attr("disabled", true);
            }
        });
    }
};
$(document).ready(function () {
    CheckoutQuantity.start();
    Checkout.start();
    Address.start();
    Coupon.start();
    $(Checkout.selector.btn_submit).click(function () {
        Coupon.is_submit = false;
        $("#formCart").trigger("submit");
    });
    $("#formCart").on('submit', (function (e) {
        e.preventDefault();
        var link = $(this).attr("action");
        $.ajax({
            url: link,
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            beforeSend: function () {
                Loading.show();
            },
            success: function (res) {
                Loading.hide();
                CheckoutValidate.reset();
                if (res.errors) {
                    let errors = res.errors;
                    if (errors.name) {
                        CheckoutValidate.message('name', errors.name);
                    }
                    if (errors.phone) {
                        CheckoutValidate.message('phone', errors.phone);
                    }
                    if (errors.email) {
                        CheckoutValidate.message('email', errors.email);
                    }
                    if (errors.city_id) {
                        CheckoutValidate.message('city_id', errors.city_id);
                    }
                    if (errors.district_id) {
                        CheckoutValidate.message('district_id', errors.district_id);
                    }
                    if (errors.ward_id) {
                        CheckoutValidate.message('ward_id', errors.ward_id);
                    }
                    if (errors.address) {
                        CheckoutValidate.message('address', errors.address);
                    }
                }
                if (res.status == true && res.redirect) {
                    let coupon = $(Coupon.selector.input).val();
                    if (coupon != "") {
                        Coupon.addCouponCode(coupon, res.redirect);
                    } else {
                        document.location.href = res.redirect;
                    }
                }
            }
        });
    }));
    $("#formOrder").on('submit', (function (e) {
        e.preventDefault();
        var link = $(this).attr("action");
        $.ajax({
            url: link,
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            beforeSend: function () {
                Loading.show();
            },
            success: function (res) {
                Loading.hide();
                if (res.redirect) {
                    document.location.href = res.redirect;
                }
                if (res.momo != "undefined" && res.momo.errorCode == 0) {
                    let payUrl = res.momo.payUrl;
                    document.location.href = payUrl;
                }

            }
        });
    }));
});
