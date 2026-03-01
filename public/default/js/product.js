
const Content = {
    selector: ".product-infomation",
    detect: function () {
        $(this.selector).find("table").addClass("table table-striped");
        $(this.selector + " .sort_content table tr td:first-child").addClass("col-4");
        $(this.selector + " .sort_content table tr td").addClass("align-middle");
    }
}
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
const SearchTerms = {
    search: function () {
        if (typeof dataSearch != "undefined") {
            var query_text = dataSearch.query_text;
            var num_results = dataSearch.total_results;
            var url = dataSearch.url;
            $.ajax({
                url: url,
                type: "GET",
                data: { query_text: query_text, num_results: num_results },
                dataType: "json",
                success: function (res) {
                    // Done
                }
            });
        }
    }
}
var RatingFilter = 0;
var FancyboxDisplay;
const Rating = {
    selector: {
        modal: '#ratingModal',
        hiddenRating: '#rating_star',
        selectRating: 'ul.rating-star button',
        form: "#formRate",
        toggleform: "#toggleformRate",
        filter: ".review-toolbar .btn-filter",
        reviewPhoto: "#review-photo",
        reviewPhotoLimit: 5,
        upload: {
            toggleClick: ".send-img",
            inputFile: '#fileRatingUpload',
            valueFile: '#ratingImg',
            review: '#ratingModal .review',
            file_size: 0
        }
    },
    validate: {
        row: ".form-input",
        error: 'has-error',
        message: 'message-error'
    },
    paginator: {
        page: '#block_review .page-link:not(".active")',
        link: '/product/rating'
    },
    fancyboxRating: {
        link: '/product/rating-image',
        item: ".fancybox-zoom",
        all: ".fancybox-all",
        data: []
    },
    reset: function () {
        $(this.validate.row).removeClass(this.validate.error);
        $(`${this.validate.row} .${this.validate.message}`).remove();
    },
    message(selector, message) {
        selector = $(".form-control[name='" + selector + "']").parents(this.validate.row).addClass(this.validate.error);
        selector.append(`<p class="` + this.validate.message + `">${message}</p>`);
    },
    updateRating: function (val = 0) {
        if (val > 0) {
            $(this.selector.hiddenRating).val(val);
            $(this.selector.selectRating).each(function () {
                let curent_val = $(this).data("star");
                if (curent_val <= val) {
                    $(this).addClass("active");
                    if (curent_val == val) {
                        $(this).addClass("selected");
                    } else {
                        $(this).removeClass("selected");
                    }
                } else {
                    $(this).removeClass("active selected");
                }
            });
        }
    },
    loadRating: function (page = 0) {
        if (page > 0) {
            let myUrl = this.paginator.link;
            let product_id = $("#product_id").val();
            $.ajax({
                url: myUrl,
                type: "GET",
                dataType: "json",
                data: { page: page, product_id: product_id, filter: RatingFilter },
                // beforeSend: function () {
                //     Loading.show();
                // },
                success: function (res) {
                    Loading.hide();
                    if (res.status == true) {
                        $("#loadDataRating").html(res.html);
                        $("#loadDataRatingPagination").html(res.pagination);
                    }
                }
            });
        }
    },
    doUpload: function (files, index, length = 0) {
        if (length > 0 && index < length) {
            var file = files[index];
            var token = $(this.selector.upload.inputFile).data("token");
            var link = $(this.selector.upload.inputFile).data("link-upload");
            var form = new FormData();
            form.append('_token', token);
            form.append('picture', file);
            $.ajax({
                url: link,
                type: "POST",
                data: form,
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
                success: function (f) {
                    if (f.status == true) {
                        Rating.updateValueUpload(f.picture);
                        Rating.selector.upload.file_size += 1;
                        let reviewHTML = `
                        <div class="item col-auto mt-3">
                           <div class="position-relative">
                                <img src="${f.url}" width="60px">
                                <a href="javascript:void(0)" data-picture="${f.picture}" class="delete bi bi-trash3"></a>
                           </div>
                        </div>
                        `;
                        $(Rating.selector.upload.review).append(reviewHTML);
                        Rating.doUpload(files, ++index, length);
                    }
                }
            });
        }
    },
    updateValueUpload(fileName = "") {
        if (fileName != "") {
            let data = $(this.selector.upload.valueFile).val();
            let arrData = new Array();
            if (data != "") {
                arrData = data.split(",");
            }
            arrData.push(fileName);
            $(this.selector.upload.valueFile).val(arrData.join(","));
        }
    },
    removeImage: function (fileName = "") {
        if (fileName != "") {
            let data = $(this.selector.upload.valueFile).val();
            let arrData = new Array();
            if (data != "") {
                arrData = data.split(",");
                let index = arrData.indexOf(fileName);
                if (index > -1) {
                    arrData.splice(index, 1);
                    $(this.selector.upload.valueFile).val(arrData.join(","));
                }
            }
        }

    },
    fancybox: function () {
        $(document).on("click", this.fancyboxRating.item, function () {
            let src = $(this).data('src');
            Rating.loadDataFancybox(src, true);
        });
        $(document).on("click", this.fancyboxRating.all, function () {
            Rating.loadDataFancybox("", false, true);
        });
    },
    loadDataFancybox: function (image = "", slideTo = false, opened = true) {
        if (this.fancyboxRating.data.length > 0) {
            if (opened) {
                let index = 0;
                if (image != "") {
                    index = this.fancyboxRating.data.findIndex((element) => element.src.indexOf(image) > -1);
                }
                FancyboxDisplay = new Fancybox(this.fancyboxRating.data, {
                    on: {
                        reveal: (f, s) => {
                            if (slideTo == true) {
                                FancyboxDisplay.carousel.slideTo(index);
                                slideTo = false;
                            }

                        }
                    }
                });
            }
        } else {
            let myUrl = this.fancyboxRating.link;
            let product_id = $("#product_id").val();
            $.ajax({
                url: myUrl,
                type: "GET",
                dataType: "json",
                data: { product_id: product_id },
                beforeSend: function () {
                    Loading.show();
                },
                success: function (res) {
                    Loading.hide();
                    if (res.status == true) {
                        Rating.fancyboxRating.data = res.data;
                        Rating.ratingReviewPhoto(res.data);
                        Rating.loadDataFancybox(image, slideTo, opened);
                    }
                }
            });
        }
    },
    ratingReviewPhoto: function (data) {
        let reviewPhotoLimit = this.selector.reviewPhotoLimit;
        if (data.length) {
            let itemHtml = "";
            $.each(data, function (i, photo) {
                if (i < reviewPhotoLimit) {
                    itemHtml += `
                    <div class="col-4 col-md-auto">
                        <div class="border rounded-2 overflow-hidden photo mb-3">
                            <a class="fancybox-zoom" data-src="${photo.image}">
                                 <img src="${photo.thumb}" alt="${photo.name}"/>
                            </a>
                        </div>
                    </div>
                    `;
                } else if (i = reviewPhotoLimit) {
                    nextTotalGalaxy = data.length - reviewPhotoLimit;
                    itemHtml += `
                    <div class="col-4 col-md-auto">
                        <div class="border rounded-2 overflow-hidden photo mb-3 position-relative">
                            <a class="fancybox-all">
                                <img src="${photo.thumb}" alt="${photo.name}"/>
                            <span class="note">Xem ${nextTotalGalaxy} ảnh từ khách hàng</span>
                            </a>
                        </div>
                    </div>
                    `;
                } else {
                    return;
                }
            });
            $(this.selector.reviewPhoto).html(`<div class="row align-items-center mt-3">${itemHtml}</div>`);
        }
    },
    init: function () {
        this.loadDataFancybox("", false, false);
        this.fancybox();
        $(this.selector.selectRating).on("click", function () {
            var val = $(this).data("star");
            Rating.updateRating(val);
            $(Rating.selector.toggleform).removeClass("d-none");
        });
        $(document).on("click", ".review_now", function () {
            $('html, body').scrollTop($('#block_review').offset().top - 150);
        });
        $(document).on("click", this.paginator.page, function () {
            var page = $(this).data("page");
            Rating.loadRating(page);
        });
        $(document).on("click", this.selector.filter, function () {
            var star = $(this).data("star");
            $(Rating.selector.filter).removeClass("active");
            $(this).addClass("active");
            RatingFilter = star;
            Rating.loadRating(1);
        });
        $(document).on("click", this.selector.upload.toggleClick, function () {
            $(Rating.selector.upload.inputFile).trigger("click");
        });
        $(document).on("click", this.selector.upload.review + " .delete", function () {
            let fileName = $(this).data("picture");
            Rating.removeImage(fileName);
            $(this).parents(".item").remove();
        });
        $(document).on("change", this.selector.upload.inputFile, function () {
            let files = $(this)[0].files;
            let maxLength = $(this).data('max_length');
            if (files.length > maxLength || Rating.selector.upload.file_size >= 3) {
                alert("Chỉ cho phép tối đa 3 file. Quý khách vui lòng gửi lại.");
            } else {
                Rating.doUpload(files, 0, files.length);
            }
        });
        $(this.selector.form).on('submit', (function (e) {
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
                    if (res.errors) {
                        let errors = res.errors;
                        if (errors.name) {
                            Rating.message('name', errors.name);
                        }
                        if (errors.phone) {
                            Rating.message('phone', errors.phone);
                        }
                        if (errors.content) {
                            Rating.message('content', errors.content);
                        }
                    }
                    if (res.status == true) {
                        $(Rating.selector.form)[0].reset();
                        $(Rating.selector.modal + " .modal-content").html(`
                            <div class="modal-header border-0 p-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </div>
                            <div class="modal-body pt-4">
                                <svg class="checkmark success" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark_circle_success" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark_check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" stroke-linecap="round"></path></svg>
                                <h4 class="title my-4 text-center text-success">
                                    ĐÁNH GIÁ SẢN PHẨM THÀNH CÔNG
                                </h4>
                            </div>
                            `);
                    }
                    if (res.status == false && res.code == 400) {
                        $(Rating.selector.modal + " .modal-content").html(`
                            <div class="modal-header border-0 p-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </div>
                            <div class="modal-body pt-4">
                                <svg class="checkmark error" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark_circle_error" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark_check" stroke-linecap="round" fill="none" d="M16 16 36 36 M36 16 16 36
                                "></path></svg>
                                <h4 class="title my-4 text-center text-danger">
                                    BẠN ĐÃ ĐÁNH GIÁ SẢN PHẨM NÀY
                                </h4>
                            </div>
                            `);
                    }
                }
            });
        }));
    }
}
$(document).ready(function () {
    $(document).on("click", ".option-group .nav-item", function () {
        Option.seleted(this);
    });
     $(document).on("click", ".option-group-entries .nav-item", function () {
        OptionEntries.seleted(this);
    });
    Content.detect();
    Filter.init();
    Sort.init();
    Paginator.init();
    ShoppingCart.init();
    //  Number
    Quantity.start();
    // Counter
    Counter.viewer();
    // Search Tern
    SearchTerms.search();
    // Rating
    Rating.init();

});